<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaTeamMiddleware
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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Allow Super Admin or Media Team
        if ($user->isSuperAdmin() || $user->isMediaTeam()) {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Media Team or Super Admin access required.');
    }
}