<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;

class MemberController extends Controller
{
    /**
     * Display a listing of members.
     */
    public function index(Request $request)
    {
        $institution = $this->getAuthInstitution();
        
        $query = User::where('institution_id', $institution->id);
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        // Role filter
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        $members = $query->latest()->paginate($request->per_page ?? 15)
            ->appends($request->query());
        
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
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to add members.');
        }
        
        return view('institution.members.create', compact('institution'));
    }
    
    /**
     * Store a newly created member.
     */
    public function store(Request $request)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to add members.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:librarian,instructor,institution_admin,user',
        ]);
        
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
        
        // Add to pivot table
        $this->addUserToInstitution($user, $institution, $request->role);
        
        $user->assignRole($request->role);
        
        if ($request->role === 'institution_admin') {
            $user->is_institution_admin = true;
            $user->save();
        }
        
        // Send welcome email
        try {
            \Mail::to($user->email)->send(new \App\Mail\WelcomeMember($user, $password));
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email to ' . $user->email . ': ' . $e->getMessage());
        }
        
        return redirect()->route('institution.members.index')
            ->with('success', 'Member added successfully! A welcome email with login credentials has been sent.');
    }
    
    /**
     * Display the specified member.
     */
    public function show(User $member)
    {
        $institution = $this->getAuthInstitution();
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to view this member.');
        }
        
        return view('institution.members.show', compact('member', 'institution'));
    }
    
    /**
     * Show the form for editing the specified member.
     */
    public function edit(User $member)
    {
        $institution = $this->getAuthInstitution();
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to edit this member.');
        }
        
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
        $institution = $this->getAuthInstitution();
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to update this member.');
        }
        
        if ($member->id === auth()->id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot change your own role.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'role' => 'required|in:librarian,instructor,institution_admin,user',
        ]);
        
        if (in_array($request->role, ['admin', 'super_admin'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot assign Admin or Super Admin role.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Cannot assign Admin or Super Admin role.');
        }
        
        $oldInstitutionId = $member->institution_id;
        
        $member->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        
        $member->syncRoles([$request->role]);
        $member->is_institution_admin = ($request->role === 'institution_admin');
        $member->save();
        
        // Update pivot role
        $member->institutions()->syncWithoutDetaching([
            $institution->id => [
                'role' => $request->role,
            ]
        ]);
        
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
     * Get member data for AJAX edit modal.
     */
    public function editJson($id)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this member.'
            ], 403);
        }
        
        $member = User::where('institution_id', $institution->id)->findOrFail($id);
        
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
    
    /**
     * Remove the specified member.
     */
    public function destroy(User $member)
    {
        $institution = $this->getAuthInstitution();
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to delete this member.');
        }
        
        if ($member->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        
        $name = $member->full_name;
        
        // Remove from pivot table
        $member->institutions()->detach($institution->id);
        
        $member->update([
            'institution_id' => null,
            'is_institution_admin' => false,
        ]);
        
        $member->removeRole($member->getRoleNames()->first());
        $member->delete();
        
        return redirect()->route('institution.members.index')
            ->with('success', "{$name} has been removed successfully. You can restore them from the trash.");
    }
    
    /**
     * Display trashed members for restoration.
     */
    public function trashed(Request $request)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to view trashed members.');
        }
        
        $members = User::where('institution_id', $institution->id)
            ->onlyTrashed()
            ->latest()
            ->paginate($request->per_page ?? 15)
            ->appends($request->query());
        
        $stats = [
            'total' => User::where('institution_id', $institution->id)->count(),
            'trashed' => User::where('institution_id', $institution->id)->onlyTrashed()->count(),
        ];
        
        return view('institution.members.trashed', compact('members', 'institution', 'stats'));
    }

    /**
     * Restore a trashed member.
     */
    public function restore($id)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to restore members.');
        }
        
        $member = User::where('institution_id', $institution->id)
            ->onlyTrashed()
            ->findOrFail($id);
        
        $member->restore();
        
        // Re-add to pivot when restoring
        $this->addUserToInstitution($member, $institution, $member->role ?? 'user');
        
        $role = $member->role ?? 'user';
        $member->assignRole($role);
        
        return redirect()->route('institution.members.trashed')
            ->with('success', "{$member->full_name} has been restored successfully.");
    }

    /**
     * Permanently delete a trashed member.
     */
    public function forceDelete($id)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to permanently delete members.');
        }
        
        $member = User::where('institution_id', $institution->id)
            ->onlyTrashed()
            ->findOrFail($id);
        
        $name = $member->full_name;
        $member->syncRoles([]);
        $member->forceDelete();
        
        return redirect()->route('institution.members.trashed')
            ->with('success', "{$name} has been permanently deleted.");
    }
    
    /**
     * Update member role (dedicated method).
     */
    public function updateRole(Request $request, User $member)
    {
        $institution = $this->getAuthInstitution();
        
        if ($member->institution_id !== $institution->id) {
            return response()->json([
                'success' => false,
                'message' => 'Member does not belong to your institution.'
            ], 403);
        }
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this member.'
            ], 403);
        }
        
        if ($member->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own role.'
            ], 403);
        }
        
        $request->validate([
            'role' => 'required|in:librarian,instructor,institution_admin,user'
        ]);
        
        if (in_array($request->role, ['admin', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot promote to Admin or Super Admin.'
            ], 403);
        }
        
        $member->syncRoles([$request->role]);
        $member->role = $request->role;
        $member->is_institution_admin = ($request->role === 'institution_admin');
        $member->save();
        
        // Update pivot role
        $member->institutions()->syncWithoutDetaching([
            $institution->id => [
                'role' => $request->role,
            ]
        ]);
        
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
        $institution = $this->getAuthInstitution();
        
        // Get all members of the institution
        $allMembers = User::where('institution_id', $institution->id)
            ->whereNull('deleted_at')
            ->get();
        
        $groupedMembers = $allMembers->groupBy(function($user) {
            return $user->getRoleNames()->first() ?? 'user';
        });
        
        $roleLabels = [
            'librarian' => '📚 Librarians',
            'instructor' => '👨‍🏫 Instructors',
            'institution_admin' => '🏢 Institution Admins',
            'user' => '👤 Members'
        ];
        
        $roleColors = [
            'librarian' => 'from-blue-500 to-cyan-400',
            'instructor' => 'from-emerald-500 to-green-400',
            'institution_admin' => 'from-purple-500 to-pink-500',
            'user' => 'from-gray-500 to-gray-400'
        ];
        
        $roleBadgeColors = [
            'librarian' => 'bg-blue-500/20 text-blue-300',
            'instructor' => 'bg-emerald-500/20 text-emerald-300',
            'institution_admin' => 'bg-purple-500/20 text-purple-300',
            'user' => 'bg-gray-500/20 text-gray-300'
        ];
        
        $roleBadgeLabels = [
            'librarian' => '📚 Librarian',
            'instructor' => '👨‍🏫 Instructor',
            'institution_admin' => '🏢 Admin',
            'user' => '👤 Member'
        ];
        
        return view('institution.members.directory', compact(
            'institution', 
            'allMembers',
            'groupedMembers',
            'roleLabels',
            'roleColors',
            'roleBadgeColors',
            'roleBadgeLabels'
        ));
    }
    
    /**
     * Export members list.
     */
    public function export(Request $request)
    {
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to export members.');
        }

        $format = $request->query('format', 'csv');
        $members = User::where('institution_id', $institution->id)->get();

        if ($format === 'csv') {
            return $this->exportCsv($members, $institution);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($members, $institution);
        }

        return redirect()->back()->with('error', 'Unsupported export format.');
    }

    /**
     * Export members as CSV.
     */
    private function exportCsv($members, $institution)
    {
        $filename = "members_{$institution->id}_" . date('Y-m-d') . ".csv";

        return response()->streamDownload(function() use ($members) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['ID', 'Name', 'Email', 'Role', 'Joined At']);
            
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
     * Export members as PDF.
     */
    private function exportPdf($members, $institution)
    {
        try {
            $pdf = Pdf::loadView('institution.members.export-pdf', [
                'members' => $members,
                'institution' => $institution,
                'date' => now()->format('Y-m-d H:i:s'),
                'total' => $members->count(),
            ]);
            
            $filename = "members_{$institution->id}_" . date('Y-m-d') . ".pdf";
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

    /**
     * Bulk action on members.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
            'action' => 'required|string|in:remove,activate,deactivate',
        ]);
        
        $institution = $this->getAuthInstitution();
        
        if (!auth()->user()->isInstitutionAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'You do not have permission to perform bulk actions.');
        }
        
        $members = User::where('institution_id', $institution->id)
            ->whereIn('id', $request->member_ids)
            ->get();
        
        foreach ($members as $member) {
            if ($request->action === 'remove') {
                $member->institutions()->detach($institution->id);
                $member->institution_id = null;
                $member->save();
            } elseif ($request->action === 'activate') {
                $member->email_verified_at = now();
                $member->save();
            } elseif ($request->action === 'deactivate') {
                $member->email_verified_at = null;
                $member->save();
            }
        }
        
        return redirect()->back()->with('success', 'Bulk action completed successfully!');
    }

    /**
     * Get the authenticated user's institution with authorization.
     */
    private function getAuthInstitution(): Institution
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
    private function getRoleBadge($role): string
    {
        $badges = [
            'librarian' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">📚 Librarian</span>',
            'instructor' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">👨‍🏫 Instructor</span>',
            'institution_admin' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🏢 Institution Admin</span>',
            'user' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 Member</span>',
        ];

        return $badges[$role] ?? '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">' . ucfirst($role) . '</span>';
    }

    /**
     * Helper: Add user to institution (legacy + pivot).
     */
    private function addUserToInstitution(User $user, Institution $institution, string $role = 'user'): void
    {
        // Add to pivot table
        $user->institutions()->syncWithoutDetaching([
            $institution->id => [
                'role' => $role,
                'status' => 'active',
                'joined_at' => now(),
            ]
        ]);
        
        // Update legacy field if not already set
        if (!$user->institution_id) {
            $user->update([
                'institution_id' => $institution->id,
            ]);
        }
    }
}