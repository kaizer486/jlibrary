<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Institution;

class UserController extends Controller
{
    public function index(Request $request)
{
    $query = User::query();
    
    // ==========================================
    // PRIVACY FILTER: Hide institution members from Admin
    // ==========================================
    // Admin can ONLY see users with NO institution (strangers)
    $query->whereNull('institution_id');
    
    // Search functionality
    if ($request->search) {
        $query->where(function($q) use ($request) {
            $q->where('full_name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }
    
    // Filter by role (only roles that can exist without institution)
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
    
    // Stats for cards (only count users with no institution)
    $totalUsers = User::whereNull('institution_id')->count();
    $superAdminCount = User::where('role', 'super_admin')->whereNull('institution_id')->count();
    $adminCount = User::where('role', 'admin')->whereNull('institution_id')->count();
    $userCount = User::where('role', 'user')->whereNull('institution_id')->count();
    
    return view('admin.users.index', compact('users', 'totalUsers', 'superAdminCount', 'adminCount', 'userCount'));
}   
    public function show(User $user)
    {
        $user->load(['books', 'certificates']);
        return view('admin.users.show', compact('user'));
    }
    
    public function create()
    {
        $institutions = Institution::where('status', 'approved')->get();
        return view('admin.users.create', compact('institutions'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['user', 'admin', 'institution_admin'])],
            'institution_id' => 'nullable|exists:institutions,id',
            'is_institution_admin' => 'boolean',
        ]);
        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'wallet_balance' => 0,
            'referral_code' => User::generateReferralCode(),
            'institution_id' => ($request->role === 'institution_admin' || $request->is_institution_admin) ? $request->institution_id : null,
            'is_institution_admin' => ($request->role === 'institution_admin' || $request->is_institution_admin) ? $request->has('is_institution_admin') : false,
        ]);
        
        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }
    
    public function edit(User $user)
    {
        // Check permission
        if (auth()->user()->isAdmin() && !$user->isUser()) {
            abort(403, 'You cannot edit admin or super admin users.');
        }
        
        $institutions = Institution::where('status', 'approved')->get();
        return view('admin.users.edit', compact('user', 'institutions'));
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
            'role' => ['required', Rule::in(['user', 'admin', 'institution_admin', 'super_admin'])],
            'institution_id' => 'nullable|exists:institutions,id',
            'is_institution_admin' => 'boolean',
        ]);
        
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        
        // Only super admin can change roles
        if (auth()->user()->isSuperAdmin()) {
            $user->role = $request->role;
        }
        
        // Handle institution assignment
        if ($request->role === 'institution_admin' || $request->is_institution_admin) {
            $user->institution_id = $request->institution_id;
            $user->is_institution_admin = $request->has('is_institution_admin');
        } else {
            $user->institution_id = null;
            $user->is_institution_admin = false;
        }
        
        $user->save();
        
        // Handle password update if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }
        
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