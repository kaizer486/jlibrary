<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    // REMOVE THE INDEX METHOD - Regular users shouldn't see marketplace listings
    // Books should be viewed in the GENERAL LIBRARY

    /**
     * Show the form for creating a new listing
     */
    public function create()
    {
        $categories = MarketplaceCategory::all();
        return view('marketplace.create', compact('categories'));
    }

    /**
     * Store a newly created listing
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'book_type' => 'required|in:digital,physical,both',
            'category_id' => 'nullable|exists:marketplace_categories,id',
            'file' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
            'cover_image' => 'nullable|image|max:5120',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['seller_id'] = auth()->id();
        $data['status'] = 'pending'; // Needs admin approval

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('marketplace/books', 'public');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('marketplace/covers', 'public');
        }

        $listing = MarketplaceListing::create($data);

        return redirect()->route('seller.listings')
            ->with('success', 'Book uploaded successfully! Waiting for admin approval.');
    }

    /**
     * Show a specific listing (for editing or viewing by seller)
     */
    public function show(MarketplaceListing $listing)
    {
        // Only the seller or admin can view this
        if ($listing->seller_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view this listing.');
        }
        
        return view('marketplace.show', compact('listing'));
    }

    /**
     * Show the form for editing a listing
     */
    public function edit(MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }
        
        $categories = MarketplaceCategory::all();
        return view('marketplace.edit', compact('listing', 'categories'));
    }

    /**
     * Update a listing
     */
    public function update(Request $request, MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'book_type' => 'required|in:digital,physical,both',
            'category_id' => 'nullable|exists:marketplace_categories,id',
            'file' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
            'cover_image' => 'nullable|image|max:5120',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();

        if ($request->hasFile('file')) {
            if ($listing->file_path) {
                Storage::disk('public')->delete($listing->file_path);
            }
            $data['file_path'] = $request->file('file')->store('marketplace/books', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($listing->cover_image) {
                Storage::disk('public')->delete($listing->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('marketplace/covers', 'public');
        }

        if ($request->hasFile('file') || $request->hasFile('cover_image')) {
            $data['status'] = 'pending';
        }

        $listing->update($data);

        return redirect()->route('seller.listings')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * Delete a listing
     */
    public function destroy(MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }

        if ($listing->file_path) {
            Storage::disk('public')->delete($listing->file_path);
        }
        if ($listing->cover_image) {
            Storage::disk('public')->delete($listing->cover_image);
        }

        $listing->delete();

        return redirect()->route('seller.listings')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Get seller's listings (My Books)
     */
    public function myListings()
    {
        $listings = auth()->user()
            ->marketplaceListings()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('seller.listings', compact('listings'));
    }
}