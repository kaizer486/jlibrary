<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        // Only get users from THIS institution
        $query = User::where('institution_id', $institution->id);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->role && $request->role !== 'all') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $members = $query->latest()->paginate(15);
        
        return view('institution.members.index', compact('members', 'institution'));
    }
    
    public function create()
    {
        return view('institution.members.create');
    }
    
    public function directory()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        $members = User::where('institution_id', $institution->id)
            ->with('institution')
            ->get()
            ->groupBy(function($user) {
                return $user->getRoleNames()->first() ?? 'user';
            });
        
        return view('institution.members.directory', compact('institution', 'members'));
    }
    
    public function updateRole(Request $request, User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            return response()->json(['success' => false, 'message' => 'Member does not belong to your institution.'], 403);
        }
        
        $request->validate([
            'role' => 'required|in:librarian,instructor,institution_admin,user'
        ]);
        
        // Prevent promoting to admin or super_admin
        if (in_array($request->role, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Cannot promote to Admin or Super Admin.'], 403);
        }
        
        // Sync the role using Spatie (removes all existing roles and assigns the new one)
        $member->syncRoles([$request->role]);
        
        // Also update the role column for backward compatibility
        $member->role = $request->role;
        
        // If promoting to institution_admin, also set the flag
        if ($request->role === 'institution_admin') {
            $member->is_institution_admin = true;
        } elseif ($member->is_institution_admin) {
            $member->is_institution_admin = false;
        }
        
        $member->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "Role updated to " . ucfirst($request->role) . " successfully!",
                'new_role' => $request->role,
                'new_badge' => $this->getRoleBadge($request->role)
            ]);
        }
        
        return redirect()->back()->with('success', 'Member role updated successfully!');
    }
    
    private function getRoleBadge($role)
    {
        return match($role) {
            'librarian' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📚 Librarian</span>',
            'instructor' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">👨‍🏫 Instructor</span>',
            'institution_admin' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🏢 Institution Admin</span>',
            'user' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 Member</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . ucfirst($role) . '</span>'
        };
    }
    
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:librarian,user',
        ]);
        
        $password = Str::random(10);
        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'institution_id' => $institution->id,
            'wallet_balance' => 0,
            'referral_code' => User::generateReferralCode(),
        ]);
        
        // Assign Spatie role
        $user->assignRole($request->role);
        
        return redirect()->route('institution.members.index')
            ->with('success', "Member added! Temporary password: {$password}");
    }
    
public function edit(User $member)
{
    $institution = auth()->user()->institution;
    
    if ($member->institution_id !== $institution->id) {
        abort(403);
    }
    
    // Return JSON for AJAX request
    if (request()->ajax()) {
        return response()->json([
            'success' => true,
            'member' => [
                'id' => $member->id,
                'full_name' => $member->full_name,
                'email' => $member->email,
                'role' => $member->getRoleNames()->first() ?? 'user',
            ]
        ]);
    }
    
    return view('institution.members.edit', compact('member'));
}
 public function update(Request $request, User $member)
{
    $institution = auth()->user()->institution;
    
    if ($member->institution_id !== $institution->id) {
        abort(403);
    }
    
    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $member->id,
        'role' => 'required|in:librarian,instructor,institution_admin,user',
    ]);
    
    // Prevent promoting to admin or super_admin
    if (in_array($request->role, ['admin', 'super_admin'])) {
        return redirect()->back()->with('error', 'Cannot promote to Admin or Super Admin.');
    }
    
    $member->update([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'role' => $request->role,
    ]);
    
    // Update Spatie role
    $member->syncRoles([$request->role]);
    
    // Update institution admin flag
    if ($request->role === 'institution_admin') {
        $member->is_institution_admin = true;
    } else {
        $member->is_institution_admin = false;
    }
    $member->save();
    
    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Member updated successfully!']);
    }
    
    return redirect()->route('institution.members.index')->with('success', 'Member updated successfully!');
}
    
    public function destroy(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403);
        }
        
        if ($member->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        
        $member->delete();
        
        return redirect()->route('institution.members.index')->with('success', 'Member deleted successfully!');
    }
}