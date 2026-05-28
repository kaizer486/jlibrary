<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function pending()
    {
        $listings = MarketplaceListing::where('status', 'pending')
                                     ->with('seller')
                                     ->latest()
                                     ->paginate(15);
        
        return view('admin.marketplace.pending', compact('listings'));
    }
    
    public function approve(MarketplaceListing $listing)
    {
        $listing->update(['status' => 'approved']);
        
        return redirect()->back()->with('success', 'Listing approved successfully!');
    }
    
    public function reject(Request $request, MarketplaceListing $listing)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);
        
        $listing->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes
        ]);
        
        return redirect()->back()->with('success', 'Listing rejected.');
    }
    
    public function all()
    {
        $listings = MarketplaceListing::with('seller')->latest()->paginate(15);
        return view('admin.marketplace.all', compact('listings'));
    }
}