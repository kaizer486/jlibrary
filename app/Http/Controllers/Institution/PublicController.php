<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Book;
use App\Models\Shelf;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display public library page for an institution.
     * NOW WORKS FOR ALL INSTITUTION TYPES!
     */
    public function index(Request $request, $institutionId)
    {
        // Get the institution
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        // Get shelves
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->withCount('books')
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
     * Display a single book in the public library.
     * NOW WORKS FOR ALL INSTITUTION TYPES!
     */
    public function show($institutionId, $bookId)
    {
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