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

     */
   public function index(Request $request)
{
    $institution = auth()->user()->institution;

    if (!$institution) {
        abort(403, 'You do not belong to any institution.');
    }

    // ✅ Get books from BOTH tables for this institution
    $regularBooks = Book::where('institution_id', $institution->id)
        ->where('status', 'approved')
        ->get(['id', 'title', 'author', 'category', 'cover_image', 'status', 'created_at', 'institution_id', 'shelf_number']);

    $bookstoreBooks = BookshopBook::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->get(['id', 'title', 'author', 'category', 'cover_image', 'status', 'created_at', 'institution_id', 'shelf_number']);

    // ✅ Merge collections
    $allBooks = $regularBooks->merge($bookstoreBooks);

    // ✅ Apply filters
    if ($request->filled('search')) {
        $search = $request->search;
        $allBooks = $allBooks->filter(function($book) use ($search) {
            return stripos($book->title, $search) !== false || 
                   stripos($book->author, $search) !== false;
        });
    }

    if ($request->filled('shelf')) {
        $allBooks = $allBooks->filter(function($book) use ($request) {
            return $book->shelf_number == $request->shelf;
        });
    }

    if ($request->filled('category')) {
        $allBooks = $allBooks->filter(function($book) use ($request) {
            return $book->category == $request->category;
        });
    }

    // ✅ Sort by created_at
    $allBooks = $allBooks->sortByDesc('created_at');

    // ✅ Paginate
    $books = new \Illuminate\Pagination\LengthAwarePaginator(
        $allBooks->forPage($request->get('page', 1), 15),
        $allBooks->count(),
        15,
        $request->get('page', 1),
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // ✅ Get shelves
    $shelves = Shelf::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->get();

    // ✅ Add book count to each shelf
    foreach ($shelves as $shelf) {
        $count = Book::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', 'approved')
            ->count();
        
        $count += BookshopBook::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', 'active')
            ->count();
        
        $shelf->books_count = $count;
    }

    // ✅ Get categories from BOTH tables
    $categories1 = Book::where('institution_id', $institution->id)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category')
        ->toArray();

    $categories2 = BookshopBook::where('institution_id', $institution->id)
        ->whereNotNull('category')
        ->distinct()
        ->pluck('category')
        ->toArray();

    $categories = collect(array_unique(array_merge($categories1, $categories2)));

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

    // ✅ Try to find the book in BOTH tables
    $book = Book::where('institution_id', $institution->id)
        ->where('status', 'approved')
        ->find($bookId);

    if (!$book) {
        $book = BookshopBook::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->find($bookId);
    }

    if (!$book) {
        abort(404, 'Book not found.');
    }

    // ✅ Increment views if column exists
    if (Schema::hasColumn($book->getTable(), 'views_count')) {
        $book->increment('views_count');
    }

    // ✅ Get related books from BOTH tables
    $relatedBooks = collect();

    // Get related books from books table
    $related1 = Book::where('institution_id', $institution->id)
        ->where('status', 'approved')
        ->where('id', '!=', $book->id)
        ->where(function($q) use ($book) {
            if (isset($book->category) && $book->category) {
                $q->orWhere('category', $book->category);
            }
            if (isset($book->shelf_number) && $book->shelf_number) {
                $q->orWhere('shelf_number', $book->shelf_number);
            }
        })
        ->limit(2)
        ->get();

    // Get related books from bookshop_books table
    $related2 = BookshopBook::where('institution_id', $institution->id)
        ->where('status', 'active')
        ->where('id', '!=', $book->id)
        ->where(function($q) use ($book) {
            if (isset($book->category) && $book->category) {
                $q->orWhere('category', $book->category);
            }
            if (isset($book->shelf_number) && $book->shelf_number) {
                $q->orWhere('shelf_number', $book->shelf_number);
            }
        })
        ->limit(2)
        ->get();

    $relatedBooks = $related1->merge($related2)->take(4);

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

    // ✅ Get books from BOTH tables
    $regularBooks = Book::where('institution_id', $institution->id)
        ->where('shelf_number', $shelf->code)
        ->where('status', 'approved')
        ->get(['id', 'title', 'author', 'cover_image']);

    $bookstoreBooks = BookshopBook::where('institution_id', $institution->id)
        ->where('shelf_number', $shelf->code)
        ->where('status', 'active')
        ->get(['id', 'title', 'author', 'cover_image']);

    $books = $regularBooks->merge($bookstoreBooks);

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