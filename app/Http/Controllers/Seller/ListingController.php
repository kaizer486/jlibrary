<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        $listings = MarketplaceListing::where('seller_id', auth()->id())
            ->withCount(['orders' => function($q) {
                $q->where('status', 'completed');
            }])
            ->latest()
            ->paginate(20);
            
        return view('seller.listings', compact('listings'));
    }

    public function create()
    {
        return view('marketplace.create');
    }
}