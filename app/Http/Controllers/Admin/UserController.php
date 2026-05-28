<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by role
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        // Sort options
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('full_name', 'desc');
                break;
            default:
                $query->latest();
        }
        
        $users = $query->paginate(15);
        
        // Stats for cards
        $totalUsers = User::count();
        $superAdminCount = User::where('role', 'super_admin')->count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        
        return view('admin.users.index', compact('users', 'totalUsers', 'superAdminCount', 'adminCount', 'userCount'));
    }
    
    public function show(User $user)
    {
        $user->load(['books', 'certificates']);
        return view('admin.users.show', compact('user'));
    }
    
    public function create()
    {
        return view('admin.users.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'admin'])], // Can't create super_admin directly
        ]);
        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'wallet_balance' => 0,
            'referral_code' => User::generateReferralCode(),
        ]);
        
        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }
    
    public function edit(User $user)
    {
        // Check permission - only super admin can edit admin/super_admin roles
        if (auth()->user()->isAdmin() && !$user->isUser()) {
            abort(403, 'You cannot edit admin or super admin users.');
        }
        
        return view('admin.users.edit', compact('user'));
    }
    
    public function update(Request $request, User $user)
    {
        // Check permission
        if (auth()->user()->isAdmin() && !$user->isUser()) {
            abort(403, 'You cannot edit admin or super admin users.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['required', Rule::in(['user', 'admin', 'super_admin'])],
        ]);
        
        // Only super admin can change roles or upgrade to super_admin
        if (auth()->user()->isSuperAdmin()) {
            $user->role = $request->role;
        } elseif (auth()->user()->isAdmin()) {
            // Admin can only edit regular users, cannot change role
            $user->full_name = $request->full_name;
            $user->email = $request->email;
            // Keep existing role
        }
        
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->save();
        
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }
    
    public function toggleRole(User $user)
    {
        // Only super admin can toggle roles
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only Super Admin can change user roles.');
        }
        
        // Cannot change own role (prevent locking yourself out)
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }
        
        // Toggle between user and admin only (can't demote super_admin via toggle)
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin role cannot be changed via toggle.');
        }
        
        $user->role = $user->isAdmin() ? 'user' : 'admin';
        $user->save();
        
        return redirect()->back()->with('success', "User role updated to {$user->role}!");
    }
    
    public function destroy(User $user)
    {
        // Cannot delete yourself
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        // Check permission
        if (!auth()->user()->canDeleteUser($user)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this user.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}