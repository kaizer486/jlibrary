<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // Allow super_admin, admin, AND institution_admin to access admin panel
        if ($user->isSuperAdmin() || $user->isAdmin() || $user->isInstitutionAdmin()) {
            return $next($request);
        }
        
        abort(403, 'Admin only. You do not have permission to access this page.');
    }
}