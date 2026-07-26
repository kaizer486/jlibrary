<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        // DEBUG: Log the user's roles to see what's happening
        Log::info('AdminMiddleware - User: ' . $user->email . ' | Roles: ' . json_encode($user->getRoleNames()->toArray()) . ' | Legacy role: ' . ($user->role ?? 'null'));
        
        // FIX: Include all admin-level roles that should access admin panel
        $adminRoles = ['admin', 'super_admin', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller', 'publisher', 'media_team'];
        
        // Check 1: Spatie roles
        if ($user->hasAnyRole($adminRoles)) {
            Log::info('AdminMiddleware - Access GRANTED via Spatie roles');
            return $next($request);
        }
        
        // Check 2: Legacy role column (for backward compatibility)
        if (in_array($user->role, ['admin', 'super_admin', 'superadmin'])) {
            Log::info('AdminMiddleware - Access GRANTED via legacy role column');
            
            // FIX: If user has admin in legacy column but not in Spatie, sync it
            if (!$user->hasRole($user->role)) {
                Log::info('AdminMiddleware - Syncing legacy role to Spatie: ' . $user->role);
                $user->assignRole($user->role);
                $user->refresh();
            }
            
            return $next($request);
        }
        
        // Check 3: If user has any roles but not admin/super_admin
        $roles = $user->getRoleNames()->toArray();
        Log::warning('AdminMiddleware - Access DENIED for user: ' . $user->email . ' | Roles: ' . json_encode($roles));
        
        abort(403, 'Admin only. You do not have permission to access this page.');
    }
}