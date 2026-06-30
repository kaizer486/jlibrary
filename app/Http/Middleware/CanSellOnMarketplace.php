<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanSellOnMarketplace
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            abort(401, 'Please log in first.');
        }

        $user = auth()->user();
        
        // Check if user has either author or bookseller role
        if (!$user->hasRole('author') && !$user->hasRole('bookseller')) {
            abort(403, 'You need to be an approved Author or Bookseller to access this page.');
        }

        return $next($request);
    }
}