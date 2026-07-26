<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Display author's marketplace dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get author's books
        $books = Book::where('uploaded_by', $user->id)->get();
        $bookIds = $books->pluck('id');
        
        // Stats
        $totalBooks = $books->count();
        $publishedBooks = $books->where('status', 'approved')->count();
        $pendingBooks = $books->where('status', 'pending')->count();
        
        // Sales stats
        $totalSales = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->sum('amount');
        
        $totalRoyalties = $totalSales * 0.10;
        
        // Orders
        $recentOrders = Order::whereHas('items', function($query) use ($bookIds) {
            $query->whereIn('book_id', $bookIds);
        })->with('user')->latest()->limit(10)->get();
        
        // Top selling books
        $topBooks = Book::where('uploaded_by', $user->id)
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('author.marketplace.index', compact(
            'books', 'totalBooks', 'publishedBooks', 'pendingBooks',
            'totalSales', 'totalRoyalties', 'recentOrders', 'topBooks'
        ));
    }
    
    /**
     * Display author's book listings
     */
    public function listings()
    {
        $books = Book::where('uploaded_by', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('author.marketplace.listings', compact('books'));
    }
    
    /**
     * Display author's orders
     */
    public function orders()
    {
        $bookIds = Book::where('uploaded_by', auth()->id())->pluck('id');
        
        $orders = Order::whereHas('items', function($query) use ($bookIds) {
            $query->whereIn('book_id', $bookIds);
        })->with(['user', 'items.book'])
        ->latest()
        ->paginate(20);
        
        return view('author.marketplace.orders', compact('orders'));
    }
    
    /**
     * Display author's earnings
     */
    public function earnings()
    {
        $user = auth()->user();
        $bookIds = Book::where('uploaded_by', $user->id)->pluck('id');
        
        // Monthly earnings for chart
        $monthlyEarnings = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales = Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $bookIds)
                ->whereMonth('created_at', $i)
                ->whereYear('created_at', date('Y'))
                ->sum('amount');
            $monthlyEarnings[] = $monthlySales * 0.10;
        }
        
        // Total stats
        $totalSales = Payment::where('status', 'completed')
            ->where('payable_type', 'App\\Models\\Book')
            ->whereIn('payable_id', $bookIds)
            ->sum('amount');
        
        $totalRoyalties = $totalSales * 0.10;
        
        $totalWithdrawn = \App\Models\WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        
        $pendingWithdrawal = \App\Models\WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');
        
        $availableBalance = $totalRoyalties - $totalWithdrawn;
        
        return view('author.marketplace.earnings', compact(
            'monthlyEarnings', 'months', 'totalSales', 'totalRoyalties',
            'totalWithdrawn', 'pendingWithdrawal', 'availableBalance'
        ));
    }
}