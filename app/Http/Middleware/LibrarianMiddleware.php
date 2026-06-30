<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibrarianMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // ✅ ALLOW: Librarian OR Institution Admin
        if ($user->hasRole('librarian') || $user->hasRole('institution_admin')) {
            return $next($request);
        }

        // ✅ ALSO CHECK: role column (legacy)
        if ($user->role === 'librarian' || $user->role === 'institution_admin') {
            return $next($request);
        }

        abort(403, 'Access denied. Librarian or Institution Admin role required.');
    }
}