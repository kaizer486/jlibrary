<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureInstitutionLoaded
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user && $user->role === 'institution_admin') {
            // Force load the institution relationship
            if (!$user->relationLoaded('institution')) {
                $user->load('institution');
            }
        }
        
        return $next($request);
    }
}