<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institution;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // TOP LEARNERS LEADERBOARD (FIXED)
        // ==========================================
        $topLearners = User::where('role', 'user')
            ->withCount('certificates')
            ->get()
            ->map(function($user) {
                // Manually count completed books (fixed: no pivot column issue)
                $user->books_completed_count = $user->books()
                    ->wherePivot('status', 'completed')
                    ->count();
                $user->quiz_avg = round($user->quizAttempts()->avg('percentage') ?? 0);
                $user->combined_score = $user->combined_score;
                return $user;
            })
            ->sortByDesc('combined_score')
            ->take(10)
            ->values();
        
        // Get current user's rank
        $allUsers = User::where('role', 'user')->get()->map(function($user) {
            return (object) [
                'id' => $user->id,
                'combined_score' => $user->combined_score
            ];
        })->sortByDesc('combined_score')->values();
        
        $currentUserRank = $allUsers->search(function($user) {
            return $user->id == auth()->id();
        });
        
        $currentUserRank = $currentUserRank !== false ? $currentUserRank + 1 : 1;
        
        // ==========================================
        // INSTITUTIONS SHOWCASE DATA
        // ==========================================
        $availableInstitutions = Institution::where('status', 'approved')
            ->withCount('users')
            ->get();
            
        $userRequests = JoinRequest::where('user_id', auth()->id())
            ->get()
            ->keyBy('institution_id');
        
        return view('dashboard', compact(
            'topLearners', 
            'currentUserRank', 
            'availableInstitutions',
            'userRequests'
        ));
    }
}