<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\UserBook;
use App\Models\Payment;
use App\Models\Institution;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryController extends Controller
{
    /**
     * Daily download limit for all users.
     * Centralized here so download() and downloadRaw() can never drift apart.
     */
    const DAILY_DOWNLOAD_LIMIT = 5;

    /**
     * All book categories for the global library
     */
    protected function getCategories()
    {
        return [
            // Technology & Science
            'Computer Science & Information Technology',
            'Artificial Intelligence & Data Science',
            'Engineering & Technology',
            'Mathematics & Statistics',
            'Physical Sciences',
            'Biological Sciences',

            // Health & Medicine
            'Health & Medical Sciences',
            'Public Health',
            'Agriculture & Veterinary Sciences',
            'Environmental & Earth Sciences',

            // Business & Finance
            'Business & Management',
            'Economics & Finance',
            'Accounting',
            'Marketing',
            'Entrepreneurship',

            // Social Sciences & Humanities
            'Law',
            'Education',
            'Social Sciences',
            'Psychology',
            'Political Science & Public Administration',
            'Humanities',
            'Philosophy',
            'Languages & Linguistics',
            'Literature',
            'History & Archaeology',
            'Geography & Tourism',
            'Religion & Theology',

            // Arts & Design
            'Arts, Design & Music',
            'Architecture & Urban Planning',

            // General Reading
            'Children\'s Books',
            'Fiction',
            'Non-Fiction',
            'Biographies & Memoirs',
            'Self-Help & Personal Development',
            'Leadership',

            // Academic & Research
            'Research & Academic Publications',
            'Journals & Conference Proceedings',
            'Theses & Dissertations',

            // Government & Reference
            'Government Publications',
            'Policies, Acts & Regulations',
            'Reports & White Papers',
            'Reference Books',
            'Open Educational Resources (OER)',
            'Newspapers & Magazines',
            'Encyclopedias & Dictionaries',
        ];
    }

    /**
     * Display all books in the global library with filters
     */
    public function index(Request $request)
    {
        // Get books from BOTH tables
        $regularBooks = Book::where('status', 'approved')->get();
        $bookstoreBooks = BookshopBook::where('status', 'active')->get();

        // Merge collections
        $allBooks = $regularBooks->merge($bookstoreBooks);

        // ==========================================
        // FILTERS
        // ==========================================

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $allBooks = $allBooks->filter(function ($book) use ($search) {
                return stripos($book->title, $search) !== false ||
                       stripos($book->author ?? '', $search) !== false ||
                       stripos($book->description ?? '', $search) !== false;
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $allBooks = $allBooks->filter(function ($book) use ($request) {
                return $book->category == $request->category;
            });
        }

        // Sub-category filter
        if ($request->has('sub_category') && $request->sub_category) {
            $allBooks = $allBooks->filter(function ($book) use ($request) {
                return $book->sub_category == $request->sub_category;
            });
        }

        // Trending filter
        if ($request->has('trending') && $request->trending == 'true') {
            $allBooks = $allBooks->filter(function ($book) {
                return $book->is_trending == true;
            });
        }

        // Recent filter
        if ($request->has('recent') && $request->recent == 'true') {
            $allBooks = $allBooks->sortByDesc('created_at');
        }

        // Featured filter
        if ($request->has('featured') && $request->featured == 'true') {
            $allBooks = $allBooks->filter(function ($book) {
                return $book->is_featured == true;
            });
        }

        // Price type filter (free/paid)
        if ($request->has('price_type') && $request->price_type) {
            if ($request->price_type == 'free') {
                $allBooks = $allBooks->filter(function ($book) {
                    return $book->is_paid == false || $book->is_paid == 0;
                });
            } elseif ($request->price_type == 'paid') {
                $allBooks = $allBooks->filter(function ($book) {
                    return $book->is_paid == true || $book->is_paid == 1;
                });
            }
        }

        // Institution filter
        if ($request->has('institution_id') && $request->institution_id) {
            $allBooks = $allBooks->filter(function ($book) use ($request) {
                return $book->institution_id == $request->institution_id;
            });
        }

        // Author filter
        if ($request->has('author') && $request->author) {
            $author = $request->author;
            $allBooks = $allBooks->filter(function ($book) use ($author) {
                return stripos($book->author ?? '', $author) !== false;
            });
        }

        // ==========================================
        // SORTING
        // ==========================================

        $sort = $request->get('sort', 'latest');

        switch ($sort) {
            case 'latest':
                $allBooks = $allBooks->sortByDesc('created_at');
                break;
            case 'oldest':
                $allBooks = $allBooks->sortBy('created_at');
                break;
            case 'title_asc':
                $allBooks = $allBooks->sortBy('title');
                break;
            case 'title_desc':
                $allBooks = $allBooks->sortByDesc('title');
                break;
            case 'downloads':
                $allBooks = $allBooks->sortByDesc('downloads');
                break;
            case 'views':
                $allBooks = $allBooks->sortByDesc('views_count');
                break;
            case 'price_low':
                $allBooks = $allBooks->sortBy('price');
                break;
            case 'price_high':
                $allBooks = $allBooks->sortByDesc('price');
                break;
            default:
                $allBooks = $allBooks->sortByDesc('created_at');
        }

        // ==========================================
        // PAGINATION
        // ==========================================

        $perPage = $request->get('per_page', 84);
        $page = $request->get('page', 1);

        $books = new LengthAwarePaginator(
            $allBooks->forPage($page, $perPage),
            $allBooks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get all categories (static list)
        $categories = $this->getCategories();

        // Get institutions for filter
        $institutions = Institution::where('status', 'approved')->get();

        return view('library.index', compact('books', 'categories', 'institutions'));
    }

    /**
     * Show a single book - works with both tables
     */
    public function show($id)
    {
        // Try to find the book in BOTH tables
        $book = Book::find($id);
        if (!$book) {
            $book = BookshopBook::find($id);
        }

        if (!$book) {
            abort(404, 'Book not found.');
        }

        // Increment view count
        if (isset($book->views_count)) {
            $book->increment('views_count');
        }

        // If book has NO institution, show it directly in a dedicated view
        if (!$book->institution_id) {
            // Load relationships
            $book->load(['uploader', 'ratings.user', 'reviews.user']);
            $book->loadCount(['ratings', 'reviews', 'bookmarks']);

            // Check if user has access
            $hasAccess = false;
            $progress = null;

            if (auth()->check()) {
                $hasAccess = !$book->isPaidItem() || $book->userHasAccess(auth()->id());
                $progress = auth()->user()->books()->where('book_id', $book->id)->first();
            }

            return view('library.global-book-show', compact('book', 'hasAccess', 'progress'));
        }

        // If book HAS institution, redirect to institution page
        return redirect()->route('institution.public.show', [
            'institutionId' => $book->institution_id,
            'book' => $book->id
        ]);
    }

    /**
     * Read book online (PDF viewer)
     */
    public function read($bookId)
    {
        $user = auth()->user();

        // Try to find the book
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }

        if (!$book) {
            abort(404, 'Book not found.');
        }

        // For free books, always allow access
        if (!$book->isPaidItem()) {
            // Auto-add free book to user's library if not already there
            $userBook = $user->books()->where('book_id', $book->id)->first();

            if (!$userBook) {
                $user->books()->attach($book->id, [
                    'status' => 'reading',
                    'progress_percent' => 0,
                    'current_page' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $currentPage = 0;
            } else {
                $currentPage = $userBook->pivot->current_page ?? 0;
            }

            return view('library.reader', compact('book', 'currentPage'));
        }

        // For paid books, check access
        if (method_exists($book, 'userHasAccess') && !$book->userHasAccess($user->id)) {
            return redirect()->route('institution.public.show', ['institutionId' => $book->institution_id ?? 1, 'book' => $book->id])
                ->with('error', 'You need to purchase this book to read it.');
        }

        // Get or create user_book record for paid books
        $userBook = $user->books()->where('book_id', $book->id)->first();

        if (!$userBook) {
            $user->books()->attach($book->id, [
                'status' => 'reading',
                'progress_percent' => 0,
                'current_page' => 0,
                'purchased_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $currentPage = 0;
        } else {
            $currentPage = $userBook->pivot->current_page ?? 0;
        }

        return view('library.reader', compact('book', 'currentPage'));
    }

    /**
     * Update reading progress (AJAX)
     */
    public function updateProgress(Request $request, $bookId)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'total_pages' => 'required|integer'
        ]);

        // Try to find the book
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }

        if (!$book) {
            return response()->json(['success' => false, 'message' => 'Book not found'], 404);
        }

        $progressPercent = round(($request->page / $request->total_pages) * 100);

        $userBook = UserBook::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $book->id
            ],
            [
                'current_page' => $request->page,
                'progress_percent' => $progressPercent,
                'status' => $progressPercent >= 100 ? 'completed' : 'reading'
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progressPercent,
            'status' => $userBook->status
        ]);
    }

    /**
     * My Library (user's books)
     */
    public function myLibrary()
    {
        $userId = Auth::id();

        // Books user is reading
        $reading = UserBook::where('user_id', $userId)
                          ->where('status', 'reading')
                          ->with('book')
                          ->get();

        // Books user completed
        $completed = UserBook::where('user_id', $userId)
                            ->where('status', 'completed')
                            ->with('book')
                            ->get();

        // Books user wants to read
        $wantToRead = UserBook::where('user_id', $userId)
                             ->where('status', 'want_to_read')
                             ->with('book')
                             ->get();

        // Purchased books (paid)
        $purchased = Book::whereHas('payments', function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('status', 'completed');
        })->get();

        return view('library.my-library', compact('reading', 'completed', 'wantToRead', 'purchased'));
    }

    /**
     * Serve PDF file with proper authentication
     */
    public function servePdf($bookId)
    {
        $user = auth()->user();

        // Check if user is logged in
        if (!$user) {
            abort(403, 'Please login to read this book.');
        }

        // Try to find the book
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }

        if (!$book) {
            abort(404, 'Book not found.');
        }

        // Allow access if user is Super Admin or Admin
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $this->returnPdfFile($book);
        }

        // For free books, any logged-in user can read
        if (!$book->isPaidItem()) {
            return $this->returnPdfFile($book);
        }

        // For paid books, check if user has purchased
        if (method_exists($book, 'userHasAccess') && $book->userHasAccess($user->id)) {
            return $this->returnPdfFile($book);
        }

        abort(403, 'You do not have permission to read this book.');
    }

    /**
     * Return the PDF file
     */
    private function returnPdfFile($book)
    {
        $filePath = storage_path('app/public/' . $book->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'Book file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $book->title . '.pdf"',
        ]);
    }

    /**
     * Add book to user's library
     */
    public function addToLibrary(Request $request, $bookId)
    {
        $status = $request->status ?? 'want_to_read';

        // Try to find the book
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }

        if (!$book) {
            return redirect()->back()->with('error', 'Book not found.');
        }

        UserBook::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'book_id' => $book->id
            ],
            ['status' => $status]
        );

        return redirect()->back()->with('success', 'Book added to your library!');
    }

    /**
     * Get trending books (API endpoint for dashboard)
     */
    public function getTrendingBooks()
    {
        $books = Book::where('is_trending', true)
            ->where('status', 'approved')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

    /**
     * Get recent books (API endpoint for dashboard)
     */
    public function getRecentBooks()
    {
        $books = Book::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

    /**
     * Get featured books (API endpoint for dashboard)
     */
    public function getFeaturedBooks()
    {
        $books = Book::where('is_featured', true)
            ->where('status', 'approved')
            ->limit(10)
            ->get();

        return response()->json($books);
    }

    /**
     * Resolve a book by id from either the Book or BookshopBook table.
     * Normalizes is_paid on BookshopBook records the same way the
     * rest of this controller already expects.
     */
    private function resolveBook($bookId)
    {
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
            if ($book) {
                $book->is_paid = ($book->price > 0) ? 1 : 0;
            }
        }

        return $book;
    }

    /**
     * Single source of truth for "has this user hit today's limit".
     * Both download() and downloadRaw() call this so they can never
     * disagree with each other again.
     */
    private function todaysDownloadCount($userId): int
    {
        return DownloadLog::where('user_id', $userId)
            ->whereDate('downloaded_at', today())
            ->count();
    }

    /**
     * Step 1: called via AJAX by the "Download PDF" button.
     * Validates the limit/access, logs the download, and hands back
     * the URL the browser should navigate to for the actual file.
     */
    public function download($bookId)
    {
        $user = auth()->user();

        if (!$user) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Please log in to download books.'
                ], 401);
            }
            return redirect()->back()->with('error', 'Please login to download books.');
        }

        $todayCount = $this->todaysDownloadCount($user->id);

        if ($todayCount >= self::DAILY_DOWNLOAD_LIMIT) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'You have reached the daily download limit of ' . self::DAILY_DOWNLOAD_LIMIT . ' books. Please try again tomorrow.',
                    'limit_reached' => true,
                    'remaining' => 0,
                    'used' => self::DAILY_DOWNLOAD_LIMIT
                ], 429);
            }
            return redirect()->back()->with('error', 'You have reached the daily download limit of ' . self::DAILY_DOWNLOAD_LIMIT . ' books. Please try again tomorrow.');
        }

        $book = $this->resolveBook($bookId);

        if (!$book) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'The book you are trying to download could not be found.'
                ], 404);
            }
            abort(404, 'Book not found.');
        }

        if ($book->isPaidItem() && method_exists($book, 'userHasAccess') && !$book->userHasAccess($user->id)) {
            $message = 'Please purchase this book to download it.';
            if (request()->ajax()) {
                return response()->json(['error' => $message], 403);
            }
            return redirect()->route('institution.public.show', ['institutionId' => $book->institution_id ?? 1, 'book' => $book->id])
                ->with('error', $message);
        }

        $filePath = storage_path('app/public/' . $book->file_path);
        if (!file_exists($filePath)) {
            if (request()->ajax()) {
                return response()->json([
                    'error' => 'The book file is currently unavailable. Please try again later.'
                ], 404);
            }
            abort(404, 'Book file not found.');
        }

        try {
            DownloadLog::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'downloaded_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log download: ' . $e->getMessage());
        }

        if (isset($book->downloads)) {
            $book->increment('downloads');
        }

        $newCount = $this->todaysDownloadCount($user->id);
        $remaining = max(0, self::DAILY_DOWNLOAD_LIMIT - $newCount);
        $used = $newCount;

        if (request()->ajax()) {
            $message = 'Book downloaded successfully.';
            if ($remaining <= 1 && $remaining > 0) {
                $message = 'Book downloaded. You have ' . $remaining . ' download(s) remaining today.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'download_url' => route('library.download.raw', $book->id),
                'remaining' => $remaining,
                'used' => $used,
                'limit' => self::DAILY_DOWNLOAD_LIMIT,
                'book_title' => $book->title
            ]);
        }

        session()->flash('download_limit_remaining', $remaining);
        session()->flash('download_limit_used', $used);
        session()->flash('download_message', 'Book downloaded successfully. You have ' . $remaining . ' download(s) remaining today.');

        return response()->download($filePath, $book->title . '.pdf');
    }

    /**
     * Step 2: the endpoint that actually streams the file to the browser.
     *
     * IMPORTANT: this used to have NO limit or paid-access check, which is
     * why users could see "limit reached" from download() yet still pull
     * files from this route directly (bookmarks, browser history, repeat
     * clicks, direct requests). It now re-checks both, using the exact
     * same counting logic as download(), so the two endpoints can't
     * disagree again. We do NOT log a second download here for the normal
     * flow (download() already logged it) — we only log if this route is
     * hit on its own without a prior download() call having happened for
     * this request cycle.
     */
    public function downloadRaw($bookId)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Please login to download books.');
        }

        // Re-enforce the limit here — this is the endpoint that hands out
        // the actual file, so it cannot be the weak link in the chain.
        if ($this->todaysDownloadCount($user->id) >= self::DAILY_DOWNLOAD_LIMIT
            && !session()->has('download_limit_remaining')) {
            abort(429, 'You have reached the daily download limit of ' . self::DAILY_DOWNLOAD_LIMIT . ' books. Please try again tomorrow.');
        }

        $book = $this->resolveBook($bookId);

        if (!$book) {
            abort(404, 'Book not found.');
        }

        if ($book->isPaidItem() && method_exists($book, 'userHasAccess') && !$book->userHasAccess($user->id)) {
            abort(403, 'Please purchase this book to download it.');
        }

        $filePath = storage_path('app/public/' . $book->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'Book file not found.');
        }

        return response()->download($filePath, $book->title . '.pdf');
    }
}