<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get top learners for widget - FIXED VERSION
        $topLearners = User::where('role', 'user')
            ->withCount('certificates')
            ->get()
            ->map(function($user) {
                // Manually count completed books
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
        
        // If user not found in top 10, add 1 to get proper rank
        $currentUserRank = $currentUserRank !== false ? $currentUserRank + 1 : 1;
        
        return view('dashboard', compact('topLearners', 'currentUserRank'));
    }
}