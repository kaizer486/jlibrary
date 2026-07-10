<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
{
    $query = User::query();
    
    // ==========================================
    // PRIVACY FILTER: Super Admin can ONLY see users with NO institution
    // ==========================================
    $query->whereNull('institution_id');
    
    // Search
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
    
    // Sort
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
    
    // Stats (only users with no institution)
    $totalUsers = User::whereNull('institution_id')->count();
    $superAdminCount = User::where('role', 'super_admin')->whereNull('institution_id')->count();
    $adminCount = User::where('role', 'admin')->whereNull('institution_id')->count();
    $userCount = User::where('role', 'user')->whereNull('institution_id')->count();
    $institutionAdminCount = User::where('role', 'institution_admin')->whereNull('institution_id')->count();
    
    $institutions = Institution::where('status', 'approved')->get();
    
    return view('super-admin.users.index', compact(
        'users', 'totalUsers', 'superAdminCount', 'adminCount', 
        'institutionAdminCount', 'userCount', 'institutions'
    ));
}  
    public function create()
    {
        $institutions = Institution::where('status', 'approved')->get();
        return view('super-admin.users.create', compact('institutions'));
    }
    
    public function store(Request $request)
{
    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => ['required', Rule::in(['user', 'admin', 'institution_admin', 'super_admin'])],
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
    
    // ==========================================
    // ASSIGN ROLE USING SPATIE
    // ==========================================
    $user->assignRole($request->role);  
    
    return redirect()->route('super-admin.users.index')->with('success', 'User created successfully!');
}    
    public function show(User $user)
    {
        $user->load(['books', 'certificates', 'institution']);
        return view('super-admin.users.show', compact('user'));
    }
    
    public function edit(User $user)
    {
        $institutions = Institution::where('status', 'approved')->get();
        return view('super-admin.users.edit', compact('user', 'institutions'));
    }
    
    public function update(Request $request, User $user)
{
    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => ['required', Rule::in(['user', 'admin', 'institution_admin', 'super_admin'])],
        'institution_id' => 'nullable|exists:institutions,id',
        'is_institution_admin' => 'boolean',
    ]);
    
    $user->full_name = $request->full_name;
    $user->email = $request->email;
    $user->role = $request->role;
    
    // Handle institution assignment
    if ($request->role === 'institution_admin' || $request->is_institution_admin) {
        $user->institution_id = $request->institution_id;
        $user->is_institution_admin = $request->has('is_institution_admin');
    } else {
        $user->institution_id = null;
        $user->is_institution_admin = false;
    }
    
    $user->save();
    
    // ==========================================
    // SYNC SPATIE ROLE
    // ==========================================
    $user->syncRoles([$request->role]);  // Add this line!
    
    // Handle password update if provided
    if ($request->filled('password')) {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        $user->password = Hash::make($request->password);
        $user->save();
    }
    
    return redirect()->route('super-admin.users.index')->with('success', 'User updated successfully!');
}
    public function destroy(User $user)
    {
        // Cannot delete yourself
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('super-admin.users.index')->with('success', 'User deleted successfully!');
    }
}