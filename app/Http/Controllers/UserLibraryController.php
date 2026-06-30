<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\LibraryPayment;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class UserLibraryController extends Controller
{
    /**
     * Display user's library dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        // Get purchased books
        $purchasedBooks = Book::whereHas('payments', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'completed');
        })->with(['institution'])->get();

        // Get reading progress (from user_books pivot table)
        $readingProgress = $user->books()->withPivot('progress_percent', 'status')->get();

        // Get bookmarks
        $bookmarks = Bookmark::where('user_id', $user->id)
            ->with('book')
            ->latest()
            ->limit(5)
            ->get();

        // Get recently viewed (from session)
        $recentlyViewed = $this->getRecentlyViewed();

        // Statistics
        $stats = [
            'total_purchased' => $purchasedBooks->count(),
            'total_read' => $readingProgress->where('pivot.status', 'completed')->count(),
            'total_reading' => $readingProgress->where('pivot.status', 'reading')->count(),
            'total_bookmarks' => Bookmark::where('user_id', $user->id)->count(),
        ];

        return view('user.library.index', compact(
            'purchasedBooks',
            'readingProgress',
            'bookmarks',
            'recentlyViewed',
            'stats'
        ));
    }

    /**
     * Get recently viewed books.
     */
    private function getRecentlyViewed()
    {
        $recentIds = session('recently_viewed_books', []);
        
        if (empty($recentIds)) {
            return collect();
        }

        return Book::whereIn('id', $recentIds)->limit(10)->get();
    }

    /**
     * Track book view.
     */
    public function trackView($bookId)
    {
        $recentIds = session('recently_viewed_books', []);
        
        $recentIds = array_unique(array_merge([$bookId], $recentIds));
        $recentIds = array_slice($recentIds, 0, 10);
        
        session(['recently_viewed_books' => $recentIds]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Update reading progress.
     */
    public function updateProgress(Request $request, $bookId)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'nullable|in:reading,completed,want_to_read',
        ]);

        $user = auth()->user();

        $user->books()->syncWithoutDetaching([
            $bookId => [
                'progress_percent' => $request->progress,
                'status' => $request->status ?? 'reading',
                'updated_at' => now(),
            ]
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Display a specific book in user's library.
     */
    public function show($bookId)
    {
        $user = auth()->user();
        
        $book = Book::whereHas('payments', function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'completed');
        })->with(['institution'])->findOrFail($bookId);

        // Get user progress
        $progress = $user->books()->where('book_id', $bookId)->first();

        return view('user.library.show', compact('book', 'progress'));
    }
}