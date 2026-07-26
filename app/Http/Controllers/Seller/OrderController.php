<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $listingIds = \App\Models\MarketplaceListing::where('seller_id', auth()->id())->pluck('id');
        
        $orders = MarketplaceOrder::whereIn('listing_id', $listingIds)
            ->with(['listing', 'buyer'])
            ->latest()
            ->paginate(20);
            
        return view('seller.orders', compact('orders'));
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