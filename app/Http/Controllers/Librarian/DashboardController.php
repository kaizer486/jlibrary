<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Shelf;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not associated with any institution.');
        }
       // Check if user is a librarian OR institution_admin
if (!auth()->user()->hasRole('librarian') && !auth()->user()->hasRole('institution_admin')) {
    abort(403, 'You do not have permission to access this page.');
}
        
        // ==========================================
        // BOOK STATISTICS
        // ==========================================
        $totalBooks = Book::where('institution_id', $institution->id)->count();
        $approvedBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->count();
        $pendingBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->count();
        $rejectedBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'rejected')
            ->count();
        
        // ==========================================
        // SHELF LOCATION STATISTICS
        // ==========================================
        $booksWithShelf = Book::where('institution_id', $institution->id)
            ->whereNotNull('shelf_number')
            ->count();
        $booksWithoutShelf = Book::where('institution_id', $institution->id)
            ->whereNull('shelf_number')
            ->count();
        
        // ==========================================
        // RECENT BOOKS
        // ==========================================
        $recentBooks = Book::where('institution_id', $institution->id)
            ->latest()
            ->limit(10)
            ->get();
        
        // ==========================================
        // BOOKS BY CATEGORY (if category column exists)
        // ==========================================
        $booksByCategory = [];
        if (Schema::hasColumn('books', 'category')) {
            $booksByCategory = Book::where('institution_id', $institution->id)
                ->select('category', \DB::raw('count(*) as total'))
                ->whereNotNull('category')
                ->groupBy('category')
                ->get();
        }
        
        // ==========================================
        // BOOKS BY SHELF (if shelf_name column exists)
        // ==========================================
        $booksByShelf = [];
        if (Schema::hasColumn('books', 'shelf_name')) {
            $booksByShelf = Book::where('institution_id', $institution->id)
                ->select('shelf_name', \DB::raw('count(*) as total'))
                ->whereNotNull('shelf_name')
                ->groupBy('shelf_name')
                ->get();
        }
        
        // ==========================================
        // RECENTLY ADDED BOOKS (last 7 days)
        // ==========================================
        $recentlyAdded = Book::where('institution_id', $institution->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // ==========================================
        // PENDING JOIN REQUESTS
        // ==========================================
        $pendingRequests = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        // ==========================================
        // TOTAL DOWNLOADS
        // ==========================================
        $totalDownloads = Book::where('institution_id', $institution->id)
            ->sum('downloads');
        
        // ==========================================
        // RECENT MEMBERS (who joined institution)
        // ==========================================
        $recentMembers = User::where('institution_id', $institution->id)
            ->latest()
            ->limit(5)
            ->get();

        // ==========================================
        // SHELVES FOR MAP
        // ==========================================
        $shelves = Shelf::where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();

        // ==========================================
        // STATS ARRAY (for the stats cards)
        // ==========================================
        $stats = [
            'total_books' => $totalBooks,
            'approved_books' => $approvedBooks,
            'pending_books' => $pendingBooks,
            'rejected_books' => $rejectedBooks,
            'total_members' => User::where('institution_id', $institution->id)->count(),
            'total_shelves' => $shelves->count(),
            'total_categories' => Book::where('institution_id', $institution->id)->whereNotNull('category')->distinct()->count('category'),
            'total_downloads' => $totalDownloads,
        ];

        return view('librarian.dashboard', compact(
            'institution',
            'stats',
            'totalBooks',
            'approvedBooks',
            'pendingBooks',
            'rejectedBooks',
            'booksWithShelf',
            'booksWithoutShelf',
            'recentBooks',
            'booksByCategory',
            'booksByShelf',
            'recentlyAdded',
            'totalDownloads',
            'recentMembers',
            'shelves',
            'pendingRequests'
        ));
    }
}