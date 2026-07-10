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
        
        // Total earnings
        $totalEarnings = $user->sellerOrders()->where('status', 'completed')->sum('seller_earnings');
        
        // Pending earnings
        $pendingEarnings = $user->sellerOrders()->where('status', 'pending')->sum('seller_earnings');
        
        // Monthly earnings (current month)
        $monthlyEarnings = $user->sellerOrders()
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('seller_earnings');
        
        // Monthly data for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = $user->sellerOrders()
                ->where('status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('seller_earnings');
            
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'total' => $total,
                'percentage' => $totalEarnings > 0 ? round(($total / $totalEarnings) * 100, 1) : 0
            ];
        }
        
        // Recent transactions
        $transactions = $user->sellerOrders()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($order) {
                return (object)[
                    'description' => 'Order #' . $order->id . ' - ' . ($order->listing->title ?? 'Book Sale'),
                    'amount' => $order->seller_earnings ?? $order->amount ?? 0,
                    'type' => 'credit',
                    'created_at' => $order->created_at,
                    'order_id' => $order->id
                ];
            });

        return view('seller.earnings', compact(
            'totalEarnings',
            'pendingEarnings',
            'monthlyEarnings',
            'monthlyData',
            'transactions'
        ));
    }
}