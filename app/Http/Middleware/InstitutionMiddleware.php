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
        
        // Super Admin, Admin, and Institution Admin with institution_id can access
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return $next($request);
        }
        
        // Institution Admin must have an institution_id
        if ($user->hasRole('institution_admin') && !$user->institution_id) {
            abort(403, 'You are not associated with any institution.');
        }
        
        // Librarian must have an institution_id
        if ($user->hasRole('librarian') && !$user->institution_id) {
            abort(403, 'You are not associated with any institution.');
        }
        
        return $next($request);
    }
}