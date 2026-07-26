<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Institution;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        $authUser = auth()->user();
        
        // Admin can ONLY see users with NO institution and NOT super_admin
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin()) {
            $query->whereNull('institution_id')
                  ->whereDoesntHave('roles', function($q) {
                      $q->where('name', 'super_admin');
                  });
        }
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by role (using Spatie roles)
        if ($request->role && $request->role !== 'all') {
            if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $request->role === 'super_admin') {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }
        }
        
        // Filter by institution status
        if ($request->institution_status && $request->institution_status !== 'all') {
            if ($request->institution_status === 'with_institution') {
                $query->whereNotNull('institution_id');
            } elseif ($request->institution_status === 'without_institution') {
                $query->whereNull('institution_id');
            }
        }
        
        // Filter by institution
        if ($request->institution_id && $request->institution_id !== 'all') {
            $query->where('institution_id', $request->institution_id);
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
        
        // Stats
        if ($authUser->isSuperAdmin()) {
            $totalUsers = User::count();
            $superAdminCount = User::role('super_admin')->count();
            $adminCount = User::role('admin')->count();
            $institutionAdminCount = User::role('institution_admin')->count();
            $withInstitutionCount = User::whereNotNull('institution_id')->count();
            $withoutInstitutionCount = User::whereNull('institution_id')->count();
            $userCount = User::role('user')->count();
        } else {
            $totalUsers = User::whereNull('institution_id')
                              ->whereDoesntHave('roles', function($q) {
                                  $q->where('name', 'super_admin');
                              })->count();
            $superAdminCount = 0;
            $adminCount = User::role('admin')->whereNull('institution_id')->count();
            $institutionAdminCount = User::role('institution_admin')->whereNull('institution_id')->count();
            $withInstitutionCount = 0;
            $withoutInstitutionCount = User::whereNull('institution_id')
                                          ->whereDoesntHave('roles', function($q) {
                                              $q->where('name', 'super_admin');
                                          })->count();
            $userCount = User::role('user')->whereNull('institution_id')->count();
        }
        
        $institutions = Institution::where('status', 'approved')->get();
        
        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'superAdminCount',
            'adminCount',
            'institutionAdminCount',
            'withInstitutionCount',
            'withoutInstitutionCount',
            'userCount',
            'institutions'
        ));
    }
    
    public function show(User $user)
    {
        $authUser = auth()->user();
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->hasRole('super_admin')) {
            abort(403, 'You do not have permission to view this user.');
        }
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->institution_id !== null) {
            abort(403, 'You do not have permission to view institution-affiliated users.');
        }
        
        $user->load(['books', 'certificates']);
        return view('admin.users.show', compact('user'));
    }
    
    public function create()
    {
        $authUser = auth()->user();
        
        if ($authUser->isSuperAdmin()) {
            $availableRoles = [
                'user' => '👤 Member',
                'admin' => '🛡️ Admin',
                'super_admin' => '👑 Super Admin',
                'institution_admin' => '🏢 Institution Admin',
                'librarian' => '📚 Librarian',
                'instructor' => '👨‍🏫 Instructor',
                'author' => '✍️ Author',
                'researcher' => '🔬 Researcher',
                'bookseller' => '📖 Bookseller',
                'publisher' => '📰 Publisher',
                'media_team' => '🎨 Media Team',
            ];
        } else {
            $availableRoles = [
                'user' => '👤 Member',
                'institution_admin' => '🏢 Institution Admin',
                'librarian' => '📚 Librarian',
                'instructor' => '👨‍🏫 Instructor',
                'author' => '✍️ Author',
                'researcher' => '🔬 Researcher',
                'bookseller' => '📖 Bookseller',
            ];
        }
        
        $institutions = Institution::where('status', 'approved')->get();
        return view('admin.users.create', compact('institutions', 'availableRoles'));
    }
    
    public function store(Request $request)
    {
        $authUser = auth()->user();
        
        $allowedRoles = ['user', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller'];
        
        if ($authUser->isSuperAdmin()) {
            $allowedRoles = ['user', 'admin', 'super_admin', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller', 'publisher', 'media_team'];
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'institution_id' => 'nullable|exists:institutions,id',
            'is_institution_admin' => 'boolean',
        ]);
        
        $role = $request->role;
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin()) {
            if (in_array($role, ['admin', 'super_admin', 'publisher', 'media_team'])) {
                abort(403, 'You do not have permission to create this role.');
            }
        }
        
        $institutionId = null;
        $isInstitutionAdmin = false;
        $needsInstitution = in_array($role, ['institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller']);
        
        if ($needsInstitution && $request->institution_id) {
            $institutionId = $request->institution_id;
            $isInstitutionAdmin = $role === 'institution_admin' || $request->has('is_institution_admin');
        } elseif (in_array($role, ['admin', 'super_admin', 'publisher', 'media_team'])) {
            $institutionId = null;
            $isInstitutionAdmin = false;
        }
        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'wallet_balance' => 0,
            'referral_code' => User::generateReferralCode(),
            'institution_id' => $institutionId,
            'is_institution_admin' => $isInstitutionAdmin,
            'role' => $role,
        ]);
        
        // Assign Spatie role
        $user->assignRole($role);
        
        // FIX: Auto-approve seller roles when assigned by admin
        $this->approveSellerRole($user, $role, $authUser);
        
        if ($institutionId && $isInstitutionAdmin) {
            $user->addToInstitution($institutionId, 'admin');
        } elseif ($institutionId) {
            $user->addToInstitution($institutionId, 'member');
        }
        
        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }
    
    public function edit(User $user)
    {
        $authUser = auth()->user();
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->hasRole('super_admin')) {
            abort(403, 'You do not have permission to edit this user.');
        }
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->institution_id !== null) {
            abort(403, 'You do not have permission to edit institution-affiliated users.');
        }
        
        if ($authUser->isSuperAdmin()) {
            $availableRoles = [
                'user' => '👤 Member',
                'admin' => '🛡️ Admin',
                'super_admin' => '👑 Super Admin',
                'institution_admin' => '🏢 Institution Admin',
                'librarian' => '📚 Librarian',
                'instructor' => '👨‍🏫 Instructor',
                'author' => '✍️ Author',
                'researcher' => '🔬 Researcher',
                'bookseller' => '📖 Bookseller',
                'publisher' => '📰 Publisher',
                'media_team' => '🎨 Media Team',
            ];
        } else {
            $availableRoles = [
                'user' => '👤 Member',
                'institution_admin' => '🏢 Institution Admin',
                'librarian' => '📚 Librarian',
                'instructor' => '👨‍🏫 Instructor',
                'author' => '✍️ Author',
                'researcher' => '🔬 Researcher',
                'bookseller' => '📖 Bookseller',
            ];
        }
        
        $institutions = Institution::where('status', 'approved')->get();
        return view('admin.users.edit', compact('user', 'institutions', 'availableRoles'));
    }
    
    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->hasRole('super_admin')) {
            abort(403, 'You do not have permission to update this user.');
        }
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->institution_id !== null) {
            abort(403, 'You do not have permission to update institution-affiliated users.');
        }
        
        $allowedRoles = ['user', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller'];
        
        if ($authUser->isSuperAdmin()) {
            $allowedRoles = ['user', 'admin', 'super_admin', 'institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller', 'publisher', 'media_team'];
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['required', Rule::in($allowedRoles)],
            'institution_id' => 'nullable|exists:institutions,id',
            'is_institution_admin' => 'boolean',
        ]);
        
        $newRole = $request->role;
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin()) {
            if (in_array($newRole, ['admin', 'super_admin', 'publisher', 'media_team'])) {
                abort(403, 'You do not have permission to assign this role.');
            }
        }
        
        if (!$authUser->isSuperAdmin() && ($user->hasRole('super_admin') || $newRole === 'super_admin')) {
            abort(403, 'You do not have permission to manage super_admin users.');
        }
        
        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->role = $newRole;
        
        $needsInstitution = in_array($newRole, ['institution_admin', 'librarian', 'instructor', 'author', 'researcher', 'bookseller']);
        
        if ($needsInstitution && $request->institution_id) {
            $user->institution_id = $request->institution_id;
            $user->is_institution_admin = $newRole === 'institution_admin' || $request->has('is_institution_admin');
        } elseif (in_array($newRole, ['admin', 'super_admin', 'publisher', 'media_team'])) {
            $user->institution_id = null;
            $user->is_institution_admin = false;
        } else {
            $user->institution_id = $request->institution_id;
            $user->is_institution_admin = $request->has('is_institution_admin');
        }
        
        // Sync role if it's in the allowed list
        if (in_array($newRole, $allowedRoles)) {
            $user->syncRoles([$newRole]);
        }
        
        $user->save();
        
        // FIX: Auto-approve seller roles when assigned/changed by admin
        $this->approveSellerRole($user, $newRole, $authUser);
        
        if ($needsInstitution && $request->institution_id) {
            $roleInInstitution = $newRole === 'institution_admin' ? 'admin' : 'member';
            $user->addToInstitution($request->institution_id, $roleInInstitution);
        }
        
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
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Only Super Admin can change user roles.');
        }
        
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }
        
        if ($user->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Super Admin role cannot be changed via toggle.');
        }
        
        if ($user->institution_id !== null) {
            return redirect()->back()->with('error', 'Cannot toggle role for institution-affiliated users.');
        }
        
        $newRole = $user->hasRole('admin') ? 'user' : 'admin';
        $user->syncRoles([$newRole]);
        $user->role = $newRole;
        $user->save();
        
        return redirect()->back()->with('success', "User role updated to {$newRole}!");
    }
    
    public function destroy(User $user)
    {
        $authUser = auth()->user();
        
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'You do not have permission to delete this user.');
        }
        
        if ($authUser->isAdmin() && !$authUser->isSuperAdmin() && $user->institution_id !== null) {
            return redirect()->back()->with('error', 'You do not have permission to delete institution-affiliated users.');
        }
        
        if (!auth()->user()->canDeleteUser($user)) {
            return redirect()->back()->with('error', 'You do not have permission to delete this user.');
        }
        
        $user->syncRoles([]);
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
    
    /**
     * Auto-approve seller roles (author, bookseller) when assigned by admin.
     * Sets approval timestamps and updates/creates Application record.
     */
    private function approveSellerRole(User $user, string $role, User $approvedBy): void
    {
        $sellerRoles = ['author', 'bookseller'];
        
        if (!in_array($role, $sellerRoles)) {
            return;
        }
        
        // Set approval timestamp on user
        $approvalField = $role . '_approved_at';
        $approvedByField = $role . '_approved_by';
        
        if (in_array($approvalField, ['author_approved_at', 'bookseller_approved_at'])) {
            $user->{$approvalField} = now();
            $user->{$approvedByField} = $approvedBy->id;
            $user->save();
        }
        
        // Find or create an Application record and mark it approved
        $application = Application::where('user_id', $user->id)
            ->where('type', $role)
            ->first();
        
        if ($application) {
            // Update existing pending/rejected application to approved
            $application->update([
                'status' => 'approved',
                'reviewed_by' => $approvedBy->id,
                'reviewed_at' => now(),
                'admin_notes' => 'Approved automatically by admin panel assignment.',
            ]);
        } else {
            // Create an approved application record for audit trail
            Application::create([
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'type' => $role,
                'status' => 'approved',
                'reviewed_by' => $approvedBy->id,
                'reviewed_at' => now(),
                'admin_notes' => 'Role assigned and approved automatically via admin panel.',
                'country' => null,
                'country_code' => null,
                'phone' => null,
                'biography' => null,
                'business_name' => null,
                'business_address' => null,
                'tax_id' => null,
            ]);
        }
    }
}