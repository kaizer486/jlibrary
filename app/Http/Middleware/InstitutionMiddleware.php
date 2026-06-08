<?php

namespace App\Http\Middleware;

use Closure;

class InstitutionMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Super Admin and Admin pass freely
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return $next($request);
        }

        // Institution Admin must exist and have an institution
        if ($user->hasRole('institution_admin')) {
            if (!$user->institution_id) {
                abort(403, 'You are not associated with any institution.');
            }
            return $next($request); // ✅ explicit pass
        }

        // Librarian must have an institution
        if ($user->hasRole('librarian')) {
            if (!$user->institution_id) {
                abort(403, 'You are not associated with any institution.');
            }
            return $next($request); // ✅ explicit pass
        }

        // Everyone else is denied
        abort(403, 'You do not have permission to access this page.');
    }
}