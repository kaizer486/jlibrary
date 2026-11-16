<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        // ── Build the filtered query ──
        $query = MarketplaceListing::where('seller_id', auth()->id());

        // Search filter (title + description)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ── Paginate with counts + revenue sums ──
        $listings = $query
            ->withCount(['orders' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['orders as seller_revenue' => function ($q) {
                $q->where('status', 'completed');
            }], 'seller_earnings')
            ->latest()
            ->paginate(20)
            ->appends($request->query());

        // ── Stats (across ALL of this seller's listings, not filtered) ──
        $baseQuery = MarketplaceListing::where('seller_id', auth()->id());

        $stats = [
            'total'    => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'pending'  => (clone $baseQuery)->where('status', 'pending')->count(),
            'sales'    => MarketplaceOrder::whereIn(
                                'listing_id',
                                (clone $baseQuery)->pluck('id')
                            )
                            ->where('status', 'completed')
                            ->count(),
        ];

        return view('seller.listings', compact('listings', 'stats'));
    }

    public function create()
    {
        return view('marketplace.create');
    }
}