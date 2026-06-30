<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // First, try the current single institution (legacy)
        $hasInstitution = $user->institution_id && $user->institution;
        
        // If not, check the new multiple institutions relationship
        if (!$hasInstitution) {
            $hasInstitution = $user->institutions()->count() > 0;
        }

        if (!$hasInstitution) {
            abort(403, 'You do not belong to any institution.');
        }

        return $next($request);
    }
}