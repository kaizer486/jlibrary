<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use App\Models\Book;
use App\Models\Payment;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\WithdrawalRequest;
use App\Models\InstitutionCreationRequest; 
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Super Admin Stats
        $totalUsers = User::count();
        $totalInstitutions = Institution::count();
        $totalBooks = Book::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $totalQuizzes = QuizAttempt::count();
        $totalCertificates = Certificate::count();
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->sum('amount');
        
        // Platform Earnings (20% commission)
        $platformEarnings = $totalRevenue * 0.20;
        
        // ✅ Pending Institution Creation Requests
        $pendingRequests = InstitutionCreationRequest::where('status', 'pending')->count();
        
        // Recent Activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentInstitutions = Institution::latest()->limit(5)->get();
        $recentBooks = Book::latest()->limit(5)->get();
        
        // Chart Data - Monthly Revenue
        $monthlyRevenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyRevenue[] = Payment::where('status', 'completed')
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('amount');
        }
        
        // User Growth
        $userGrowth = [];
        for ($i = 1; $i <= 12; $i++) {
            $userGrowth[] = User::whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->count();
        }
        
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        return view('super-admin.dashboard', compact(
            'totalUsers', 'totalInstitutions', 'totalBooks', 'totalRevenue',
            'totalQuizzes', 'totalCertificates', 'pendingWithdrawals',
            'platformEarnings', 'recentUsers', 'recentInstitutions', 'recentBooks',
            'monthlyRevenue', 'userGrowth', 'months',
            'pendingRequests' // 
        ));
    }
}