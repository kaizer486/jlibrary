<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();

                // ==========================================
                // ROLE-BASED REDIRECT FOR ALREADY LOGGED IN USERS
                // ==========================================
                
                if ($user->hasRole('super_admin')) {
                    return redirect()->route('super-admin.dashboard');
                }

                if ($user->hasRole('admin')) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->hasRole('institution_admin')) {
                    return redirect()->route('institution.dashboard');
                }

                if ($user->hasRole('librarian')) {
                    return redirect()->route('librarian.dashboard');
                }

                if ($user->hasRole('instructor')) {
                    return redirect()->route('instructor.dashboard');
                }

                if ($user->hasRole('author')) {
                    return redirect()->route('author.dashboard');
                }

                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}