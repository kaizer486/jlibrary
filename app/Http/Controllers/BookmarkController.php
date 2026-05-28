<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    // Toggle bookmark (add/remove)
    public function toggle(Request $request)
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|string|in:book'
        ]);

        // Only support Book model for now
        if ($request->bookmarkable_type !== 'book') {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $book = Book::find($request->bookmarkable_id);
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $existingBookmark = Bookmark::where('user_id', Auth::id())
            ->where('bookmarkable_id', $book->id)
            ->where('bookmarkable_type', Book::class)
            ->first();

        if ($existingBookmark) {
            $existingBookmark->delete();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'bookmarked' => false,
                    'message' => 'Removed from bookmarks',
                    'count' => $book->bookmarks()->count()
                ]);
            }
            
            return redirect()->back()->with('success', 'Removed from bookmarks');
        } else {
            Bookmark::create([
                'user_id' => Auth::id(),
                'bookmarkable_id' => $book->id,
                'bookmarkable_type' => Book::class,
                'note' => $request->note
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'bookmarked' => true,
                    'message' => 'Added to bookmarks',
                    'count' => $book->bookmarks()->count()
                ]);
            }
            
            return redirect()->back()->with('success', 'Added to bookmarks');
        }
    }

    // Get all user bookmarks
    public function index()
    {
        $bookmarks = Bookmark::where('user_id', Auth::id())
            ->with('bookmarkable')
            ->latest()
            ->paginate(20);

        return view('bookmarks.index', compact('bookmarks'));
    }

    // Remove bookmark
    public function destroy($id)
    {
        $bookmark = Bookmark::where('user_id', Auth::id())->findOrFail($id);
        $bookmark->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bookmark removed']);
        }

        return redirect()->back()->with('success', 'Bookmark removed');
    }
}