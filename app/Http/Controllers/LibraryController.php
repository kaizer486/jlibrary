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
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryController extends Controller
{
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
            $allBooks = $allBooks->filter(function($book) use ($search) {
                return stripos($book->title, $search) !== false || 
                       stripos($book->author ?? '', $search) !== false ||
                       stripos($book->description ?? '', $search) !== false;
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $allBooks = $allBooks->filter(function($book) use ($request) {
                return $book->category == $request->category;
            });
        }
        
        // Sub-category filter
        if ($request->has('sub_category') && $request->sub_category) {
            $allBooks = $allBooks->filter(function($book) use ($request) {
                return $book->sub_category == $request->sub_category;
            });
        }
        
        // Trending filter - NEW
        if ($request->has('trending') && $request->trending == 'true') {
            $allBooks = $allBooks->filter(function($book) {
                return $book->is_trending == true;
            });
        }
        
        // Recent filter - NEW
        if ($request->has('recent') && $request->recent == 'true') {
            $allBooks = $allBooks->sortByDesc('created_at');
        }
        
        // Featured filter - NEW
        if ($request->has('featured') && $request->featured == 'true') {
            $allBooks = $allBooks->filter(function($book) {
                return $book->is_featured == true;
            });
        }
        
        // Price type filter (free/paid)
        if ($request->has('price_type') && $request->price_type) {
            if ($request->price_type == 'free') {
                $allBooks = $allBooks->filter(function($book) {
                    return $book->is_paid == false || $book->is_paid == 0;
                });
            } elseif ($request->price_type == 'paid') {
                $allBooks = $allBooks->filter(function($book) {
                    return $book->is_paid == true || $book->is_paid == 1;
                });
            }
        }
        
        // Institution filter
        if ($request->has('institution_id') && $request->institution_id) {
            $allBooks = $allBooks->filter(function($book) use ($request) {
                return $book->institution_id == $request->institution_id;
            });
        }
        
        // Author filter
        if ($request->has('author') && $request->author) {
            $author = $request->author;
            $allBooks = $allBooks->filter(function($book) use ($author) {
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
        
        $perPage = $request->get('per_page', 48);
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
                $hasAccess =!$book->isPaidItem() || $book->userHasAccess(auth()->id());
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
 * Download book with daily limit (5 total downloads per day)
 */
public function download($bookId)
{
    $user = auth()->user();
    
    // Check daily download limit FIRST (strict: 5 total, any books)
    if ($user->hasReachedDailyDownloadLimit()) {
        return redirect()->back()->with('error', 'Daily download limit reached. You can download up to 5 books per day. Please try again tomorrow.');
    }
    
    // Try to find the book
    $book = Book::find($bookId);
    if (!$book) {
        $book = BookshopBook::find($bookId);
    }
    
    if (!$book) {
        abort(404, 'Book not found.');
    }
    
    // Check access for paid books
    if ($book->isPaidItem() && method_exists($book, 'userHasAccess') && !$book->userHasAccess($user->id)) {
        return redirect()->route('institution.public.show', ['institutionId' => $book->institution_id ?? 1, 'book' => $book->id])
            ->with('error', 'Please purchase this book to download it.');
    }
    
    // Log the download (counts toward daily limit)
    $user->logDownload($book);
    
    // Increment book download count
    if (isset($book->downloads)) {
        $book->increment('downloads');
    }
    
    // Get file path
    $filePath = storage_path('app/public/' . $book->file_path);
    
    if (!file_exists($filePath)) {
        abort(404, 'Book file not found.');
    }
    
    return response()->download($filePath, $book->title . '.pdf');
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
        $purchased = Book::whereHas('payments', function($q) use ($userId) {
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
}