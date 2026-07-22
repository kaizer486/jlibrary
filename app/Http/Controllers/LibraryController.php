<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookshopBook;
use App\Models\UserBook;
use App\Models\Payment;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
     * Display all books in the global library
     */
    public function index(Request $request)
    {
        // Get books from BOTH tables
        $regularBooks = Book::where('status', 'approved')->get();
        $bookstoreBooks = BookshopBook::where('status', 'active')->get();

        // Merge collections
        $allBooks = $regularBooks->merge($bookstoreBooks);

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $allBooks = $allBooks->filter(function($book) use ($search) {
                return stripos($book->title, $search) !== false || 
                       stripos($book->author ?? '', $search) !== false;
            });
        }

        if ($request->has('category') && $request->category) {
            $allBooks = $allBooks->filter(function($book) use ($request) {
                return $book->category == $request->category;
            });
        }

        // Sort by created_at
        $allBooks = $allBooks->sortByDesc('created_at');

        // Paginate - FIXED: removed the stray quote
        $perPage = $request->get('per_page', 48); // Allow user to change per page, default 48
        $page = $request->get('page', 1);
        
        $books = new \Illuminate\Pagination\LengthAwarePaginator(
            $allBooks->forPage($page, $perPage),
            $allBooks->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get all categories (static list)
        $categories = $this->getCategories();

        return view('library.index', compact('books', 'categories'));
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
        
        // If book has NO institution, show it directly in a dedicated view
        if (!$book->institution_id) {
            // Load relationships
            $book->load(['uploader', 'ratings.user', 'reviews.user']);
            $book->loadCount(['ratings', 'reviews', 'bookmarks']);
            
            // Check if user has access
            $hasAccess = false;
            $progress = null;
            
            if (auth()->check()) {
                $hasAccess = !$book->is_paid || $book->userHasAccess(auth()->id());
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
        
        // Try to find the book in BOTH tables
        $book = Book::find($bookId);
        if (!$book) {
            $book = BookshopBook::find($bookId);
        }
        
        if (!$book) {
            abort(404, 'Book not found.');
        }
        
        // For free books, always allow access
        if (!$book->is_paid) {
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
     * Download book
     */
    public function download($bookId)
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
        
        // Check access for paid books
        if ($book->is_paid && method_exists($book, 'userHasAccess') && !$book->userHasAccess($user->id)) {
            return redirect()->route('institution.public.show', ['institutionId' => $book->institution_id ?? 1, 'book' => $book->id])
                ->with('error', 'Please purchase this book to download it.');
        }
        
        // Increment download count
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
        if (!$book->is_paid) {
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
}