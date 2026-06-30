<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionMemberMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is logged in
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user belongs to an institution
        if (!$user->institution_id || !$user->institution) {
            abort(403, 'You do not belong to any institution. Please join or create an institution first.');
        }

        return $next($request);
    }
}