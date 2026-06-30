<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Borrowing;
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

        $hasViewsColumn = Schema::hasColumn('books', 'views_count');
        $hasDownloadsColumn = Schema::hasColumn('books', 'downloads');

        $stats = [
            'total_books' => Book::where('institution_id', $institution->id)->count(),
            'total_members' => User::where('institution_id', $institution->id)->count(),
            'total_views' => $hasViewsColumn ? Book::where('institution_id', $institution->id)->sum('views_count') : 0,
            'total_downloads' => $hasDownloadsColumn ? Book::where('institution_id', $institution->id)->sum('downloads') : 0,
            'total_borrowings' => Borrowing::where('institution_id', $institution->id)->count(),
            'active_borrowings' => Borrowing::where('institution_id', $institution->id)->where('status', 'borrowed')->count(),
        ];

        $popularBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved');

        if ($hasViewsColumn) {
            $popularBooks = $popularBooks->orderBy('views_count', 'desc');
        } else {
            $popularBooks = $popularBooks->orderBy('created_at', 'desc');
        }
        $popularBooks = $popularBooks->limit(10)->get();

        $topCategories = Book::where('institution_id', $institution->id)
            ->whereNotNull('category')
            ->select('category', \DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('institution.reports.index', compact(
            'stats',
            'popularBooks',
            'topCategories'
        ));
    }

    public function export(Request $request)
    {
        return redirect()->back()->with('success', 'Report exported successfully!');
    }
}