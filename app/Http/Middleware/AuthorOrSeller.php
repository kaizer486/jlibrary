<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorOrSeller
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || (!$user->hasRole('author') && !$user->hasRole('seller'))) {
            abort(403, 'Unauthorized. Author or Seller access required.');
        }

        return $next($request);
    }
}