<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->get('timeframe', 'all_time');
        
        $query = User::where('role', 'user')
            ->withCount('certificates');
        
        // Apply timeframe filter
        if ($timeframe == 'this_month') {
            $query->whereMonth('last_active_at', now()->month);
        } elseif ($timeframe == 'this_week') {
            $query->whereBetween('last_active_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }
        
        // Get ALL learners for full page (not just top 10)
        $limit = $request->has('full') ? 100 : 10;
        
        $topLearners = $query->get()
            ->map(function($user) {
                $user->books_completed_count = $user->books()
                    ->wherePivot('status', 'completed')
                    ->count();
                $user->quiz_avg = round($user->quizAttempts()->avg('percentage') ?? 0);
                $user->combined_score = $user->combined_score;
                $user->level_progress = $user->level_progress;
                $user->xp_points = $user->xp_points ?? 0;
                $user->level = $user->level ?? 1;
                $user->streak_days = $user->streak_days ?? 0;
                return $user;
            })
            ->sortByDesc('combined_score')
            ->take($limit)
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
        
        // For AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'learners' => $topLearners,
                'current_user_rank' => $currentUserRank
            ]);
        }
        
        // For full page view
        if ($request->has('full') || request()->routeIs('leaderboard.index')) {
            return view('leaderboard.index', compact('topLearners', 'currentUserRank', 'timeframe'));
        }
        
        return view('components.leaderboard', compact('topLearners', 'currentUserRank', 'timeframe'));
    }
}