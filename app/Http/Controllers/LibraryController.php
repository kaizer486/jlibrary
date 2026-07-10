<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        $query = Book::where('status', 'approved');
        
        // Search by title or author
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        // Filter by type (free/paid)
        if ($request->has('type') && $request->type) {
            if ($request->type === 'free') {
                $query->where('is_paid', false);
            } elseif ($request->type === 'paid') {
                $query->where('is_paid', true);
            }
        }
        
        $books = $query->latest()->paginate(12);
        
        // Get all categories (static list)
        $categories = $this->getCategories();
        
        return view('library.index', compact('books', 'categories'));
    }
    
    /**
     * Redirect to institution book page instead of showing here
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);
        return redirect()->route('institution.public.show', [$book->institution_id, $book->id]);
    }
    
    /**
     * Read book online (PDF viewer)
     */
    public function read(Book $book)
    {
        $user = auth()->user();
        
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
        if (!$book->userHasAccess($user->id)) {
            return redirect()->route('institution.public.show', [$book->institution_id, $book->id])
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
    public function updateProgress(Request $request, Book $book)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'total_pages' => 'required|integer'
        ]);
        
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
    public function download(Book $book)
    {
        // Check access for paid books
        if ($book->is_paid && !$book->userHasAccess(Auth::id())) {
            return redirect()->route('institution.public.show', [$book->institution_id, $book->id])
                ->with('error', 'Please purchase this book to download it.');
        }
        
        // Increment download count
        $book->increment('downloads');
        
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
    public function servePdf(Book $book)
    {
        $user = auth()->user();
        
        // Check if user is logged in
        if (!$user) {
            abort(403, 'Please login to read this book.');
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
        if ($book->userHasAccess($user->id)) {
            return $this->returnPdfFile($book);
        }
        
        abort(403, 'You do not have permission to read this book.');
    }
    
    /**
     * Return the PDF file
     */
    private function returnPdfFile(Book $book)
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
    public function addToLibrary(Request $request, Book $book)
    {
        $status = $request->status ?? 'want_to_read';
        
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