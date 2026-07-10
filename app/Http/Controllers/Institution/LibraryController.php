<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookshopBook;
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

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Build query using the correct model
        $query = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition);

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

        // ✅ Get shelves
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        // ✅ Add book count to each shelf using the correct model
        foreach ($shelves as $shelf) {
            $shelf->books_count = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->count();
        }

        // ✅ Get categories from the correct model
        $categories = $bookModel::where('institution_id', $institution->id)
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

    /**
     * Display a single book (admin view).
     */
    public function show($bookId)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ Get the book using the correct model
        $book = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->findOrFail($bookId);

        // ✅ Increment views if column exists
        if (Schema::hasColumn($book->getTable(), 'views_count')) {
            $book->increment('views_count');
        }

        // ✅ Get related books using the correct model
        $relatedBooks = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->where('id', '!=', $book->id)
            ->where(function($q) use ($book) {
                if (isset($book->category) && $book->category) {
                    $q->orWhere('category', $book->category);
                }
                if (isset($book->shelf_number) && $book->shelf_number) {
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

    /**
     * Get books for a specific shelf (AJAX).
     */
    public function getShelfBooks(Request $request, $shelfId)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            return response()->json(['error' => 'No institution found'], 403);
        }

        $shelf = Shelf::where('institution_id', $institution->id)
            ->where('id', $shelfId)
            ->firstOrFail();

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        $books = $bookModel::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', $statusCondition)
            ->get(['id', 'title', 'author', 'cover_image']);

        return response()->json([
            'shelf' => $shelf->name,
            'books' => $books
        ]);
    }

    /**
     * Get library stats (AJAX).
     */
    public function getStats()
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            return response()->json(['error' => 'No institution found'], 403);
        }

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        $stats = [
            'total_books' => $bookModel::where('institution_id', $institution->id)
                ->where('status', $statusCondition)
                ->count(),
            'total_shelves' => Shelf::where('institution_id', $institution->id)
                ->where('status', 'active')
                ->count(),
            'total_categories' => $bookModel::where('institution_id', $institution->id)
                ->whereNotNull('category')
                ->distinct()
                ->count('category'),
        ];

        return response()->json($stats);
    }
}