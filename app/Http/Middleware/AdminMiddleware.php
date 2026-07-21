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
        
       
        
        // Check if user has admin or super_admin role using Spatie
        if ($user->hasRole(['admin', 'super_admin'])) {
            return $next($request);
        }
        
        // If user doesn't have the role, try checking the role column
        if ($user->role === 'admin' || $user->role === 'superadmin') {
            return $next($request);
        }
        
        abort(403, 'Admin only. You do not have permission to access this page.');
    }
}