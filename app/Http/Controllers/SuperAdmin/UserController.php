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
    // Define all available roles
    protected $availableRoles = [
        'super_admin' => '👑 Super Admin',
        'admin' => '🛡️ Administrator',
        'media_team' => '🎨 Media Team',
        'institution_admin' => '🏢 Institution Admin',
        'school_admin' => '🏫 School Admin',
        'college_admin' => '🎓 College Admin',
        'university_admin' => '🏛️ University Admin',
        'library_admin' => '📚 Library Admin',
        'bookstore_admin' => '📖 Bookstore Admin',
        'publisher_admin' => '📰 Publisher Admin',
        'librarian' => '📚 Librarian',
        'instructor' => '👨‍🏫 Instructor',
        'researcher' => '🔬 Researcher',
        'author' => '✍️ Author',
        'user' => '👤 Member',
    ];

    public function index(Request $request)
    {
        $query = User::query();
        
        // ==========================================
        // FIXED: Show ALL users with filter options
        // ==========================================
        
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
        
        // Filter by institution (new)
        if ($request->institution_id && $request->institution_id !== 'all') {
            $query->where('institution_id', $request->institution_id);
        }
        
        // Filter by institution assignment status (new)
        if ($request->institution_status === 'with_institution') {
            $query->whereNotNull('institution_id');
        } elseif ($request->institution_status === 'without_institution') {
            $query->whereNull('institution_id');
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
        
        // Stats - ALL users
        $totalUsers = User::count();
        $superAdminCount = User::where('role', 'super_admin')->count();
        $adminCount = User::where('role', 'admin')->count();
        $userCount = User::where('role', 'user')->count();
        $institutionAdminCount = User::where('role', 'institution_admin')->count();
        $withInstitutionCount = User::whereNotNull('institution_id')->count();
        $withoutInstitutionCount = User::whereNull('institution_id')->count();
        
        // Role counts for all roles
        $roleCounts = [];
        foreach (array_keys($this->availableRoles) as $role) {
            $roleCounts[$role] = User::where('role', $role)->count();
        }
        
        $institutions = Institution::where('status', 'approved')->get();
        
        return view('super-admin.users.index', compact(
            'users', 
            'totalUsers', 
            'superAdminCount', 
            'adminCount', 
            'institutionAdminCount', 
            'userCount',
            'withInstitutionCount',
            'withoutInstitutionCount',
            'roleCounts',
            'institutions'
        ));
    }
    
    public function create()
    {
        $institutions = Institution::where('status', 'approved')->get();
        $availableRoles = $this->availableRoles;
        return view('super-admin.users.create', compact('institutions', 'availableRoles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(array_keys($this->availableRoles))],
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
            'institution_id' => $this->shouldAssignInstitution($request) ? $request->institution_id : null,
            'is_institution_admin' => $this->shouldAssignInstitution($request) ? $request->has('is_institution_admin') : false,
        ]);
        
        // Assign role using Spatie
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
        $availableRoles = $this->availableRoles;
        return view('super-admin.users.edit', compact('user', 'institutions', 'availableRoles'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['required', Rule::in(array_keys($this->availableRoles))],
            'institution_id' => 'nullable|exists:institutions,id',
            'is_institution_admin' => 'boolean',
        ]);
        
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->role = $request->role;
        
        // Handle institution assignment based on role
        if ($this->shouldAssignInstitution($request)) {
            $user->institution_id = $request->institution_id;
            $user->is_institution_admin = $request->has('is_institution_admin');
        } else {
            $user->institution_id = null;
            $user->is_institution_admin = false;
        }
        
        $user->save();
        
        // Sync Spatie role
        $user->syncRoles([$request->role]);
        
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
    
    /**
     * Check if institution should be assigned based on role
     */
    protected function shouldAssignInstitution($request): bool
    {
        $institutionRoles = [
            'institution_admin', 
            'school_admin', 
            'college_admin', 
            'university_admin',
            'library_admin',
            'bookstore_admin',
            'publisher_admin',
            'librarian',
            'instructor'
        ];
        
        return in_array($request->role, $institutionRoles) || $request->has('is_institution_admin');
    }
}