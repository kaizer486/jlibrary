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
        
        // Super Admin and Admin can access everything
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return $next($request);
        }
        
        // Institution Admin and Librarian need to belong to an institution
        if (($user->isInstitutionAdmin() || $user->isLibrarian()) && !$user->hasInstitution()) {
            abort(403, 'You are not associated with any institution.');
        }
        
        return $next($request);
    }
}