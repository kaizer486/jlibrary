<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceListing;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = MarketplaceListing::with(['seller', 'institution']);
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by institution
        if ($request->institution_id && $request->institution_id !== 'all') {
            $query->where('institution_id', $request->institution_id);
        }
        
        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        $listings = $query->latest()->paginate(15);
        
        // Stats
        $totalListings = MarketplaceListing::count();
        $pendingListings = MarketplaceListing::where('status', 'pending')->count();
        $approvedListings = MarketplaceListing::where('status', 'approved')->count();
        $rejectedListings = MarketplaceListing::where('status', 'rejected')->count();
        $totalSales = MarketplaceListing::where('status', 'approved')->sum('price');
        
        $institutions = Institution::where('status', 'approved')->get();
        
        return view('super-admin.marketplace.index', compact(
            'listings', 'totalListings', 'pendingListings', 'approvedListings',
            'rejectedListings', 'totalSales', 'institutions'
        ));
    }
    
    public function show(MarketplaceListing $listing)
    {
        $listing->load(['seller', 'institution', 'reviews.user']);
        return view('super-admin.marketplace.show', compact('listing'));
    }
    
    public function approve(MarketplaceListing $listing)
    {
        $listing->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Listing approved successfully!');
    }
    
    public function reject(Request $request, MarketplaceListing $listing)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);
        
        $listing->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Listing rejected.');
    }
    
    public function destroy(MarketplaceListing $listing)
    {
        $listing->delete();
        
        return redirect()->route('super-admin.marketplace.index')->with('success', 'Listing deleted successfully!');
    }
}