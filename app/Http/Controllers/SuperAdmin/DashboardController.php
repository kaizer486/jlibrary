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
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // BASIC STATS
        // ==========================================
        $totalUsers = User::count();
        $totalInstitutions = Institution::count();
        $totalBooks = Book::count();
        $totalQuizzes = QuizAttempt::count();
        $totalCertificates = Certificate::count();
        
        // ==========================================
        // REVENUE STATS
        // ==========================================
        $totalRevenue = Payment::where('status', 'completed')->sum('amount') ?? 0;
        $platformEarnings = $totalRevenue * 0.20; // 20% platform commission
        
        // ==========================================
        // WITHDRAWAL STATS
        // ==========================================
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->sum('amount') ?? 0;
        $totalWithdrawals = WithdrawalRequest::where('status', 'completed')->sum('amount') ?? 0;
        
        // ==========================================
        // INSTITUTION REQUESTS
        // ==========================================
        $pendingRequests = InstitutionCreationRequest::where('status', 'pending')->count();
        $totalRequests = InstitutionCreationRequest::count();
        
        // ==========================================
        // SUBSCRIPTION STATS
        // ==========================================
        $subscriptionStats = $this->getSubscriptionStats();
        
        // ==========================================
        // CHART DATA - Monthly Revenue
        // ==========================================
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        $monthlyRevenue = [];
        $userGrowth = [];
        $subscriptionRevenue = [];
        
        for ($i = 0; $i < 12; $i++) {
            $month = $i + 1;
            $year = date('Y');
            
            // Monthly revenue from payments
            $monthlyRevenue[] = Payment::where('status', 'completed')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('amount') ?? 0;
            
            // Monthly user growth
            $userGrowth[] = User::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->count();
            
            // Monthly subscription revenue
            $subscriptionRevenue[] = Subscription::where('status', 'active')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->sum('amount') ?? 0;
        }
        
        // ==========================================
        // RECENT ACTIVITY
        // ==========================================
        $recentUsers = User::latest()->limit(5)->get();
        $recentInstitutions = Institution::latest()->limit(5)->get();
        $recentBooks = Book::with('institution')->latest()->limit(5)->get();
        $recentSubscriptions = Subscription::with('institution')
            ->latest()
            ->limit(5)
            ->get();
        $recentPayments = Payment::with('user')
            ->latest()
            ->limit(5)
            ->get();
        
        // ==========================================
        // PLAN DISTRIBUTION
        // ==========================================
        $planDistribution = Subscription::where('status', 'active')
            ->select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->get()
            ->pluck('count', 'plan')
            ->toArray();
        
        // ==========================================
        // PAYMENT METHOD DISTRIBUTION
        // ==========================================
        $paymentMethodDistribution = Subscription::where('status', 'active')
            ->select('payment_method', DB::raw('count(*) as count'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method')
            ->toArray();
        
        // ==========================================
        // INSTITUTION STATUS BREAKDOWN
        // ==========================================
        $institutionStatus = [
            'approved' => Institution::where('status', 'approved')->count(),
            'pending' => Institution::where('status', 'pending')->count(),
            'suspended' => Institution::where('status', 'suspended')->count(),
            'inactive' => Institution::where('status', 'inactive')->count(),
        ];
        
        // ==========================================
        // USER ROLE BREAKDOWN
        // ==========================================
        $userRoles = [
            'super_admin' => User::role('super_admin')->count(),
            'admin' => User::role('admin')->count(),
            'institution_admin' => User::role('institution_admin')->count(),
            'librarian' => User::role('librarian')->count(),
            'user' => User::role('user')->count(),
        ];
        
        // ==========================================
        // INSTITUTIONS WITHOUT SUBSCRIPTION
        // ==========================================
        $institutionsWithoutSubscription = Institution::whereDoesntHave('subscriptions', function($query) {
            $query->where('status', 'active');
        })->count();
        
        // ==========================================
        // EXPIRING SOON (7 DAYS)
        // ==========================================
        $expiringSoon = Subscription::where('status', 'active')
            ->where('ends_at', '<=', now()->addDays(7))
            ->where('ends_at', '>', now())
            ->count();
        
        // ==========================================
        // TOTAL SUBSCRIPTION REVENUE
        // ==========================================
        $totalSubscriptionRevenue = Subscription::where('status', 'active')->sum('amount') ?? 0;
        
        return view('super-admin.dashboard', compact(
            // Basic Stats
            'totalUsers',
            'totalInstitutions',
            'totalBooks',
            'totalQuizzes',
            'totalCertificates',
            
            // Revenue Stats
            'totalRevenue',
            'platformEarnings',
            'totalSubscriptionRevenue',
            
            // Withdrawal Stats
            'pendingWithdrawals',
            'totalWithdrawals',
            
            // Institution Requests
            'pendingRequests',
            'totalRequests',
            
            // Subscription Stats
            'subscriptionStats',
            
            // Chart Data
            'months',
            'monthlyRevenue',
            'userGrowth',
            'subscriptionRevenue',
            
            // Recent Activity
            'recentUsers',
            'recentInstitutions',
            'recentBooks',
            'recentSubscriptions',
            'recentPayments',
            
            // Distribution
            'planDistribution',
            'paymentMethodDistribution',
            'institutionStatus',
            'userRoles',
            'institutionsWithoutSubscription',
            'expiringSoon'
        ));
    }
    
    /**
     * Get comprehensive subscription statistics
     */
    protected function getSubscriptionStats(): array
    {
        // Active subscriptions by plan
        $activeByPlan = Subscription::where('status', 'active')
            ->select('plan', DB::raw('count(*) as count'))
            ->groupBy('plan')
            ->get()
            ->pluck('count', 'plan')
            ->toArray();
        
        // All status counts
        $statusCounts = [
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];
        
        // Total subscriptions
        $total = array_sum($statusCounts);
        
        // Calculate percentages
        $percentages = [];
        foreach ($statusCounts as $key => $count) {
            $percentages[$key] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
        }
        
        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyTrend[] = [
                'month' => $month->format('M'),
                'count' => Subscription::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }
        
        // Revenue by plan
        $revenueByPlan = Subscription::where('status', 'active')
            ->select('plan', DB::raw('sum(amount) as total'))
            ->groupBy('plan')
            ->get()
            ->pluck('total', 'plan')
            ->toArray();
        
        return [
            'total' => $total,
            'status_counts' => $statusCounts,
            'percentages' => $percentages,
            'active_by_plan' => $activeByPlan,
            'monthly_trend' => $monthlyTrend,
            'revenue_by_plan' => $revenueByPlan,
        ];
    }
    
    /**
     * Get real-time subscription analytics for AJAX
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $data = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'expiring_soon' => Subscription::where('status', 'active')
                ->where('ends_at', '<=', now()->addDays(7))
                ->where('ends_at', '>', now())
                ->count(),
            'revenue_today' => Subscription::whereDate('created_at', today())->sum('amount'),
            'revenue_this_month' => Subscription::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'plan_distribution' => Subscription::where('status', 'active')
                ->select('plan', DB::raw('count(*) as count'))
                ->groupBy('plan')
                ->get(),
            'payment_methods' => Subscription::where('status', 'active')
                ->select('payment_method', DB::raw('count(*) as count'))
                ->whereNotNull('payment_method')
                ->groupBy('payment_method')
                ->get(),
        ];
        
        return response()->json($data);
    }
    
    /**
     * Get subscription chart data for AJAX
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'subscriptions');
        $period = $request->get('period', 12);
        
        $labels = [];
        $data = [];
        
        for ($i = $period - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            
            if ($type === 'subscriptions') {
                $data[] = Subscription::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
            } elseif ($type === 'revenue') {
                $data[] = Subscription::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('amount') ?? 0;
            } elseif ($type === 'active') {
                $data[] = Subscription::where('status', 'active')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
            }
        }
        
        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}