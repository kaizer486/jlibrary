<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\JoinRequest;
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
            'started_at' => $institution->subscription_started_at,
            'expires_at' => $institution->subscription_expires_at,
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
}