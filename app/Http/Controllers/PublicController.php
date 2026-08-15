<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Book;
use App\Models\Shelf;
use App\Models\UserBook;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Show public institution library
     */
  public function index($institutionId)
{
    $institution = Institution::with('books')->findOrFail($institutionId);
    $books = $institution->books()->paginate(20);
    
    // Get the location (if it exists)
    $location = $institution->location; // Assuming there's a location relationship
    // Or if location is part of the institution model
    // $location = $institution;
    
    // Get shelves
    $shelves = $institution->shelves()->get();
    
    // Get categories
    $categories = $institution->books()->distinct('category')->pluck('category');
    
    return view('institution.public.index', compact('institution', 'books', 'location', 'shelves', 'categories'));
}
    
    /**
     * Show a single book from institution
     * Handles both ID and slug for flexibility
     */
    public function show($institutionId, $identifier)
    {
        $institution = Institution::findOrFail($institutionId);
        
        // Try to find by ID if numeric, otherwise by slug
        if (is_numeric($identifier)) {
            $book = Book::where('id', $identifier)
                ->where('institution_id', $institutionId)
                ->firstOrFail();
        } else {
            $book = Book::where('slug', $identifier)
                ->where('institution_id', $institutionId)
                ->firstOrFail();
        }

        $hasAccess = false;
        $progress = null;

        if (auth()->check()) {
            if (!$book->isPaidItem()) {
                $hasAccess = true;
            } else {
                $hasAccess = $book->userHasAccess(auth()->id());
            }

            // Load reading progress if any
            $progress = UserBook::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->first();
        }

        // Increment view count
        $book->increment('views_count');

        // FIXED: Correct view path
        return view('institution.public.show', compact('institution', 'book', 'hasAccess', 'progress'));
    }

    /**
     * Show shelf books
     */
    public function shelfShow($institutionId, $shelfId)
    {
        $institution = Institution::findOrFail($institutionId);
        $shelf = Shelf::where('id', $shelfId)
            ->where('institution_id', $institutionId)
            ->firstOrFail();
        $books = $shelf->books()->paginate(20);
        
        // FIXED: Correct view path
        return view('institution.public.shelf', compact('institution', 'shelf', 'books'));
    }

    /**
     * Search books in institution
     */
    public function search(Request $request, $institutionId)
    {
        $institution = Institution::findOrFail($institutionId);
        $query = $request->get('q');
        
        $books = Book::where('institution_id', $institutionId)
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('author', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->paginate(20);
        
        return view('institution.public.index', compact('institution', 'books'));
    }

    /**
     * Get book by ISBN
     */
    public function showByIsbn($institutionId, $isbn)
    {
        $institution = Institution::findOrFail($institutionId);
        $book = Book::where('isbn', $isbn)
            ->where('institution_id', $institutionId)
            ->firstOrFail();

        return $this->show($institutionId, $book->id);
    }

    /**
     * Get book by slug
     */
    public function showBySlug($institutionId, $slug)
    {
        return $this->show($institutionId, $slug);
    }

    /**
     * Get books by category
     */
    public function category($institutionId, $category)
    {
        $institution = Institution::findOrFail($institutionId);
        $books = Book::where('institution_id', $institutionId)
            ->where('category', $category)
            ->paginate(20);
        
        return view('institution.public.index', compact('institution', 'books', 'category'));
    }

    /**
     * Get featured books in institution
     */
    public function featured($institutionId)
    {
        $institution = Institution::findOrFail($institutionId);
        $books = Book::where('institution_id', $institutionId)
            ->where('is_featured', true)
            ->paginate(20);
        
        return view('institution.public.index', compact('institution', 'books'));
    }

    /**
     * Get trending books in institution
     */
    public function trending($institutionId)
    {
        $institution = Institution::findOrFail($institutionId);
        $books = Book::where('institution_id', $institutionId)
            ->where('is_trending', true)
            ->paginate(20);
        
        return view('institution.public.index', compact('institution', 'books'));
    }

    /**
     * Download book from institution
     */
    public function download($institutionId, $bookId)
    {
        $book = Book::where('id', $bookId)
            ->where('institution_id', $institutionId)
            ->firstOrFail();
            
        // Check if user has access
        if (auth()->check() && (!$book->isPaidItem() || $book->userHasAccess(auth()->id()))) {
            $book->increment('downloads');
            
            if ($book->file_path && file_exists(storage_path('app/public/' . $book->file_path))) {
                return response()->download(storage_path('app/public/' . $book->file_path));
            }
            
            abort(404, 'File not found.');
        }
        
        abort(403, 'You do not have access to download this book.');
    }

    /**
     * Read book from institution
     */
    public function read($institutionId, $bookId)
    {
        $institution = Institution::findOrFail($institutionId);
        $book = Book::where('id', $bookId)
            ->where('institution_id', $institutionId)
            ->firstOrFail();

        // Check access
        $hasAccess = false;
        if (auth()->check()) {
            if (!$book->isPaidItem()) {
                $hasAccess = true;
            } else {
                $hasAccess = $book->userHasAccess(auth()->id());
            }
        }

        if (!$hasAccess) {
            abort(403, 'You do not have access to read this book.');
        }

        // Track progress
        $progress = null;
        if (auth()->check()) {
            $progress = UserBook::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'book_id' => $book->id,
                ],
                [
                    'status' => 'reading',
                    'last_accessed_at' => now(),
                ]
            );
        }

        // Increment view count
        $book->increment('views_count');

        return view('institution.public.read', compact('institution', 'book', 'progress'));
    }

    /**
     * Update reading progress for institution book
     */
    public function updateProgress(Request $request, $institutionId, $bookId)
    {
        $book = Book::where('id', $bookId)
            ->where('institution_id', $institutionId)
            ->firstOrFail();

        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'progress_percent' => 'required|integer|min:0|max:100',
            'status' => 'nullable|string|in:reading,completed',
        ]);

        $userBook = UserBook::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'book_id' => $book->id,
            ],
            [
                'progress_percent' => $request->progress_percent,
                'status' => $request->status ?? 'reading',
                'last_accessed_at' => now(),
            ]
        );

        if ($request->status === 'completed') {
            $userBook->completed_at = now();
            $userBook->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Progress updated successfully',
            'progress' => $userBook,
        ]);
    }
}