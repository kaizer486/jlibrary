<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Hardcopy orders placed by this user
        $hardcopyOrders = Order::where('user_id', $user->id)
            ->with(['book', 'author', 'institution'])
            ->latest()
            ->paginate(10, ['*'], 'hardcopy_page');

        // Softcopy purchases (completed Payments against a Book)
        $softcopyPurchases = \App\Models\Payment::where('user_id', $user->id)
            ->where('payable_type', 'App\\Models\\Book')
            ->where('status', 'completed')
            ->with('payable')
            ->latest()
            ->paginate(10, ['*'], 'softcopy_page');

        // Stats
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

    protected function notifyOwner(Book $book, array $owner, Order $order, $buyer): void
    {
        $title = 'New Hardcopy Order #' . $order->id;
        $body  = $buyer->full_name . ' ordered "' . $book->title
               . '" (x' . $order->quantity . ') for TSh ' . number_format($order->total, 2)
               . '. Phone: ' . $order->customer_phone;

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
            return;
        }

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
}