<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceCategory;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarketplaceController extends Controller
{
    /**
     * Show the form for creating a new listing.
     */
    public function create()
    {
        $categories = MarketplaceCategory::orderBy('name')->get();
        return view('marketplace.create', compact('categories'));
    }

    /**
     * Store a newly created listing.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:marketplace_categories,id',
            'cover_image' => 'nullable|image|max:5120',

            // Format & pricing
            'book_type'      => 'required|in:softcopy,hardcopy,both',
            'softcopy_price' => 'nullable|numeric|min:0|required_if:book_type,softcopy,both',
            'hardcopy_price' => 'nullable|numeric|min:0|required_if:book_type,hardcopy,both',
            'stock'          => 'nullable|integer|min:0|required_if:book_type,hardcopy,both',

            // Files
            'book_file' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
        ]);

        // ── Derive legacy fields from format fields ──
        $bookType  = $request->book_type;
        $softPrice = $request->filled('softcopy_price') ? (float) $request->softcopy_price : null;
        $hardPrice = $request->filled('hardcopy_price') ? (float) $request->hardcopy_price : null;
        $stockQty  = $request->filled('stock') ? (int) $request->stock : 0;

        $hasHard = in_array($bookType, ['hardcopy', 'both'], true)
            && $hardPrice !== null && $hardPrice > 0;

        // ── Handle file uploads ──
        $bookPath = null;
        if ($request->hasFile('book_file')) {
            $bookPath = $request->file('book_file')->store('marketplace/books', 'public');
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('marketplace/covers', 'public');
        }

        // ── Create listing ──
        MarketplaceListing::create([
            'seller_id'          => auth()->id(),
            'institution_id'     => auth()->user()->institution_id,
            'title'              => $request->title,
            'description'        => $request->description,
            'category_id'        => $request->category_id,
            'book_file'          => $bookPath,
            'cover_image'        => $coverPath,
            'status'             => 'pending',

            // New format fields
            'book_type'          => $bookType,
            'softcopy_price'     => $softPrice,
            'hardcopy_price'     => $hardPrice,
            'hardcopy_available' => $hasHard,
            'stock'              => $hasHard ? $stockQty : 0,

            // Legacy field (still used by other parts of the system)
            'price'              => $softPrice ?? $hardPrice ?? 0,
        ]);

        return redirect()->route('seller.listings')
            ->with('success', 'Listing submitted! Waiting for admin approval.');
    }

    /**
     * Show a specific listing (for the seller).
     */
    public function show(MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You do not have permission to view this listing.');
        }

        return view('marketplace.show', compact('listing'));
    }

    /**
     * Show the form for editing a listing.
     */
    public function edit(MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }

        $categories = MarketplaceCategory::orderBy('name')->get();
        return view('marketplace.edit', compact('listing', 'categories'));
    }

    /**
     * Update an existing listing.
     */
    public function update(Request $request, MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:marketplace_categories,id',
            'cover_image' => 'nullable|image|max:5120',

            'book_type'      => 'required|in:softcopy,hardcopy,both',
            'softcopy_price' => 'nullable|numeric|min:0|required_if:book_type,softcopy,both',
            'hardcopy_price' => 'nullable|numeric|min:0|required_if:book_type,hardcopy,both',
            'stock'          => 'nullable|integer|min:0|required_if:book_type,hardcopy,both',

            'book_file' => 'nullable|file|mimes:pdf,epub,mobi|max:51200',
        ]);

        $bookType  = $request->book_type;
        $softPrice = $request->filled('softcopy_price') ? (float) $request->softcopy_price : null;
        $hardPrice = $request->filled('hardcopy_price') ? (float) $request->hardcopy_price : null;
        $stockQty  = $request->filled('stock') ? (int) $request->stock : 0;

        $hasHard = in_array($bookType, ['hardcopy', 'both'], true)
            && $hardPrice !== null && $hardPrice > 0;

        $data = [
            'title'              => $request->title,
            'description'        => $request->description,
            'category_id'        => $request->category_id,
            'book_type'          => $bookType,
            'softcopy_price'     => $softPrice,
            'hardcopy_price'     => $hardPrice,
            'hardcopy_available' => $hasHard,
            'stock'              => $hasHard ? $stockQty : 0,
            'price'              => $softPrice ?? $hardPrice ?? 0,
        ];

        $fileChanged = false;

        if ($request->hasFile('book_file')) {
            if ($listing->book_file) {
                Storage::disk('public')->delete($listing->book_file);
            }
            $data['book_file'] = $request->file('book_file')->store('marketplace/books', 'public');
            $fileChanged = true;
        }

        if ($request->hasFile('cover_image')) {
            if ($listing->cover_image) {
                Storage::disk('public')->delete($listing->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('marketplace/covers', 'public');
            $fileChanged = true;
        }

        // Re-submit for approval if a new file/cover was uploaded
        if ($fileChanged) {
            $data['status'] = 'pending';
        }

        $listing->update($data);

        return redirect()->route('seller.listings')
            ->with('success', 'Listing updated successfully.');
    }

    /**
     * Delete a listing.
     */
    public function destroy(MarketplaceListing $listing)
    {
        if ($listing->seller_id !== auth()->id()) {
            abort(403, 'You do not own this listing.');
        }

        if ($listing->book_file) {
            Storage::disk('public')->delete($listing->book_file);
        }
        if ($listing->cover_image) {
            Storage::disk('public')->delete($listing->cover_image);
        }

        $listing->delete();

        return redirect()->route('seller.listings')
            ->with('success', 'Listing deleted successfully.');
    }

    /**
     * Seller's listings page.
     */
    public function myListings()
    {
        $listings = auth()->user()
            ->marketplaceListings()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketplace.listings', compact('listings'));
    }
}