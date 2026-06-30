<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LibraryController extends Controller
{
    /**
     * Display institution library (admin view).
     * NOW WORKS FOR ALL INSTITUTION TYPES!
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ REMOVED: type === 'library' check

        $query = Book::where('institution_id', $institution->id)
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('shelf')) {
            $query->where('shelf_number', $request->shelf);
        }

        $books = $query->latest()->paginate(15);

        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->withCount('books')
            ->get();

        $categories = Book::where('institution_id', $institution->id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('institution.library.index', compact(
            'institution',
            'books',
            'shelves',
            'categories'
        ));
    }

    public function show($bookId)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ REMOVED: type === 'library' check

        $book = Book::where('institution_id', $institution->id)
            ->findOrFail($bookId);

        $book->increment('views_count');

        $relatedBooks = Book::where('institution_id', $institution->id)
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

        return view('institution.library.show', compact(
            'institution',
            'book',
            'relatedBooks'
        ));
    }
}