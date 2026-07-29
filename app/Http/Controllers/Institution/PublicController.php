<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Book;
use App\Models\BookshopBook; 
use App\Models\Shelf;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    
    public function index(Request $request, $institutionId)
    {
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Get shelves with books loaded
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        // ✅ For each shelf, load its books manually
        foreach ($shelves as $shelf) {
            // Load books for this shelf using the correct model
            $shelf->books = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->limit(10)
                ->get();
            
            // Set books_count
            $shelf->books_count = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->count();
        }

        // ✅ Get books using the correct model
        $query = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition);

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
        $categories = $bookModel::where('institution_id', $institution->id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

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
     * Display all books on a specific shelf.
     */
    public function shelfShow($institutionId, $shelfId)
    {
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        $shelf = Shelf::where('institution_id', $institution->id)
            ->where('id', $shelfId)
            ->firstOrFail();

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Get books on this shelf
        $books = $bookModel::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', $statusCondition)
            ->latest()
            ->paginate(12);

        return view('institution.public.shelf-show', compact(
            'institution',
            'shelf',
            'books'
        ));
    }

    /**
     * Display a single book in the public library.
     * NOW WORKS FOR ALL INSTITUTION TYPES!
     */
    public function show($institutionId, $bookId)
    {
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Get the book using the correct model
        $book = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->findOrFail($bookId);

        // ✅ Increment views (if column exists)
        if (isset($book->views_count)) {
            $book->increment('views_count');
        }

        // ✅ Related books using the correct model
        $relatedBooks = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->where('id', '!=', $book->id)
            ->where(function($q) use ($book) {
                if (isset($book->category)) {
                    $q->orWhere('category', $book->category);
                }
                if (isset($book->shelf_number)) {
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