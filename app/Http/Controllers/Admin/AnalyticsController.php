<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\Payment;
use App\Models\QuizAttempt;
use App\Models\Certificate;
use App\Models\MarketplaceListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Basic Stats
        $totalUsers = User::count();
        $totalBooks = Book::where('status', 'approved')->count();
        $totalSales = Payment::where('status', 'completed')->sum('amount');
        $totalQuizzesTaken = QuizAttempt::count();
        $totalCertificates = Certificate::count();
        $marketplaceListings = MarketplaceListing::count();
        $pendingApprovals = Book::where('status', 'pending')->count();

        // Monthly sales data for chart
        $monthlySales = Payment::where('status', 'completed')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        $salesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $salesData[] = $monthlySales[$i] ?? 0;
        }

        // User growth data
        $userGrowth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as count'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $userGrowthData = [];
        for ($i = 1; $i <= 12; $i++) {
            $userGrowthData[] = $userGrowth[$i] ?? 0;
        }

        // Popular books (most downloaded/purchased)
        $popularBooks = Book::where('status', 'approved')
            ->orderBy('downloads', 'desc')
            ->limit(5)
            ->get(['title', 'downloads']);

        // Top users (most quizzes passed)
        $topUsers = User::withCount(['quizAttempts as quizzes_passed' => function($query) {
                $query->where('passed', true);
            }])
            ->orderBy('quizzes_passed', 'desc')
            ->limit(5)
            ->get(['id', 'full_name', 'email']);

        // Recent activities
        $recentActivities = collect();

        // Recent user registrations
        $recentUsers = User::latest()->limit(5)->get()->map(function($user) {
            return (object)[
                'type' => 'user_registered',
                'message' => "New user registered: {$user->full_name}",
                'time' => $user->created_at,
                'icon' => 'ti-user-plus',
                'color' => 'green'
            ];
        });

        // Recent purchases
        $recentPurchases = Payment::with('user')->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return (object)[
                    'type' => 'purchase',
                    'message' => "{$payment->user->full_name} purchased a book for TSh " . number_format($payment->amount, 2),
                    'time' => $payment->created_at,
                    'icon' => 'ti-shopping-cart',
                    'color' => 'purple'
                ];
            });

        // Recent quiz completions
        $recentQuizzes = QuizAttempt::with('user', 'quiz')
            ->where('passed', true)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($attempt) {
                return (object)[
                    'type' => 'quiz_passed',
                    'message' => "{$attempt->user->full_name} passed '{$attempt->quiz->title}' with {$attempt->percentage}%",
                    'time' => $attempt->created_at,
                    'icon' => 'ti-brain',
                    'color' => 'orange'
                ];
            });

        $recentActivities = $recentUsers->concat($recentPurchases)->concat($recentQuizzes)
            ->sortByDesc('time')
            ->take(10);

        // Sales by book type (free vs paid)
        $freeBooksSales = Book::where('is_paid', false)->count();
        $paidBooksSales = Book::where('is_paid', true)->count();

        return view('admin.analytics', compact(
            'totalUsers', 'totalBooks', 'totalSales', 'totalQuizzesTaken',
            'totalCertificates', 'marketplaceListings', 'pendingApprovals',
            'salesData', 'userGrowthData', 'popularBooks', 'topUsers',
            'recentActivities', 'freeBooksSales', 'paidBooksSales'
        ));
    }

    // API endpoint for AJAX data refresh
    public function getData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        
        if ($period === 'weekly') {
            $salesData = Payment::where('status', 'completed')
                ->select(DB::raw('DAYOFWEEK(created_at) as day'), DB::raw('SUM(amount) as total'))
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->pluck('total', 'day')
                ->toArray();
                
            $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $data = [];
            for ($i = 1; $i <= 7; $i++) {
                $data[] = $salesData[$i] ?? 0;
            }
        } else {
            // Monthly data
            $monthlySales = Payment::where('status', 'completed')
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $data[] = $monthlySales[$i] ?? 0;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    // Helper method to log activities (to be called from other controllers)
public static function logActivity($userId, $action, $description, $entityType = null, $entityId = null)
{
    return ActivityLog::log($userId, $action, $description, $entityType, $entityId);
}
}