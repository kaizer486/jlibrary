<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get seller's listings
        $listings = $user->marketplaceListings()
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Stats
        $stats = [
            'total_listings' => $user->marketplaceListings()->count(),
            'pending_listings' => $user->marketplaceListings()->where('status', 'pending')->count(),
            'approved_listings' => $user->marketplaceListings()->where('status', 'approved')->count(),
            'total_sales' => $user->sellerOrders()->count(),
            'total_earnings' => $user->sellerOrders()->where('status', 'completed')->sum('seller_earnings'),
            'pending_orders' => $user->sellerOrders()->where('status', 'pending')->count(),
        ];
        
        // Recent sales
        $recentSales = $user->sellerOrders()
            ->with(['listing', 'buyer'])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Earnings data (use direct values from stats)
        $totalEarnings = $stats['total_earnings'];
        $pendingOrders = $stats['pending_orders'];
        
        return view('seller.dashboard', compact(
            'listings',
            'stats',
            'recentSales',
            'totalEarnings',
            'pendingOrders'
        ));
    }

    public function listings()
    {
        $listings = auth()->user()
            ->marketplaceListings()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.listings', compact('listings'));
    }

    public function orders()
    {
        $orders = auth()->user()
            ->sellerOrders()
            ->with(['listing', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.orders', compact('orders'));
    }

    public function earnings()
    {
        $user = auth()->user();
        
        $totalEarnings = $user->sellerOrders()->where('status', 'completed')->sum('seller_earnings');
        $pendingEarnings = $user->sellerOrders()->where('status', 'pending')->sum('seller_earnings');
        
        $monthlyEarnings = $user->sellerOrders()
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(seller_earnings) as total')
            ->groupBy('month')
            ->get();

        return view('seller.earnings', compact('totalEarnings', 'pendingEarnings', 'monthlyEarnings'));
    }
}