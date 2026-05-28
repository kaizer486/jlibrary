<?php

namespace App\Http\Middleware;

use Closure;

class UpdateUserStreak
{
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            auth()->user()->updateStreak();
        }
        return $next($request);
    }
}