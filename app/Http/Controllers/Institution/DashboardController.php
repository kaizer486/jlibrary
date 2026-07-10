<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\JoinRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $institution = $user->institution;
        
        if (!$institution) {
            return redirect()->route('dashboard')
                ->with('error', 'You are not associated with any institution.');
        }
        
        // ==========================================
        // DETECT INSTITUTION TYPE
        // ==========================================
        $isBookstore = $institution->type === 'bookstore';
        
        if ($isBookstore) {
            return $this->bookstoreDashboard($institution);
        }
        
        return $this->libraryDashboard($institution);
    }

    /**
     * Library Dashboard
     */
    private function libraryDashboard($institution)
    {
        // Members stats
        $totalMembers = User::where('institution_id', $institution->id)->count();
        
        // Books stats
        $totalBooks = Book::where('institution_id', $institution->id)->count();
        
        // Admin stats
        $totalAdmins = User::where('institution_id', $institution->id)
            ->where('role', 'institution_admin')
            ->count();
        
        // Librarian stats
        $totalLibrarians = User::where('institution_id', $institution->id)
            ->where('role', 'librarian')
            ->count();
        
        // Pending join requests
        $pendingRequests = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->count();

        // Wallet balance
        $walletBalance = $institution->wallet_balance ?? 0;

        // Pending withdrawal amount
        $pendingWithdrawalRequests = 0;
        if (method_exists($institution, 'withdrawals')) {
            $pendingWithdrawalRequests = $institution->withdrawals()
                ->where('status', 'pending')
                ->sum('amount') ?? 0;
        }
        
        $stats = [
            'total_members'               => $totalMembers,
            'total_books'                 => $totalBooks,
            'total_admins'                => $totalAdmins,
            'total_librarians'            => $totalLibrarians,
            'pending_requests'            => $pendingRequests,
            'wallet_balance'              => $walletBalance,
            'pending_withdrawal_requests' => $pendingWithdrawalRequests,
        ];
        
        // Recent members
        $recentMembers = User::where('institution_id', $institution->id)
            ->latest()
            ->limit(5)
            ->get();
        
        // Recent books
        $recentBooks = Book::where('institution_id', $institution->id)
            ->latest()
            ->limit(5)
            ->get();
        
        // Recent join requests
        $recentRequests = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
        
        // ==========================================
        // SUBSCRIPTION DATA
        // ==========================================
        $subscription = [
            'is_active' => $institution->isSubscriptionActive(),
            'days_left' => $institution->getDaysLeft(),
            'progress' => $institution->getSubscriptionProgress(),
            'status_color' => $institution->getSubscriptionStatusColor(),
            'status_label' => $institution->getSubscriptionStatusLabel(),
            'plan_label' => $institution->getPlanLabel(),
            'plan' => $institution->subscription_tier,
            'started_at' => $institution->subscription_started_at ?? null,
            'expires_at' => $institution->subscription_expires_at ?? null,
        ];
        
        return view('institution.dashboard', compact(
            'institution', 
            'stats', 
            'recentMembers', 
            'recentBooks',
            'recentRequests',
            'subscription' 
        ));
    }

    /**
     * Bookstore Dashboard
     */
    /**
 * Bookstore Dashboard
 */
private function bookstoreDashboard($institution)
{
    // Get all products (books)
    $totalProducts = Book::where('institution_id', $institution->id)->count();
    
    // In stock products (assuming you have a 'quantity' field)
    $inStock = Book::where('institution_id', $institution->id)
        ->where('quantity', '>', 0)
        ->count();
    
    // Out of stock products
    $outOfStock = Book::where('institution_id', $institution->id)
        ->where('quantity', '<=', 0)
        ->count();
    
    // Total customers (members)
    $totalCustomers = User::where('institution_id', $institution->id)->count();
    
    // ==========================================
    // ORDERS & SALES - CHECK IF TABLE EXISTS
    // ==========================================
    $todaySales = 0;
    $totalSales = 0;
    $pendingOrders = 0;
    $recentOrders = collect();
    
    // ✅ Check if orders table exists before querying
    try {
        if (Schema::hasTable('orders') && class_exists('App\Models\Order')) {
            $todaySales = Order::where('institution_id', $institution->id)
                ->whereDate('created_at', today())
                ->sum('total') ?? 0;
            
            $totalSales = Order::where('institution_id', $institution->id)
                ->sum('total') ?? 0;
            
            $pendingOrders = Order::where('institution_id', $institution->id)
                ->where('status', 'pending')
                ->count() ?? 0;
            
            $recentOrders = Order::where('institution_id', $institution->id)
                ->latest()
                ->limit(5)
                ->get();
        }
    } catch (\Exception $e) {
        \Log::warning('Orders table not found: ' . $e->getMessage());
    }
    
    // Low stock products (5 or less)
    $lowStockBooks = Book::where('institution_id', $institution->id)
        ->where('quantity', '<=', 5)
        ->where('quantity', '>', 0)
        ->limit(5)
        ->get();
    
    // Wallet balance
    $walletBalance = $institution->wallet_balance ?? 0;
    
    // Pending withdrawal requests
    $pendingWithdrawalRequests = 0;
    if (method_exists($institution, 'withdrawals')) {
        $pendingWithdrawalRequests = $institution->withdrawals()
            ->where('status', 'pending')
            ->sum('amount') ?? 0;
    }
    
    $stats = [
        'total_products'              => $totalProducts,
        'in_stock'                    => $inStock,
        'out_of_stock'                => $outOfStock,
        'total_customers'             => $totalCustomers,
        'today_sales'                 => $todaySales,
        'total_sales'                 => $totalSales,
        'pending_orders'              => $pendingOrders,
        'wallet_balance'              => $walletBalance,
        'pending_withdrawal_requests' => $pendingWithdrawalRequests,
    ];
    
    // ==========================================
    // SUBSCRIPTION DATA
    // ==========================================
    $subscription = [
        'is_active' => $institution->isSubscriptionActive(),
        'days_left' => $institution->getDaysLeft(),
        'progress' => $institution->getSubscriptionProgress(),
        'status_color' => $institution->getSubscriptionStatusColor(),
        'status_label' => $institution->getSubscriptionStatusLabel(),
        'plan_label' => $institution->getPlanLabel(),
        'plan' => $institution->subscription_tier,
        'started_at' => $institution->subscription_started_at ?? null,
        'expires_at' => $institution->subscription_expires_at ?? null,
    ];
    
    return view('institution.dashboard-bookstore', compact(
        'institution',
        'stats',
        'recentOrders',
        'lowStockBooks',
        'subscription'
    ));
}
}