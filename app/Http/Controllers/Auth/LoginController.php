<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * ✅ OVERRIDE: Force "Remember Me" to ALWAYS be false
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // ✅ ALWAYS FALSE - Ignore the "Remember Me" checkbox
        return $this->guard()->attempt(
            $credentials,
            false  // Never remember the user
        );
    }

    /**
     * Handle post-authentication redirect based on user role.
     */
    protected function authenticated(Request $request, $user)
    {
        // ✅ Clear any existing remember token on login
        if ($user->remember_token) {
            $user->remember_token = null;
            $user->save();
        }

        // Role-based redirect
        if ($user->hasRole('super_admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('institution_admin')) {
            return redirect()->route('institution.dashboard');
        }
        if ($user->hasRole('librarian')) {
            return redirect()->route('librarian.dashboard');
        }
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }
        if ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        }
        return redirect()->route('dashboard');
    }

    /**
     * ✅ OVERRIDE: Logout and clear everything
     */
    public function logout(Request $request)
    {
        $user = $this->guard()->user();

        // ✅ Clear the remember token on logout
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        $this->guard()->logout();

        // ✅ Invalidate session
        $request->session()->invalidate();
        
        // ✅ Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}