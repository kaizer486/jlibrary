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

    // ✅ DEBUG: Log shelves count
    \Log::info('Total shelves: ' . $shelves->count());

    // ✅ For each shelf, load books manually
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
        
        // ✅ DEBUG: Log each shelf's book count
        \Log::info('Shelf ' . $shelf->code . ' has ' . $shelf->books_count . ' books');
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

    // ✅ Now filter by shelf_number for BOTH bookstore and regular libraries
    $books = $bookModel::where('institution_id', $institution->id)
        ->where('shelf_number', $shelf->code)  // ✅ This will now work
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
     */
    public function show($institutionId, $bookId)
    {
        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        // ✅ Determine which model to use
        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        // ✅ For bookstore, use ONLY columns that exist in bookshop_books
        if ($institution->type === 'bookstore') {
            $book = $bookModel::where('institution_id', $institution->id)
                ->where('status', $statusCondition)
                ->select([
                    'id', 'title', 'author', 'category', 'description',
                    'cover_image', 'price',
                    'pages as total_pages',
                    'stock_quantity',
                    'sold_count as downloads',
                    'institution_id', 'status',
                    'isbn', 'publisher', 'publication_year'
                ])
                ->findOrFail($bookId);
            
            // ✅ Bookstore books don't have views_count, skip increment
        } else {
            // Regular book - includes all columns
            $book = $bookModel::where('institution_id', $institution->id)
                ->where('status', $statusCondition)
                ->select([
                    'id', 'title', 'author', 'category', 'description',
                    'cover_image', 'file_path', 'is_paid', 'price',
                    'total_pages', 'downloads', 'views_count',
                    'shelf_number', 'shelf_name', 'column_location',
                    'position', 'floor', 'section',
                    'is_bookstore_item', 'book_type',
                    'softcopy_price', 'hardcopy_price', 'stock_quantity',
                    'institution_id', 'status',
                ])
                ->findOrFail($bookId);
            
            // ✅ Only regular books have views_count
            $book->increment('views_count');
        }

        // Check if user has certificate for this book
        $hasCertificate = false;
        $certificate = null;
        if (auth()->check()) {
            $certificate = Certificate::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->first();
            $hasCertificate = !is_null($certificate);
        }

        // ✅ Related books using the correct model
        $relatedBooks = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->where('id', '!=', $book->id)
            ->where(function($q) use ($book) {
                if ($book->category) {
                    $q->orWhere('category', $book->category);
                }
            })
            ->limit(4)
            ->get();

        return view('institution.public.show', compact(
            'institution',
            'book',
            'relatedBooks',
            'hasCertificate',
            'certificate'
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

        $institution = Institution::where('status', 'approved')
            ->findOrFail($institutionId);

        $bookModel = $institution->type === 'bookstore' ? BookshopBook::class : Book::class;
        $statusCondition = $institution->type === 'bookstore' ? 'active' : 'approved';

        $book = $bookModel::where('institution_id', $institution->id)
            ->where('status', $statusCondition)
            ->findOrFail($bookId);

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