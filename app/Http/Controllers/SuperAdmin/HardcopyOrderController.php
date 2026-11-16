<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HardcopyOrderController extends Controller
{
    /**
     * List all hardcopy orders across the platform.
     */
    public function index(Request $request)
    {
        $query = Order::whereIn('book_type', ['hardcopy', 'both']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('commission_status')) {
            $query->where('commission_status', $request->commission_status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id', $s)
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhereHas('book', fn($bq) => $bq->where('title', 'like', "%{$s}%"))
                  ->orWhereHas('user', fn($uq) => $uq->where('full_name', 'like', "%{$s}%"));
            });
        }

        $orders = $query->with(['book', 'user', 'author', 'institution'])
            ->latest()
            ->paginate(20, ['*'], 'orders_page');

        $allOrders = Order::whereIn('book_type', ['hardcopy', 'both']);

        $stats = [
            'total'      => (clone $allOrders)->count(),
            'pending'    => (clone $allOrders)->where('status', 'pending')->count(),
            'revenue'    => (clone $allOrders)->sum('total'),
            'commission' => (clone $allOrders)->sum('platform_commission'),
            'payouts'    => (clone $allOrders)
                                ->whereNotNull('platform_commission')
                                ->sum(DB::raw('total - platform_commission')),
            'unset'      => (clone $allOrders)->whereNull('platform_commission')->count(),
        ];

        return view('super-admin.hardcopy-orders.index', compact('orders', 'stats'));
    }

    /**
     * Show one order with commission editing form.
     */
    public function show(Order $order)
    {
        abort_unless(in_array($order->book_type, ['hardcopy', 'both']), 404);

        $order->load(['book', 'user', 'author', 'institution']);

        return view('super-admin.hardcopy-orders.show', compact('order'));
    }

    /**
     * Save negotiated commission. Super-admin only.
     */
    public function updateCommission(Request $request, Order $order)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403, 'Only super-admin can set commission.');
        abort_unless(in_array($order->book_type, ['hardcopy', 'both']), 404);

        $request->validate([
            'platform_commission' => 'required|numeric|min:0|max:' . $order->total,
            'commission_status'   => 'required|in:pending,negotiated,collected',
            'commission_notes'    => 'nullable|string|max:2000',
        ]);

        $order->update([
            'platform_commission' => $request->platform_commission,
            'commission_status'   => $request->commission_status,
            'commission_notes'    => $request->commission_notes,
        ]);

        // Notify the owner about the new commission
        try {
            $recipients = [];

            if ($order->author_id) {
                $recipients[] = $order->author_id;
            } elseif ($order->institution_id) {
                $admins = \App\Models\User::where('institution_id', $order->institution_id)
                    ->where('is_institution_admin', true)
                    ->pluck('id')
                    ->toArray();
                $recipients = $admins;
            }

            foreach ($recipients as $uid) {
                NotificationHelper::send(
                    $uid,
                    'order_commission_updated',
                    'Commission Updated for Order #' . $order->id,
                    'Platform commission set to TSh ' . number_format($order->platform_commission, 2)
                        . '. Your payout: TSh ' . number_format($order->ownerPayout(), 2),
                    [
                        'order_id'   => $order->id,
                        'commission' => $order->platform_commission,
                        'payout'     => $order->ownerPayout(),
                        'link'       => route('author.orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Commission notification failed: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            'Commission updated. Owner payout: TSh ' . number_format($order->ownerPayout(), 2)
        );
    }
    
    /**
     * Delete a hardcopy order (super-admin / admin intervention).
     * Restores stock and notifies both parties.
     */
    public function destroy(Order $order)
    {
        abort_unless(in_array($order->book_type, ['hardcopy', 'both']), 404);

        $orderId   = $order->id;
        $orderTotal = (float) $order->total;
        $buyerId   = $order->user_id;
        $authorId  = $order->author_id;
        $bookId    = $order->book_id;
        $quantity  = (int) $order->quantity;

        try {
            DB::transaction(function () use ($order, $bookId, $quantity) {
                // Restore stock on the book if it still exists
                $book = \App\Models\Book::find($bookId);
                if ($book) {
                    $book->increment('stock_quantity', $quantity);
                }

                $order->delete();
            });
        } catch (\Throwable $e) {
            \Log::error('Super-admin order delete failed: ' . $e->getMessage());
            return back()->with('error', 'Could not delete the order. Please try again.');
        }

        // Notify buyer
        try {
            NotificationHelper::send(
                $buyerId,
                'order_deleted',
                'Order #' . $orderId . ' Cancelled by Admin',
                'Your hardcopy order #' . $orderId . ' (TSh ' . number_format($orderTotal, 2) . ') was removed by the platform team.',
                ['order_id' => $orderId]
            );
        } catch (\Throwable $e) {
            \Log::warning('Buyer delete notification failed: ' . $e->getMessage());
        }

        // Notify seller
        if ($authorId) {
            try {
                NotificationHelper::send(
                    $authorId,
                    'order_deleted',
                    'Order #' . $orderId . ' Cancelled by Admin',
                    'Hardcopy order #' . $orderId . ' (TSh ' . number_format($orderTotal, 2) . ') was removed by the platform team. Stock has been restored.',
                    ['order_id' => $orderId, 'link' => route('author.orders.index')]
                );
            } catch (\Throwable $e) {
                \Log::warning('Seller delete notification failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('super-admin.hardcopy-orders.index')
            ->with('success', 'Order #' . $orderId . ' deleted. Stock restored. Both parties notified.');
    }
}