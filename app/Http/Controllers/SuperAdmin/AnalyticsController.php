<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Institution;
use App\Models\Payment;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\MarketplaceListing;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // User Stats
        $totalUsers        = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeUsers = User::where('last_active_at', '>=', now()->subDays(30))->count();

        // Book Stats
        $totalBooks      = Book::count();
        $approvedBooks   = Book::where('status', 'approved')->count();
        $pendingBooks    = Book::where('status', 'pending')->count();
        $totalDownloads  = Book::sum('downloads');

        // Institution Stats
        $totalInstitutions    = Institution::count();
        $approvedInstitutions = Institution::where('status', 'approved')->count();
        $pendingInstitutions  = Institution::where('status', 'pending')->count();

        // Financial Stats
        $totalRevenue      = Payment::where('status', 'completed')->sum('amount');
        $platformEarnings  = $totalRevenue * 0.20;
        $bookSales         = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->sum('amount');
        $totalWithdrawals  = WithdrawalRequest::where('status', 'completed')->sum('amount');

        // Engagement Stats
        $totalQuizzes        = QuizAttempt::count();
        $totalCertificates   = Certificate::count();
        $marketplaceListings = MarketplaceListing::count();

        // Monthly Data for Charts
        $monthlyRevenue = [];
        $monthlyUsers   = [];
        $monthlyBooks   = [];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = Payment::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $monthlyUsers[] = User::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();

            $monthlyBooks[] = Book::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        // Top Books by Downloads
        $topBooks = Book::orderByDesc('downloads')
            ->limit(5)
            ->get(['id', 'title', 'author', 'downloads', 'views_count']);

        // Top Books by Views (new)
        $topViewedBooks = Book::orderByDesc('views_count')
            ->limit(5)
            ->get(['id', 'title', 'author', 'views_count', 'downloads']);

        // Top Institutions
        $topInstitutions = Institution::withCount('users')
            ->orderByDesc('users_count')
            ->limit(5)
            ->get(['id', 'name', 'type']);

        // Recent Activity
        $recentUsers    = User::latest()->limit(5)->get();
        $recentBooks    = Book::latest()->limit(5)->get();
        $recentPayments = Payment::with('user')->latest()->limit(5)->get();

        return view('super-admin.analytics.index', compact(
            'totalUsers', 'newUsersThisMonth', 'activeUsers',
            'totalBooks', 'approvedBooks', 'pendingBooks', 'totalDownloads',
            'totalInstitutions', 'approvedInstitutions', 'pendingInstitutions',
            'totalRevenue', 'platformEarnings', 'bookSales', 'totalWithdrawals',
            'totalQuizzes', 'totalCertificates', 'marketplaceListings',
            'monthlyRevenue', 'monthlyUsers', 'monthlyBooks', 'months',
            'topBooks', 'topViewedBooks', 'topInstitutions',
            'recentUsers', 'recentBooks', 'recentPayments'
        ));
    }

    public function getData(Request $request)
    {
        $period = $request->get('period', 'monthly');

        if ($period === 'weekly') {
            $labels  = [];
            $revenue = [];
            $users   = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[]  = $date->format('D');
                $revenue[] = Payment::where('status', 'completed')
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('amount');
                $users[] = User::whereDate('created_at', $date->toDateString())->count();
            }
        } else {
            $labels  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            $revenue = [];
            $users   = [];

            for ($i = 1; $i <= 12; $i++) {
                $revenue[] = Payment::where('status', 'completed')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
                $users[] = User::whereMonth('created_at', $i)
                    ->whereYear('created_at', now()->year)
                    ->count();
            }
        }

        return response()->json([
            'labels'  => $labels,
            'revenue' => $revenue,
            'users'   => $users,
        ]);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'revenue');

        switch ($type) {
            case 'revenue':
                $data = Payment::with('user')->where('status', 'completed')->latest()->get();
                break;
            case 'users':
                $data = User::latest()->get();
                break;
            case 'books':
                $data = Book::with('institution')->latest()->get();
                break;
            default:
                $data = Institution::withCount('users')->latest()->get();
        }

        $fileName = $type . '_report_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($data, $type) {
            $file = fopen('php://output', 'w');

            if ($type === 'revenue') {
                fputcsv($file, ['Date', 'User', 'Amount', 'Type', 'Status']);
                foreach ($data as $item) {
                    fputcsv($file, [
                        $item->created_at->format('Y-m-d H:i'),
                        $item->user->full_name ?? 'N/A',
                        $item->amount,
                        $item->payable_type === 'App\\Models\\Book' ? 'Book Purchase' : 'Deposit',
                        $item->status,
                    ]);
                }
            } elseif ($type === 'users') {
                fputcsv($file, ['Name', 'Email', 'Role', 'Joined', 'Status']);
                foreach ($data as $item) {
                    fputcsv($file, [
                        $item->full_name,
                        $item->email,
                        $item->role,
                        $item->created_at->format('Y-m-d'),
                        $item->email_verified_at ? 'Verified' : 'Unverified',
                    ]);
                }
            } elseif ($type === 'books') {
                fputcsv($file, ['Title', 'Author', 'Institution', 'Price', 'Downloads', 'Views', 'Status']);
                foreach ($data as $item) {
                    fputcsv($file, [
                        $item->title,
                        $item->author,
                        $item->institution->name ?? 'N/A',
                        $item->is_paid ? $item->price : 'Free',
                        $item->downloads ?? 0,
                        $item->views_count ?? 0,
                        $item->status,
                    ]);
                }
            } else {
                fputcsv($file, ['Name', 'Type', 'Email', 'Status', 'Users', 'Joined']);
                foreach ($data as $item) {
                    fputcsv($file, [
                        $item->name,
                        $item->type,
                        $item->email,
                        $item->status,
                        $item->users_count ?? $item->users()->count(),
                        $item->created_at->format('Y-m-d'),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}