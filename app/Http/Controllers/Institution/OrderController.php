<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Book;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display orders for the institution admin.
     */
    public function index()
    {
        $institution = auth()->user()->institution;
        
        $orders = Order::where('institution_id', $institution->id)
            ->with(['user', 'book'])
            ->latest()
            ->paginate(15);
        
        $stats = [
            'total' => Order::where('institution_id', $institution->id)->count(),
            'pending' => Order::where('institution_id', $institution->id)->where('status', 'pending')->count(),
            'processing' => Order::where('institution_id', $institution->id)->where('status', 'processing')->count(),
            'completed' => Order::where('institution_id', $institution->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('institution_id', $institution->id)->where('status', 'cancelled')->count(),
        ];
        
        return view('institution.orders.index', compact('orders', 'stats', 'institution'));
    }

    /**
     * Show a specific order.
     */
    public function show(Order $order)
    {
        $institution = auth()->user()->institution;
        
        if ($order->institution_id !== $institution->id) {
            abort(403);
        }
        
        return view('institution.orders.show', compact('order', 'institution'));
    }

    /**
     * Update order status (AJAX).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $institution = auth()->user()->institution;
        
        if ($order->institution_id !== $institution->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        
        $order->update(['status' => $request->status]);
        
        // Notify the user about order status update
        NotificationHelper::send(
            $order->user_id,
            'order_status_update',
            '📦 Order #' . $order->id . ' Updated',
            "Your order #{$order->id} status is now: " . ucfirst($request->status),
            [
                'order_id' => $order->id,
                'status' => $request->status,
                'link' => route('orders.show', $order->id)
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);
    }

    /**
     * Create a new order (from public book page).
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_type' => 'required|in:softcopy,hardcopy,both',
            'quantity' => 'required|integer|min:1',
            'shipping_address' => 'required_if:book_type,hardcopy|string|max:500',
            'payment_method' => 'nullable|string|max:100',
        ]);
        
        $user = auth()->user();
        $book = Book::findOrFail($request->book_id);
        
        // Check if user already owns this book
        if ($user->hasPurchasedBook($book->id)) {
            return redirect()->back()
                ->with('error', 'You already own this book.');
        }
        
        // Calculate price based on type
        $pricePerUnit = 0;
        if ($request->book_type === 'softcopy' && $book->softcopy_price) {
            $pricePerUnit = $book->softcopy_price;
        } elseif ($request->book_type === 'hardcopy' && $book->hardcopy_price) {
            $pricePerUnit = $book->hardcopy_price;
        } elseif ($request->book_type === 'both') {
            $pricePerUnit = ($book->softcopy_price ?? 0) + ($book->hardcopy_price ?? 0);
        }
        
        $total = $pricePerUnit * $request->quantity;
        
        // Create order
        $order = Order::create([
            'institution_id' => $book->institution_id,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'book_type' => $request->book_type,
            'quantity' => $request->quantity,
            'price_per_unit' => $pricePerUnit,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ]);
        
        // Send notification to institution admins
        $admins = User::where('institution_id', $book->institution_id)
            ->where('is_institution_admin', true)
            ->get();
        
        foreach ($admins as $admin) {
            NotificationHelper::send(
                $admin->id,
                'new_order',
                '🛒 New Order #' . $order->id,
                $user->full_name . ' ordered "' . $book->title . '" (' . $request->book_type . ') for TSh ' . number_format($total, 2),
                [
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'user_id' => $user->id,
                    'link' => route('institution.orders.show', $order->id)
                ]
            );
        }
        
        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order placed successfully! Awaiting confirmation.');
    }
}