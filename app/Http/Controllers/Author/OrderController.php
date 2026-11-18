<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use App\Models\Payment;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List hardcopy orders for books uploaded by the authenticated author,
     * plus softcopy sales (via the `payments` morph).
     *
     * Two tabs share this view:
     *   - hardcopy orders  → orders.author_id = me
     *   - softcopy sales   → payments where payable_type = Book,
     *                        payable_id IN my-book-ids, status = completed
     */
    public function index(Request $request)
    {
        $authorId = Auth::id();

        // Only books this author owns (not institution books).
        $myBookIds = Book::where('uploaded_by', $authorId)
            ->whereNull('institution_id')
            ->pluck('id');

        // Hardcopy orders
        $orders = Order::where('author_id', $authorId)
            ->with(['user', 'book'])
            ->latest()
            ->paginate(15, ['*'], 'orders_page');

        // Softcopy sales (completed payments against my books)
        $softcopySales = $myBookIds->isNotEmpty()
            ? Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $myBookIds)
                ->with('user')
                ->latest()
                ->paginate(15, ['*'], 'sales_page')
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        // Stats
        $stats = [
            'total'       => Order::where('author_id', $authorId)->count(),
            'pending'     => Order::where('author_id', $authorId)->where('status', 'pending')->count(),
            'processing'  => Order::where('author_id', $authorId)->where('status', 'processing')->count(),
            'completed'   => Order::where('author_id', $authorId)->where('status', 'completed')->count(),
            'cancelled'   => Order::where('author_id', $authorId)->where('status', 'cancelled')->count(),
        ];

        // Quick sale totals for headline cards
        $hardcopyRevenue = Order::where('author_id', $authorId)
            ->whereIn('status', ['completed', 'processing'])
            ->sum('total');

        $softcopyRevenue = $myBookIds->isNotEmpty()
            ? Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $myBookIds)
                ->sum('amount')
            : 0;

        return view('author.orders.index', compact(
            'orders',
            'softcopySales',
            'stats',
            'hardcopyRevenue',
            'softcopyRevenue',
            'myBookIds'
        ));
    }

    /**
     * Show a single hardcopy order — only the owning author may view it.
     */
    public function show(Order $order)
    {
        if ((int) $order->author_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['user', 'book']);

        return view('author.orders.show', compact('order'));
    }

    /**
     * Update the status of a hardcopy order.
     * Same status vocabulary as institution/seller orders:
     *   pending | processing | completed | cancelled
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ((int) $order->author_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $previous = $order->status;

        DB::transaction(function () use ($order, $request, $previous) {
            // If the order is being cancelled from an active state,
            // restore stock on the book.
            if ($request->status === 'cancelled'
                && in_array($previous, ['pending', 'processing'], true)
                && $order->book) {
                $order->book->increment('stock_quantity', (int) $order->quantity);
            }

            // If coming OUT of cancelled back to active, re-deduct stock.
            if ($previous === 'cancelled'
                && in_array($request->status, ['pending', 'processing', 'completed'], true)
                && $order->book) {
                $available = (int) ($order->book->stock_quantity ?? 0);
                if ($available >= (int) $order->quantity) {
                    $order->book->decrement('stock_quantity', (int) $order->quantity);
                }
            }

            $order->update(['status' => $request->status]);
        });

        // Notify buyer
        try {
            NotificationHelper::send(
                $order->user_id,
                'order_status_update',
                '📦 Order #' . $order->id . ' Updated',
                'Your order #' . $order->id . ' status is now: ' . ucfirst($request->status),
                [
                    'order_id' => $order->id,
                    'status'   => $request->status,
                    'link'     => route('library.order.show', $order->id),
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('Failed to notify buyer on order status update: ' . $e->getMessage());
        }

        return back()->with('success', 'Order status updated to ' . ucfirst($request->status) . '.');
    }
    
    /**
     * Export the seller's hardcopy orders to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filename = 'my-orders-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new OrdersExport(all: false, status: $request->get('status')),
            $filename
        );
    }

    // ==========================================
    // PAYMENT CONFIRMATION — SELLER ACTIONS
    // ==========================================

    /**
     * Seller marks an order as "payment received".
     * Only allowed when the order is a contact-payment hardcopy order
     * and hasn't been paid yet.
     */
    public function markPaid(Request $request, Order $order)
    {
        if ((int) $order->author_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->canSellerMarkPaid()) {
            return back()->with('error', 'This order cannot be marked as paid (already paid, cancelled, or not a contact-payment order).');
        }

        try {
            $order->update([
                'payment_status'      => 'seller_marked_paid',
                'payment_received_at' => now(),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to mark order paid: ' . $e->getMessage());
            return back()->with('error', 'Could not update the payment status. Please try again.');
        }

        // Notify the buyer to confirm
        try {
            NotificationHelper::send(
                $order->user_id,
                'payment_marked_received',
                '💰 Payment Recorded for Order #' . $order->id,
                'The seller has marked your payment of TSh ' . number_format($order->total, 2)
                    . ' as received. Please confirm that you paid to complete the order.',
                [
                    'order_id' => $order->id,
                    'amount'   => (float) $order->total,
                    'link'     => route('library.order.show', $order->id),
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('Payment-marked notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment marked as received. The buyer has been notified to confirm.');
    }

    /**
     * Seller un-marks a payment they marked by mistake.
     * Only allowed if buyer hasn't confirmed yet.
     */
    public function unmarkPaid(Request $request, Order $order)
    {
        if ((int) $order->author_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->canSellerUnmarkPaid()) {
            return back()->with('error', 'Cannot unmark this payment. It has either already been confirmed by the buyer, or no payment was marked.');
        }

        $order->update([
            'payment_status'      => 'unpaid',
            'payment_received_at' => null,
        ]);

        // Notify the buyer that the seller reversed the claim
        try {
            NotificationHelper::send(
                $order->user_id,
                'payment_marked_reversed',
                '↩️ Payment Mark Reversed for Order #' . $order->id,
                'The seller has reversed the payment confirmation. If you have already paid, please contact the seller directly.',
                [
                    'order_id' => $order->id,
                    'link'     => route('library.order.show', $order->id),
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('Payment-unmark notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment mark reversed. The order is now back to unpaid.');
    }
}