<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Unified orders page.
     *   Tab 1 → Product (Marketplace) Orders  ← $orders
     *   Tab 2 → Book (Hardcopy) Orders         ← $bookOrders
     */
    public function index()
    {
        $sellerId = auth()->id();

        // ── Product (marketplace) orders — UNCHANGED LOGIC ──
        $listingIds = \App\Models\MarketplaceListing::where('seller_id', $sellerId)->pluck('id');

        $orders = MarketplaceOrder::whereIn('listing_id', $listingIds)
            ->with(['listing', 'buyer'])
            ->latest()
            ->paginate(20, ['*'], 'products_page');

        // ── Book (hardcopy) orders — NEW ──
        $bookOrders = Order::where('author_id', $sellerId)
            ->with(['user', 'book'])
            ->latest()
            ->paginate(20, ['*'], 'books_page');

        // ── Badge counts (for tabs + sidebar) ──
        $pendingProducts = MarketplaceOrder::whereIn('listing_id', $listingIds)
            ->where('status', 'pending')
            ->count();

        $pendingBooks = Order::where('author_id', $sellerId)
            ->where('status', 'pending')
            ->count();

        return view('seller.orders', compact(
            'orders',
            'bookOrders',
            'pendingProducts',
            'pendingBooks'
        ));
    }

    public function show($id)
    {
        $order = MarketplaceOrder::with(['listing', 'buyer'])->findOrFail($id);

        // Verify seller owns this order
        if ($order->listing->seller_id !== auth()->id()) {
            abort(403);
        }

        return view('seller.order-show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = MarketplaceOrder::findOrFail($id);

        if ($order->listing->seller_id !== auth()->id()) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:pending,processing,completed,cancelled']);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }
}