<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionAdminMiddleware
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
            abort(403, 'You do not belong to any institution.');
        }

        // Check if user is an institution admin
        if (!$user->isInstitutionAdmin()) {
            abort(403, 'You do not have permission to access this page. Institution admin access required.');
        }

        return $next($request);
    }
}