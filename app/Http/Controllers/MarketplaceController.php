<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    // Display all approved listings
    public function index()
    {
        $listings = MarketplaceListing::where('status', 'approved')
                                     ->with('seller')
                                     ->latest()
                                     ->paginate(12);
        
        return view('marketplace.index', compact('listings'));
    }
    
    // Show create listing form
    public function create()
    {
        return view('marketplace.create');
    }
    
    // Store new listing
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'book_file' => 'required|file|mimes:pdf|max:20480',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        // Upload book file
        $bookPath = $request->file('book_file')->store('marketplace/books', 'public');
        
        // Upload cover image if provided
        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('marketplace/covers', 'public');
        }
        
        // Create listing
        $listing = MarketplaceListing::create([
            'seller_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'book_file' => $bookPath,
            'cover_image' => $coverPath,
            'status' => 'pending'
        ]);
        
        return redirect()->route('marketplace.show', $listing)
                         ->with('success', 'Your book has been submitted for admin approval!');
    }
    
    // Show single listing
    public function show($id)
    {
        $listing = MarketplaceListing::with('seller')->findOrFail($id);
        
        // Increment views
        $listing->increment('views');
        
        // Check if current user is the seller
        $isSeller = Auth::check() && $listing->seller_id === Auth::id();
        
        return view('marketplace.show', compact('listing', 'isSeller'));
    }
    
    // Download book
    public function download($id)
    {
        $listing = MarketplaceListing::findOrFail($id);
        
        // Only approved listings can be downloaded
        if ($listing->status !== 'approved') {
            abort(404, 'This book is not yet available for download.');
        }
        
        // Increment downloads
        $listing->increment('downloads');
        
        $filePath = storage_path('app/public/' . $listing->book_file);
        
        if (!file_exists($filePath)) {
            abort(404, 'Book file not found.');
        }
        
        return response()->download($filePath, $listing->title . '.pdf');
    }
    
    // My listings (seller dashboard)
    public function myListings()
    {
        $listings = MarketplaceListing::where('seller_id', Auth::id())
                                     ->latest()
                                     ->get();
        
        return view('marketplace.my-listings', compact('listings'));
    }
    
    // Delete listing
    public function destroy($id)
    {
        $listing = MarketplaceListing::findOrFail($id);
        
        // Only seller can delete
        if ($listing->seller_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Delete files
        Storage::disk('public')->delete($listing->book_file);
        if ($listing->cover_image) {
            Storage::disk('public')->delete($listing->cover_image);
        }
        
        $listing->delete();
        
        return redirect()->route('marketplace.my-listings')
                         ->with('success', 'Listing deleted successfully.');
    }
}