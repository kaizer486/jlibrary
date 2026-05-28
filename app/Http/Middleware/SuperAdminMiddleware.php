<?php

namespace App\Http\Middleware;

use Closure;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Super Admin access required.');
        }
        
        return $next($request);
    }
}