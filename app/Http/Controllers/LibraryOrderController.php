<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;     
use App\Exports\MyOrdersExport;

class LibraryOrderController extends Controller
{
    /**
     * Create a hardcopy order for a book from the global library page.
     *
     * No online payment — the book owner is notified and will contact
     * the buyer (using the phone number on the order) to arrange
     * delivery and payment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'          => 'required|exists:books,id',
            'quantity'         => 'required|integer|min:1|max:50',
            'customer_name'    => 'required|string|max:120',
            'customer_phone'   => 'required|string|max:30',
            'shipping_address' => 'required|string|max:500',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        if (!$user) {
            return $this->respond($request, false, 'Please log in to place an order.', 401);
        }

        $book = Book::find($request->book_id);
        if (!$book) {
            return $this->respond($request, false, 'Book not found.', 404);
        }

        if (!$book->offersHardcopy()) {
            return $this->respond($request, false, 'This book is not available as a hardcopy.', 422);
        }

        $owner = $book->orderOwner();
        if (!$owner) {
            return $this->respond($request, false, 'This book has no owner to receive the order.', 422);
        }

        $quantity = (int) $request->quantity;
        if (!$book->hasStockFor($quantity)) {
            return $this->respond(
                $request,
                false,
                'Only ' . (int) ($book->stock_quantity ?? 0) . ' copies available right now.',
                422
            );
        }

        $unitPrice = $book->effectiveHardcopyPrice();
        $total     = round($unitPrice * $quantity, 2);

        try {
            $order = DB::transaction(function () use ($book, $user, $owner, $request, $quantity, $unitPrice, $total) {
                // Deduct stock
                $book->decrement('stock_quantity', $quantity);

                return Order::create([
                    'institution_id'   => $owner['type'] === 'institution' ? $owner['id'] : null,
                    'author_id'        => $owner['type'] === 'author' ? $owner['id'] : null,
                    'user_id'          => $user->id,
                    'book_id'          => $book->id,
                    'book_type'        => 'hardcopy',
                    'quantity'         => $quantity,
                    'price_per_unit'   => $unitPrice,
                    'total'            => $total,
                    'status'           => 'pending',
                    'payment_method'   => 'contact',
                    'payment_status'   => 'unpaid',
                    'transaction_id'   => null,
                    'shipping_address' => $request->shipping_address,
                    'customer_phone'   => $request->customer_phone,
                    'notes'            => trim(
                        'Name: ' . $request->customer_name
                        . ($request->filled('notes') ? ' | Notes: ' . $request->notes : '')
                    ),
                ]);
            });
        } catch (\Throwable $e) {
            \Log::error('Hardcopy order creation failed: ' . $e->getMessage());
            return $this->respond($request, false, 'Could not create the order. Please try again.', 500);
        }

        try {
            $this->notifyOwner($book, $owner, $order, $user);
        } catch (\Throwable $e) {
            \Log::warning('Order notification failed: ' . $e->getMessage());
        }

        return $this->respond(
            $request,
            true,
            'Order placed successfully! The book owner will contact you to confirm delivery.',
            201,
            [
                'order_id'     => $order->id,
                'redirect_url' => route('library.order.show', $order->id),
                'total'        => (float) $order->total,
            ]
        );
    }
    /**
     * Export the buyer's own order history to Excel.
     */
    public function exportExcel(Request $request)
    {
        $filename = 'my-orders-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new MyOrdersExport(), $filename);
    }
    /**
     * Process a bulk checkout (multiple books at once).
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:120',
            'customer_phone'   => 'required|string|max:30',
            'shipping_address' => 'required|string|max:500',
            'notes'            => 'nullable|string|max:1000',
            'items'            => 'required|json',
        ]);

        $items = json_decode($request->items, true);
        if (empty($items) || !is_array($items)) {
            return back()->with('error', 'Your cart is empty or invalid.');
        }

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to place an order.');
        }

        $bulkId = 'BULK-' . strtoupper(Str::random(8));
        $createdOrders = [];
        $errors = [];

        try {
            DB::transaction(function () use ($items, $user, $request, $bulkId, &$createdOrders, &$errors) {
                foreach ($items as $item) {
                    $book = Book::find($item['id']);
                    
                    if (!$book || !$book->offersHardcopy()) {
                        $errors[] = "Book ID {$item['id']} is no longer available.";
                        continue;
                    }

                    $qty = (int) $item['qty'];
                    if (!$book->hasStockFor($qty)) {
                        $errors[] = "\"{$book->title}\" only has " . ($book->stock_quantity ?? 0) . " copies left.";
                        continue;
                    }

                    $owner = $book->orderOwner();
                    if (!$owner) {
                        $errors[] = "\"{$book->title}\" has no owner to receive the order.";
                        continue;
                    }

                    $unitPrice = $book->effectiveHardcopyPrice();
                    $total     = round($unitPrice * $qty, 2);

                    // Deduct stock
                    $book->decrement('stock_quantity', $qty);

                    $createdOrders[] = Order::create([
                        'bulk_order_id'    => $bulkId,
                        'institution_id'   => $owner['type'] === 'institution' ? $owner['id'] : null,
                        'author_id'        => $owner['type'] === 'author' ? $owner['id'] : null,
                        'user_id'          => $user->id,
                        'book_id'          => $book->id,
                        'book_type'        => 'hardcopy',
                        'quantity'         => $qty,
                        'price_per_unit'   => $unitPrice,
                        'total'            => $total,
                        'status'           => 'pending',
                        'payment_method'   => 'contact',
                        'payment_status'   => 'unpaid',
                        'shipping_address' => $request->shipping_address,
                        'customer_phone'   => $request->customer_phone,
                        'notes'            => trim(
                            'Name: ' . $request->customer_name
                            . ($request->filled('notes') ? ' | Notes: ' . $request->notes : '')
                        ),
                    ]);
                }

                if (count($createdOrders) === 0) {
                    throw new \Exception('No valid items to order.');
                }
            });
        } catch (\Throwable $e) {
            \Log::error('Bulk order failed: ' . $e->getMessage());
            return back()->with('error', $e->getMessage() ?: 'Could not create the bulk order. Please try again.');
        }

        // Notify owners for each order
        foreach ($createdOrders as $order) {
            try {
                $book = $order->book;
                $owner = $book->orderOwner();
                $this->notifyOwner($book, $owner, $order, $user);
            } catch (\Throwable $e) {
                \Log::warning('Bulk order notification failed: ' . $e->getMessage());
            }
        }

        $successMsg = count($createdOrders) . ' order(s) placed successfully!';
        if (!empty($errors)) {
            $successMsg .= ' Note: ' . implode(' ', $errors);
        }

        return redirect()->route('library.orders.index')
            ->with('success', $successMsg);
    }

    /**
     * Buyer's view of their own order.
     */
    public function show(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['book', 'author', 'institution']);

        return view('library.order-show', compact('order'));
    }

    /**
     * Buyer's "My Orders" list — hardcopy orders + softcopy purchases.
     */
    public function index()
    {
        $user = Auth::user();

        $hardcopyOrders = Order::where('user_id', $user->id)
            ->with(['book', 'author', 'institution'])
            ->latest()
            ->paginate(10, ['*'], 'hardcopy_page');

        $softcopyPurchases = \App\Models\Payment::where('user_id', $user->id)
            ->where('payable_type', 'App\\Models\\Book')
            ->where('status', 'completed')
            ->with('payable')
            ->latest()
            ->paginate(10, ['*'], 'softcopy_page');

        $hardcopyCount = Order::where('user_id', $user->id)->count();
        $pendingCount  = Order::where('user_id', $user->id)->where('status', 'pending')->count();
        $softcopyCount = \App\Models\Payment::where('user_id', $user->id)
            ->where('payable_type', 'App\\Models\\Book')
            ->where('status', 'completed')
            ->count();

        return view('library.my-orders', compact(
            'hardcopyOrders',
            'softcopyPurchases',
            'hardcopyCount',
            'pendingCount',
            'softcopyCount'
        ));
    }

    // ==========================================
    // PAYMENT CONFIRMATION — BUYER ACTIONS
    // ==========================================

    /**
     * Buyer confirms: "Yes, I did pay the seller."
     */
    public function confirmPayment(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->canBuyerRespond()) {
            return back()->with('error', 'This order is not awaiting your confirmation.');
        }

        try {
            $order->update([
                'payment_status'                 => 'buyer_confirmed',
                'payment_confirmed_by_buyer_at'  => now(),
                'payment_dispute_reason'         => null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to confirm payment: ' . $e->getMessage());
            return back()->with('error', 'Could not confirm the payment. Please try again.');
        }

        // Notify the seller
        try {
            foreach ($this->getSellerRecipients($order) as $uid) {
                NotificationHelper::send(
                    $uid,
                    'payment_confirmed_by_buyer',
                    '✅ Payment Confirmed — Order #' . $order->id,
                    'The buyer has confirmed paying TSh ' . number_format($order->total, 2)
                        . '. This order is now fully settled.',
                    [
                        'order_id' => $order->id,
                        'amount'   => (float) $order->total,
                        'link'     => route('author.orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Buyer-confirm notification to seller failed: ' . $e->getMessage());
        }

        // Notify super-admins
        try {
            foreach (\App\Models\User::role('super_admin')->get() as $sa) {
                NotificationHelper::send(
                    $sa->id,
                    'payment_settled',
                    '💰 Payment Settled — Order #' . $order->id,
                    'Buyer confirmed payment of TSh ' . number_format($order->total, 2)
                        . ' for order #' . $order->id . '.',
                    [
                        'order_id' => $order->id,
                        'amount'   => (float) $order->total,
                        'link'     => route('super-admin.hardcopy-orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Payment-settled notification to super-admin failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment confirmed. The seller has been notified.');
    }

    /**
     * Buyer disputes: "No, I did not pay (or did not receive the correct book)."
     */
    public function disputePayment(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$order->canBuyerRespond()) {
            return back()->with('error', 'This order is not awaiting your confirmation.');
        }

        $request->validate([
            'reason' => 'required|string|min:5|max:1000',
        ]);

        try {
            $order->update([
                'payment_status'         => 'disputed',
                'payment_dispute_reason' => $request->reason,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Failed to log dispute: ' . $e->getMessage());
            return back()->with('error', 'Could not log the dispute. Please try again.');
        }

        // Notify the seller
        try {
            foreach ($this->getSellerRecipients($order) as $uid) {
                NotificationHelper::send(
                    $uid,
                    'payment_disputed',
                    '⚠️ Payment Disputed — Order #' . $order->id,
                    'The buyer has disputed your payment claim of TSh ' . number_format($order->total, 2)
                        . '. Reason: ' . $request->reason,
                    [
                        'order_id' => $order->id,
                        'amount'   => (float) $order->total,
                        'reason'   => $request->reason,
                        'link'     => route('author.orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Dispute notification to seller failed: ' . $e->getMessage());
        }

        // Notify super-admins (urgent)
        try {
            foreach (\App\Models\User::role('super_admin')->get() as $sa) {
                NotificationHelper::send(
                    $sa->id,
                    'payment_disputed',
                    '🚨 Payment Dispute — Order #' . $order->id,
                    'Buyer disputed payment of TSh ' . number_format($order->total, 2)
                        . ' for order #' . $order->id . '. Reason: ' . $request->reason,
                    [
                        'order_id' => $order->id,
                        'amount'   => (float) $order->total,
                        'reason'   => $request->reason,
                        'link'     => route('super-admin.hardcopy-orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Dispute notification to super-admin failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Dispute logged. The seller and super-admin have been notified.');
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Normalize AJAX vs non-AJAX response.
     */
    protected function respond(Request $request, bool $ok, string $message, int $status = 200, array $extra = [])
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge([
                'success' => $ok,
                'message' => $message,
            ], $extra), $status);
        }

        return back()->with($ok ? 'success' : 'error', $message);
    }

    /**
     * Get the user IDs that should be notified as "the seller"
     * (either the author, or all institution admins).
     */
    protected function getSellerRecipients(Order $order): array
    {
        if ($order->author_id) {
            return [$order->author_id];
        }

        if ($order->institution_id) {
            return \App\Models\User::where('institution_id', $order->institution_id)
                ->where('is_institution_admin', true)
                ->pluck('id')
                ->toArray();
        }

        return [];
    }

    /**
     * Notify the book owner + all super-admins when a new order is placed.
     */
    protected function notifyOwner(Book $book, array $owner, Order $order, $buyer): void
    {
        $title = 'New Hardcopy Order #' . $order->id;
        $body  = $buyer->full_name . ' ordered "' . $book->title
               . '" (x' . $order->quantity . ') for TSh ' . number_format($order->total, 2)
               . '. Phone: ' . $order->customer_phone;

        // ── Step 1: Notify the book OWNER ──
        if ($owner['type'] === 'author') {
            NotificationHelper::send(
                $owner['id'],
                'new_order',
                $title,
                $body,
                [
                    'order_id' => $order->id,
                    'book_id'  => $book->id,
                    'user_id'  => $buyer->id,
                    'link'     => route('author.orders.show', $order->id),
                ]
            );
        } else {
            $admins = \App\Models\User::where('institution_id', $owner['id'])
                ->where('is_institution_admin', true)
                ->get();

            foreach ($admins as $admin) {
                NotificationHelper::send(
                    $admin->id,
                    'new_order',
                    $title,
                    $body,
                    [
                        'order_id' => $order->id,
                        'book_id'  => $book->id,
                        'user_id'  => $buyer->id,
                        'link'     => route('orders.show', $order->id),
                    ]
                );
            }
        }

        // ── Step 2: Notify super-admins so they can oversee / negotiate commission ──
        try {
            foreach (\App\Models\User::role('super_admin')->get() as $sa) {
                NotificationHelper::send(
                    $sa->id,
                    'new_hardcopy_order',
                    'New Hardcopy Order #' . $order->id,
                    'Buyer ' . $buyer->full_name . ' ordered "' . $book->title
                        . '" (x' . $order->quantity . ') for TSh ' . number_format($order->total, 2)
                        . '. Contact the owner to negotiate commission.',
                    [
                        'order_id' => $order->id,
                        'book_id'  => $book->id,
                        'buyer_id' => $buyer->id,
                        'link'     => route('super-admin.hardcopy-orders.show', $order->id),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Super-admin notification failed: ' . $e->getMessage());
        }
    }
}