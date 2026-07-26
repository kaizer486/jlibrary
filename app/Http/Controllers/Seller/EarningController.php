<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get seller earnings from marketplace orders
        $listingIds = \App\Models\MarketplaceListing::where('seller_id', $user->id)->pluck('id');
        
        $totalEarnings = \App\Models\MarketplaceOrder::whereIn('listing_id', $listingIds)
            ->where('status', 'completed')
            ->sum('seller_earnings');
            
        $monthlyEarnings = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyEarnings[] = \App\Models\MarketplaceOrder::whereIn('listing_id', $listingIds)
                ->where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('seller_earnings');
        }
        
        $recentEarnings = \App\Models\MarketplaceOrder::whereIn('listing_id', $listingIds)
            ->where('status', 'completed')
            ->with('listing', 'buyer')
            ->latest()
            ->limit(20)
            ->get();
        
        return view('seller.earnings', compact('totalEarnings', 'monthlyEarnings', 'recentEarnings'));
    }
}