<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ==========================================
        // ROLE-BASED REDIRECT AFTER LOGIN
        // ==========================================
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return redirect()->intended(route('super-admin.dashboard'));
        }
        
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        if ($user->hasRole('institution_admin')) {
            return redirect()->intended(route('institution.dashboard'));
        }
        
        if ($user->hasRole('author')) {
            return redirect()->intended(route('author.dashboard'));
        }
        
        if ($user->hasRole('librarian')) {
            return redirect()->intended(route('librarian.dashboard'));
        }
        
        if ($user->hasRole('instructor')) {
            return redirect()->intended(route('instructor.dashboard'));
        }
        
        // Default redirect for regular users
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}