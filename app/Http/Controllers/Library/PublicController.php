<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Book;
use App\Models\Shelf;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    
    public function index(Request $request, $institutionId)
{
    // ✅ REMOVED: ->where('type', 'library')
    // Now works for ALL institutions
    $institution = Institution::where('status', 'approved')
        ->findOrFail($institutionId);

    // ✅ FIX: Get shelves with their books loaded
    $shelves = Shelf::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->withCount('books')
        ->with(['books' => function($q) {
            $q->select('id', 'title', 'cover_image', 'author', 'shelf_number', 'institution_id', 'status')
              ->where('status', 'approved')
              ->limit(10); // Limit to 10 books per shelf for display
        }])
        ->get();

    // Get books
    $query = Book::where('institution_id', $institution->id)
        ->where('status', 'approved');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('author', 'LIKE', "%{$search}%");
        });
    }

    // Filter by shelf
    if ($request->filled('shelf')) {
        $query->where('shelf_number', $request->shelf);
    }

    // Filter by category
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    $books = $query->latest()->paginate(12);

    // Get categories
    $categories = Book::where('institution_id', $institution->id)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category');

    // Get location
    $location = $institution->location ?? null;

    return view('institution.public.index', compact(
        'institution',
        'shelves',
        'books',
        'categories',
        'location'
    ));
}
/**
 * Display all books on a specific shelf (Public view).
 */
public function shelfShow($institutionId, $shelfId)
{
    $institution = Institution::where('status', 'approved')
        ->findOrFail($institutionId);

    $shelf = Shelf::where('institution_id', $institution->id)
        ->where('id', $shelfId)
        ->with(['books' => function($q) {
            $q->where('status', 'approved')
              ->select('id', 'title', 'cover_image', 'author', 'shelf_number', 'institution_id');
        }])
        ->firstOrFail();

    // Get books on this shelf (paginated for the show page)
    $books = Book::where('institution_id', $institution->id)
        ->where('shelf_number', $shelf->code)
        ->where('status', 'approved')
        ->latest()
        ->paginate(12);

    // Get all shelves for navigation
    $allShelves = Shelf::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->withCount('books')
        ->get();

    // Get categories for filter
    $categories = Book::where('institution_id', $institution->id)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category');

    return view('institution.public.shelf-show', compact(
        'institution',
        'shelf',
        'books',
        'allShelves',
        'categories'
    ));
}

    /**
     * Display a single book in the public library.
     * NOW WORKS FOR ALL INSTITUTION TYPES!
     */
    public function show($institutionId, $bookId)
    {
        // ✅ REMOVED: ->where('type', 'library')
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        $book = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->findOrFail($bookId);

        $book->increment('views_count');

        // Related books
        $relatedBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->where('id', '!=', $book->id)
            ->where(function($q) use ($book) {
                if ($book->category) {
                    $q->orWhere('category', $book->category);
                }
                if ($book->shelf_number) {
                    $q->orWhere('shelf_number', $book->shelf_number);
                }
            })
            ->limit(4)
            ->get();

        return view('institution.public.show', compact(
            'institution',
            'book',
            'relatedBooks'
        ));
    }
}