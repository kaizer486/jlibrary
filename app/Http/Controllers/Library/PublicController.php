<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\Shelf;
use App\Models\Certificate;
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

        // ✅ Get shelves
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        // ✅ For each shelf, load books manually
        foreach ($shelves as $shelf) {
            $shelf->books = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->limit(10)
                ->get();
            
            $shelf->books_count = $bookModel::where('institution_id', $institution->id)
                ->where('shelf_number', $shelf->code)
                ->where('status', $statusCondition)
                ->count();
        }

        // ✅ Get books using the correct model - ONLY from this institution
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
     * Display all books on a specific shelf (Public view).
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

        // ✅ Filter by shelf_number for BOTH bookstore and regular libraries
        $books = $bookModel::where('institution_id', $institution->id)
            ->where('shelf_number', $shelf->code)
            ->where('status', $statusCondition)
            ->latest()
            ->paginate(12);

        // Get all shelves for navigation
        $allShelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->withCount('books')
            ->get();

        // Get categories for filter
        $categories = $bookModel::where('institution_id', $institution->id)
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
 * ✅ FIXED: Properly finds books from both tables
 */
/**
 * Display a single book in the public library.
 */
public function show($institutionId, $bookId)
{
    // ✅ Get the institution
    $institution = Institution::where('status', 'approved')
        ->findOrFail($institutionId);

    // ✅ Try to find the book in Book model first (regular library)
    $book = Book::where('id', $bookId)
        ->where(function($query) use ($institutionId) {
            $query->where('institution_id', $institutionId)
                  ->orWhereNull('institution_id');
        })
        ->where('status', 'approved')
        ->first();

    // ✅ If not found in Book, try BookshopBook (bookstore)
    $isBookstore = false;
    if (!$book) {
        $book = BookshopBook::where('id', $bookId)
            ->where(function($query) use ($institutionId) {
                $query->where('institution_id', $institutionId)
                      ->orWhereNull('institution_id');
            })
            ->where('status', 'active')
            ->first();
        $isBookstore = true;
    }

    // ✅ If still not found, abort
    if (!$book) {
        abort(404, 'Book not found.');
    }

    // ✅ If book has NO institution, redirect to global library show
    if (!$book->institution_id) {
        return redirect()->route('library.show', $book->id);
    }

    // ✅ If book belongs to a different institution, redirect
    if ($book->institution_id != $institution->id) {
        return redirect()->route('institution.public.show', [
            'institutionId' => $book->institution_id,
            'book' => $book->id
        ]);
    }

    // ✅ Only increment views for regular books (not bookstore)
    if (!$isBookstore && method_exists($book, 'increment')) {
        $book->increment('views_count');
    }

    // ✅ Check if user has certificate for this book
    $hasCertificate = false;
    $certificate = null;
    if (auth()->check()) {
        $certificate = Certificate::where('user_id', auth()->id())
            ->where('book_id', $book->id)
            ->first();
        $hasCertificate = !is_null($certificate);
    }

    // ✅ Get related books - only for regular books
    $relatedBooks = collect();
    if (!$isBookstore) {
        $relatedBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->where('id', '!=', $book->id)
            ->where(function($q) use ($book) {
                if ($book->category) {
                    $q->orWhere('category', $book->category);
                }
            })
            ->limit(4)
            ->get();
        
        // If not enough related books, get from global
        if ($relatedBooks->count() < 4) {
            $globalBooks = Book::whereNull('institution_id')
                ->where('status', 'approved')
                ->where('id', '!=', $book->id)
                ->where(function($q) use ($book) {
                    if ($book->category) {
                        $q->orWhere('category', $book->category);
                    }
                })
                ->limit(4 - $relatedBooks->count())
                ->get();
            $relatedBooks = $relatedBooks->merge($globalBooks);
        }
    }

    // ✅ Check if user has access
    $hasAccess = false;
    if (auth()->check()) {
        if ($isBookstore) {
            $hasAccess = !$book->is_paid || $book->userHasAccess(auth()->id());
        } else {
            $hasAccess = !$book->is_paid || $book->userHasAccess(auth()->id());
        }
    }

    // ✅ Get reading progress (only for regular books)
    $progress = null;
    if (auth()->check() && !$isBookstore) {
        $progress = auth()->user()->books()->where('book_id', $book->id)->first();
    }

    // ✅ For bookstore books, set default values WITHOUT modifying the model
    $ratingsCount = 0;
    $reviewsCount = 0;
    $bookmarksCount = 0;

    if (!$isBookstore) {
        // Regular books - load counts properly
        $book->loadCount(['ratings', 'reviews', 'bookmarks']);
        $ratingsCount = $book->ratings_count ?? 0;
        $reviewsCount = $book->reviews_count ?? 0;
        $bookmarksCount = $book->bookmarks_count ?? 0;
    }

    // ✅ Pass counts to view separately
    return view('institution.public.show', compact(
        'institution',
        'book',
        'relatedBooks',
        'hasCertificate',
        'certificate',
        'hasAccess',
        'progress',
        'isBookstore',
        'ratingsCount',
        'reviewsCount',
        'bookmarksCount'
    ));
}
    /**
     * Get user's progress for a book (AJAX).
     */
    public function getProgress($institutionId, $bookId)
    {
        if (!auth()->check()) {
            return response()->json(['progress' => 0, 'authenticated' => false]);
        }

        $user = auth()->user();
        $progress = $user->books()->where('book_id', $bookId)->first();

        return response()->json([
            'authenticated' => true,
            'progress' => $progress ? $progress->pivot->progress_percent : 0,
            'status' => $progress ? $progress->pivot->status : 'not_started',
        ]);
    }

    /**
     * Update book reading progress (AJAX).
     */
    public function updateProgress(Request $request, $institutionId, $bookId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        // ✅ Find the book in either table
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $user = auth()->user();
        $progress = $request->input('progress');

        // Update or create pivot record
        $user->books()->syncWithoutDetaching([
            $book->id => [
                'progress_percent' => $progress,
                'status' => $progress >= 100 ? 'completed' : 'reading',
                'last_read_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $response = [
            'success' => true,
            'message' => 'Progress updated successfully',
            'progress' => $progress,
            'completed' => $progress >= 100,
        ];

        // Auto-generate certificate if book is completed (100%)
        if ($progress >= 100) {
            $existing = Certificate::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();

            if (!$existing) {
                $certificateController = new \App\Http\Controllers\CertificateController();
                $certificate = $certificateController->generateFromBook($book, 100);

                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(
                        new \App\Mail\CertificateEarnedMail(
                            $user->full_name,
                            $book->title,
                            100,
                            70
                        )
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send certificate email: ' . $e->getMessage());
                }

                $response['certificate_earned'] = true;
                $response['certificate_id'] = $certificate->id;
                $response['message'] = '🎉 Congratulations! You earned a certificate for completing this book!';
                $response['redirect'] = route('certificates.show', $certificate);
            } else {
                $response['certificate_earned'] = false;
                $response['message'] = 'Book completed! You already have a certificate for this book.';
            }
        }

        return response()->json($response);
    }
}