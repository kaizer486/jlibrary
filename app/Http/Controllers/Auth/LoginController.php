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

    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        return $this->guard()->attempt(
            $credentials,
            false
        );
    }

    /**
     * Handle post-authentication redirect based on user role.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->remember_token) {
            $user->remember_token = null;
            $user->save();
        }

        // ==========================================
        // 👑 SUPER ADMIN
        // ==========================================
        if ($user->hasRole('super_admin')) {
            return redirect()->route('super-admin.dashboard');
        }
        
        // ==========================================
        // 🛡️ ADMIN
        // ==========================================
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // ==========================================
        // 🏢 INSTITUTION ADMIN - GO TO ADMIN PANEL
        // ==========================================
        if ($user->hasRole('institution_admin') || $user->is_institution_admin) {
            return redirect()->route('institution.dashboard');
        }

        // ==========================================
        // 📚 LIBRARIAN - GO TO ADMIN PANEL
        // ==========================================
        if ($user->hasRole('librarian')) {
            return redirect()->route('librarian.dashboard');
        }

        // ==========================================
        // 👨‍🏫 INSTRUCTOR
        // ==========================================
        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }

        // ==========================================
        // ✍️ AUTHOR
        // ==========================================
        if ($user->hasRole('author')) {
            return redirect()->route('author.dashboard');
        }

        // ==========================================
        // 👤 NORMAL USER - Public Library Page
        // ==========================================
        $institution = $user->institution;
        
        if ($institution) {
            return redirect()->route('institution.public.index', $institution->id);
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = $this->guard()->user();

        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function redirectTo()
    {
        $user = auth()->user();
        
        if (!$user) {
            return route('dashboard');
        }
        
        if ($user->hasRole('super_admin')) {
            return route('super-admin.dashboard');
        }
        
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        
        if ($user->hasRole('institution_admin') || $user->is_institution_admin) {
            return route('institution.dashboard');
        }
        
        if ($user->hasRole('librarian')) {
            return route('librarian.dashboard');
        }
        
        if ($user->hasRole('instructor')) {
            return route('instructor.dashboard');
        }
        
        if ($user->hasRole('author')) {
            return route('author.dashboard');
        }
        
        $institution = $user->institution;
        
        if ($institution) {
            return route('institution.public.index', $institution->id);
        }
        
        return route('dashboard');
    }
}