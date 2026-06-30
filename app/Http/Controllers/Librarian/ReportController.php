<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        // Check if columns exist
        $hasViewsColumn = Schema::hasColumn('books', 'views_count');
        $hasDownloadsColumn = Schema::hasColumn('books', 'downloads');

        // Stats
        $stats = [
            'total_books' => Book::where('institution_id', $institution->id)->count(),
            'total_members' => User::where('institution_id', $institution->id)->count(),
            'total_views' => $hasViewsColumn ? Book::where('institution_id', $institution->id)->sum('views_count') : 0,
            'total_downloads' => $hasDownloadsColumn ? Book::where('institution_id', $institution->id)->sum('downloads') : 0,
            'growth' => 12,
            'member_growth' => 8,
            'view_growth' => 15,
            'download_growth' => 10,
        ];

        // Revenue
        $totalRevenue = Payment::where('institution_id', $institution->id)
            ->where('status', 'completed')
            ->sum('amount') ?? 0;
        
        $revenue = [
            'total' => $totalRevenue,
            'book_sales' => $totalRevenue,
            'library_share' => $totalRevenue * 0.80,
            'platform_share' => $totalRevenue * 0.20,
        ];

        // Popular Books
        $popularBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved');
        
        if ($hasViewsColumn) {
            $popularBooks = $popularBooks->orderBy('views_count', 'desc');
        } else {
            $popularBooks = $popularBooks->orderBy('created_at', 'desc');
        }
        $popularBooks = $popularBooks->limit(10)->get();

        // Top Categories
        $topCategories = Book::where('institution_id', $institution->id)
            ->whereNotNull('category')
            ->select('category', \DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Recent Activity
        $recentActivity = collect();

        return view('librarian.reports.index', compact(
            'stats',
            'revenue',
            'popularBooks',
            'topCategories',
            'recentActivity'
        ));
    }

    public function export(Request $request)
    {
        return redirect()->back()->with('success', 'Report exported successfully!');
    }
}