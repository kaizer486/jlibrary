<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index()
{
    $user = auth()->user();
    
    // Get approved application
    $approvedApp = $user->applications()->with('reviewer')->where('status', 'approved')->where('type', 'author')->first();
    
    // Get author's books
    $totalBooks = Book::where('uploaded_by', $user->id)->count();
    $publishedBooks = Book::where('uploaded_by', $user->id)->where('status', 'approved')->count();
    $pendingBooks = Book::where('uploaded_by', $user->id)->where('status', 'pending')->count();
    
    // Get sales data (from payments where payable_type is Book and book belongs to author)
    $bookIds = Book::where('uploaded_by', $user->id)->pluck('id');
    
    $totalSales = Payment::where('status', 'completed')
        ->where('payable_type', 'App\\Models\\Book')
        ->whereIn('payable_id', $bookIds)
        ->sum('amount');
    
    // Author earns 10% commission
    $totalRoyalties = $totalSales * 0.10;
    
    // Get royalty records for the table
    $royalties = Payment::where('status', 'completed')
        ->where('payable_type', 'App\\Models\\Book')
        ->whereIn('payable_id', $bookIds)
        ->with('user')
        ->latest()
        ->get();
    
    // Calculate paid out and available balance
    $totalWithdrawn = WithdrawalRequest::where('user_id', $user->id)
        ->where('status', 'completed')
        ->sum('amount');
    
    $pendingWithdrawal = WithdrawalRequest::where('user_id', $user->id)
        ->where('status', 'pending')
        ->sum('amount');
    
    $availableBalance = $totalRoyalties - $totalWithdrawn;
    
    // Get recent sales
    $recentSales = Payment::where('status', 'completed')
        ->where('payable_type', 'App\\Models\\Book')
        ->whereIn('payable_id', $bookIds)
        ->with('user')
        ->latest()
        ->limit(10)
        ->get();
    
    // Get author's books with download counts
    $topBooks = Book::where('uploaded_by', $user->id)
        ->orderBy('downloads', 'desc')
        ->limit(5)
        ->get();
    
    // Monthly earnings data for chart
    $monthlyEarnings = [];
    for ($i = 1; $i <= 12; $i++) {
        $monthlySales = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->whereMonth('created_at', $i)
            ->whereYear('created_at', date('Y'))
            ->sum('amount');
        $monthlyEarnings[] = $monthlySales * 0.10;
    }
    
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    return view('author.dashboard', compact(
        'user', 'approvedApp', 'totalBooks', 'publishedBooks', 'pendingBooks',
        'totalSales', 'totalRoyalties', 'recentSales', 'topBooks',
        'monthlyEarnings', 'months', 'totalWithdrawn', 'pendingWithdrawal', 
        'availableBalance', 'royalties'
    ));
}
}