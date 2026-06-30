<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

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
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status (verified)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'pending') {
                $query->whereNull('email_verified_at');
            }
        }

        $members = $query->latest()->paginate(15)->appends($request->query());

        $stats = [
            'total' => User::where('institution_id', $institution->id)->count(),
            'active' => User::where('institution_id', $institution->id)->whereNotNull('email_verified_at')->count(),
            'pending' => User::where('institution_id', $institution->id)->whereNull('email_verified_at')->count(),
            'librarians' => User::where('institution_id', $institution->id)->whereHas('roles', function($q) {
                $q->where('name', 'librarian');
            })->count(),
        ];

        return view('librarian.members.index', compact('members', 'stats'));
    }

    public function show(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }

        return view('librarian.members.show', compact('member'));
    }

    public function updateRole(Request $request, User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }

        if (!auth()->user()->canManageUser($member)) {
            abort(403, 'You do not have permission to manage this user.');
        }

        $request->validate([
            'role' => 'required|in:user,librarian,institution_admin',
        ]);

        $member->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role updated successfully!');
    }

    public function destroy(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403, 'This member does not belong to your institution.');
        }

        if (!auth()->user()->canDeleteUser($member)) {
            abort(403, 'You do not have permission to remove this user.');
        }

        if (auth()->user()->id === $member->id) {
            return redirect()->back()->with('error', 'You cannot remove yourself.');
        }

        $member->update(['institution_id' => null]);

        return redirect()->route('librarian.members.index')
            ->with('success', 'Member removed from library successfully.');
    }
}