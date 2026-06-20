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
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        // Only get users from THIS institution
        $query = User::where('institution_id', $institution->id);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $members = $query->latest()->paginate($request->per_page ?? 15)
            ->appends($request->query());
        
        // Get statistics
        $stats = [
            'total' => User::where('institution_id', $institution->id)->count(),
            'admins' => User::where('institution_id', $institution->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'institution_admin');
                })->count(),
            'librarians' => User::where('institution_id', $institution->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'librarian');
                })->count(),
            'instructors' => User::where('institution_id', $institution->id)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'instructor');
                })->count(),
        ];
        
        return view('institution.members.index', compact('members', 'institution', 'stats'));
    }
    
    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        if (!auth()->user()->can('create', User::class)) {
            abort(403, 'You do not have permission to add members.');
        }
        
        return view('institution.members.create', compact('institution'));
    }
    
    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        if (!auth()->user()->can('create', User::class)) {
            abort(403, 'You do not have permission to add members.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:librarian,instructor,institution_admin,user',
        ]);
        
        // Prevent promoting to admin or super_admin
        if (in_array($request->role, ['admin', 'super_admin'])) {
            return redirect()->back()
                ->with('error', 'Cannot assign Admin or Super Admin role.');
        }
        
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
        
        // Set institution admin flag
        if ($request->role === 'institution_admin') {
            $user->is_institution_admin = true;
            $user->save();
        }
        
        // Send password via email (you can implement this)
        // $user->sendPasswordNotification($password);
        
        // For now, show password in success message (remove in production)
        return redirect()->route('institution.members.index')
            ->with('success', "Member added successfully! Temporary password: {$password}. Please share this with the member.");
    }
    
    /**
     * Display the specified member.
     */
    public function show(User $member)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You are not associated with any institution.');
        }
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->can('view', $member)) {
            abort(403, 'You do not have permission to view this member.');
        }
        
        return view('institution.members.show', compact('member', 'institution'));
    }
    
    /**
     * Show the form for editing the specified member.
     */
    public function edit(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $member)) {
            abort(403, 'You do not have permission to edit this member.');
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
        
        return view('institution.members.edit', compact('member', 'institution'));
    }
    
    /**
     * Update the specified member.
     */
    public function update(Request $request, User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->can('update', $member)) {
            abort(403, 'You do not have permission to update this member.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'role' => 'required|in:librarian,instructor,institution_admin,user',
        ]);
        
        // Prevent promoting to admin or super_admin
        if (in_array($request->role, ['admin', 'super_admin'])) {
            return redirect()->back()
                ->with('error', 'Cannot assign Admin or Super Admin role.');
        }
        
        // Update user
        $member->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        
        // Update Spatie role
        $member->syncRoles([$request->role]);
        
        // Update institution admin flag
        $member->is_institution_admin = ($request->role === 'institution_admin');
        $member->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Member updated successfully!'
            ]);
        }
        
        return redirect()->route('institution.members.index')
            ->with('success', 'Member updated successfully!');
    }
    
    /**
     * Remove the specified member.
     */
    public function destroy(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->can('delete', $member)) {
            abort(403, 'You do not have permission to delete this member.');
        }
        
        if ($member->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete yourself.');
        }
        
        // Remove from institution
        $member->update([
            'institution_id' => null,
            'is_institution_admin' => false,
        ]);
        $member->removeRole($member->getRoleNames()->first());
        
        // Soft delete or hard delete (soft delete recommended)
        $member->delete();
        
        return redirect()->route('institution.members.index')
            ->with('success', 'Member removed successfully.');
    }
    
    /**
     * Update member role (dedicated method).
     */
    public function updateRole(Request $request, User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            return response()->json([
                'success' => false,
                'message' => 'Member does not belong to your institution.'
            ], 403);
        }
        
        if (!auth()->user()->can('update', $member)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this member.'
            ], 403);
        }
        
        $request->validate([
            'role' => 'required|in:librarian,instructor,institution_admin,user'
        ]);
        
        // Prevent promoting to admin or super_admin
        if (in_array($request->role, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot promote to Admin or Super Admin.'
            ], 403);
        }
        
        // Update role
        $member->syncRoles([$request->role]);
        $member->role = $request->role;
        $member->is_institution_admin = ($request->role === 'institution_admin');
        $member->save();
        
        return response()->json([
            'success' => true,
            'message' => "Role updated to " . ucfirst($request->role) . " successfully!",
            'new_role' => $request->role,
            'new_badge' => $this->getRoleBadge($request->role)
        ]);
    }
    
    /**
     * Directory view of members grouped by role.
     */
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
     /**
     * Export members list.
     */
    public function export(Request $request)
    {
        $institution = $this->getAuthInstitution();
        $this->authorize('export', User::class);

        // ✅ FIX: Use query() or input() to get the format parameter
        $format = $request->query('format', 'csv'); // or $request->input('format', 'csv')

        $members = User::where('institution_id', $institution->id)->get();

        if ($format === 'csv') {
            return $this->exportCsv($members, $institution);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($members, $institution);
        }

        return redirect()->back()
            ->with('error', 'Unsupported export format.');
    }

    /**
     * Export members as CSV.
     */
    private function exportCsv($members, $institution)
    {
        $filename = "members_{$institution->id}_" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function() use ($members) {
            $handle = fopen('php://output', 'w');
            
            // Headers
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Joined At']);
            
            // Data
            foreach ($members as $member) {
                fputcsv($handle, [
                    $member->id,
                    $member->full_name,
                    $member->email,
                    $member->getRoleNames()->first() ?? 'user',
                    $member->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export members as PDF (placeholder).
     */
    private function exportPdf($members, $institution)
    {
        // This is a placeholder. You'll need to install a PDF package like barryvdh/laravel-dompdf
        return redirect()->back()
            ->with('info', 'PDF export coming soon!');
    }

    /**
     * Get the authenticated user's institution with authorization.
     */
    private function getAuthInstitution(): \App\Models\Institution
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        return $institution;
    }

    /**
     * Get role badge HTML.
     */
    private function getRoleBadge($role)
    {
        $badges = [
            'librarian' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📚 Librarian</span>',
            'instructor' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">👨‍🏫 Instructor</span>',
            'institution_admin' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🏢 Institution Admin</span>',
            'user' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 Member</span>',
        ];

        return $badges[$role] ?? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . ucfirst($role) . '</span>';
    }
}