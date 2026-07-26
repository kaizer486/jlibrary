<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Payment;
use App\Models\WithdrawalRequest;
use App\Models\MarketplaceListing;
use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ==========================================
        // AUTHOR DATA
        // ==========================================
        $approvedApp = $user->applications()
            ->with('reviewer')
            ->where('status', 'approved')
            ->where('type', 'author')
            ->first();

        $totalBooks = Book::where('uploaded_by', $user->id)->count();
        $publishedBooks = Book::where('uploaded_by', $user->id)->where('status', 'approved')->count();
        $pendingBooks = Book::where('uploaded_by', $user->id)->where('status', 'pending')->count();

        $bookIds = Book::where('uploaded_by', $user->id)->pluck('id');

        // Book sales (from library purchases)
        $bookSalesTotal = $bookIds->isNotEmpty() 
            ? Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $bookIds)
                ->sum('amount')
            : 0;

        $bookRoyalties = $bookSalesTotal * 0.10;

        $recentBookSales = $bookIds->isNotEmpty()
            ? Payment::where('status', 'completed')
                ->where('payable_type', 'App\\Models\\Book')
                ->whereIn('payable_id', $bookIds)
                ->with('user')
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $topBooks = Book::where('uploaded_by', $user->id)
            ->orderBy('downloads', 'desc')
            ->limit(5)
            ->get();

        // Book monthly earnings
        $bookMonthlyEarnings = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySales = $bookIds->isNotEmpty()
                ? Payment::where('status', 'completed')
                    ->where('payable_type', 'App\\Models\\Book')
                    ->whereIn('payable_id', $bookIds)
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', date('Y'))
                    ->sum('amount')
                : 0;
            $bookMonthlyEarnings[] = $monthlySales * 0.10;
        }

        // ==========================================
        // SELLER DATA (Marketplace)
        // ==========================================
        $totalListings = MarketplaceListing::where('seller_id', $user->id)->count();
        
        $listingIds = MarketplaceListing::where('seller_id', $user->id)->pluck('id');

        $totalProductSales = $listingIds->isNotEmpty()
            ? MarketplaceOrder::whereIn('listing_id', $listingIds)
                ->where('status', 'completed')
                ->count()
            : 0;

        $productEarnings = $listingIds->isNotEmpty()
            ? MarketplaceOrder::whereIn('listing_id', $listingIds)
                ->where('status', 'completed')
                ->sum('seller_earnings')
            : 0;

        $pendingOrders = $listingIds->isNotEmpty()
            ? MarketplaceOrder::whereIn('listing_id', $listingIds)
                ->where('status', 'pending')
                ->count()
            : 0;

        $recentProductSales = $listingIds->isNotEmpty()
            ? MarketplaceOrder::whereIn('listing_id', $listingIds)
                ->where('status', 'completed')
                ->with(['buyer', 'listing'])
                ->latest()
                ->limit(5)
                ->get()
            : collect();

        $listings = MarketplaceListing::where('seller_id', $user->id)
            ->withCount(['orders' => function($q) {
                $q->where('status', 'completed');
            }])
            ->latest()
            ->limit(6)
            ->get();

        // Product monthly earnings
        $productMonthlyEarnings = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyProductSales = $listingIds->isNotEmpty()
                ? MarketplaceOrder::whereIn('listing_id', $listingIds)
                    ->where('status', 'completed')
                    ->whereMonth('created_at', $i)
                    ->whereYear('created_at', date('Y'))
                    ->sum('seller_earnings')
                : 0;
            $productMonthlyEarnings[] = $monthlyProductSales;
        }

        // ==========================================
        // COMBINED STATS
        // ==========================================
        $totalEarnings = $bookRoyalties + $productEarnings;
        
        $totalWithdrawn = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');

        $pendingWithdrawal = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = max(0, $totalEarnings - $totalWithdrawn);

        // Combined monthly earnings for chart
        $monthlyEarnings = [];
        for ($i = 0; $i < 12; $i++) {
            $monthlyEarnings[] = ($bookMonthlyEarnings[$i] ?? 0) + ($productMonthlyEarnings[$i] ?? 0);
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Seller type badge
        $sellerType = method_exists($user, 'getSellerType') ? $user->getSellerType() : 'Author & Seller';

        return view('author.dashboard', compact(
            // Author
            'user', 'approvedApp', 'totalBooks', 'publishedBooks', 'pendingBooks',
            'bookSalesTotal', 'bookRoyalties', 'recentBookSales', 'topBooks',
            'bookMonthlyEarnings',
            // Seller
            'totalListings', 'totalProductSales', 'productEarnings', 'pendingOrders',
            'recentProductSales', 'listings', 'productMonthlyEarnings', 'sellerType',
            // Combined
            'totalEarnings', 'totalWithdrawn', 'pendingWithdrawal', 'availableBalance',
            'monthlyEarnings', 'months'
        ));
    }
}