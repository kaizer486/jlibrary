<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\JoinRequest;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class JoinRequestController extends Controller
{
    /**
     * Show user's join requests.
     */
    public function index(): View
    {
        $userRequests = JoinRequest::where('user_id', auth()->id())
            ->with('institution')
            ->latest()
            ->paginate(request('per_page', 10));
        
        return view('join-requests.index', compact('userRequests'));
    }
    
    /**
     * Store a new join request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'message' => 'nullable|string|max:500',
        ]);
        
        $user = auth()->user();
        $institution = Institution::findOrFail($request->institution_id);
        
        // ✅ Check if institution requires approval
        if (!$institution->requiresApproval()) {
            return redirect()->back()
                ->with('error', 'This institution does not require approval. Click "Join Now" to join instantly.');
        }
        
        // Check if user is already a member
        if ($user->institutions()->where('institution_id', $institution->id)->exists()) {
            return redirect()->back()
                ->with('error', 'You are already a member of this institution.');
        }
        
        // Check if institution can accept new members
        if (!$institution->canAddUser()) {
            return redirect()->back()
                ->with('error', 'This institution has reached its maximum member limit.');
        }
        
        // Check for existing request
        $existingRequest = JoinRequest::where('user_id', $user->id)
            ->where('institution_id', $institution->id)
            ->first();
        
        if ($existingRequest) {
            // If the user was previously approved but left, allow re-joining
            if ($existingRequest->status === 'approved') {
                // Delete the old record to allow new request
                $existingRequest->delete();
            } elseif ($existingRequest->status === 'pending') {
                return redirect()->back()
                    ->with('error', 'You already have a pending request for this institution.');
            } elseif ($existingRequest->status === 'rejected') {
                return redirect()->back()
                    ->with('error', 'Your previous request was rejected. Please contact the institution admin.');
            }
        }
        
        // Create join request
        $joinRequest = JoinRequest::create([
            'user_id' => $user->id,
            'institution_id' => $institution->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // Send notification to all institution admins
        $admins = User::where('institution_id', $institution->id)
            ->where('is_institution_admin', true)
            ->get();

        foreach ($admins as $admin) {
            NotificationHelper::joinRequestSent(
                $admin->id,
                auth()->user()->full_name,
                $joinRequest->id,
                $institution->id
            );
        }
        
        return redirect()->back()
            ->with('success', 'Join request sent successfully! Awaiting approval.');
    }
    
    /**
     * Show user's join requests (alias).
     */
    public function myRequests(Request $request): View
    {
        $requests = JoinRequest::where('user_id', auth()->id())
            ->with('institution')
            ->latest()
            ->paginate(10);
        
        return view('join-requests.my-requests', compact('requests'));
    }
    
    /**
     * Cancel a pending request.
     */
    public function cancel(JoinRequest $joinRequest): RedirectResponse
    {
        if ($joinRequest->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($joinRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Cannot cancel a request that has already been processed.');
        }
        
        $joinRequest->delete();
        
        return redirect()->back()
            ->with('success', 'Join request cancelled successfully.');
    }

    // ==========================================
    // INSTITUTION ADMIN METHODS
    // ==========================================

    /**
     * Display join requests for the institution admin.
     */
    public function institutionRequests(Request $request): View
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $query = JoinRequest::where('institution_id', $institution->id)
            ->with('user');
        
        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }
        
        $requests = $query->latest()->paginate(15)->appends($request->query());
        
        $stats = [
            'pending' => JoinRequest::where('institution_id', $institution->id)->where('status', 'pending')->count(),
            'approved' => JoinRequest::where('institution_id', $institution->id)->where('status', 'approved')->count(),
            'rejected' => JoinRequest::where('institution_id', $institution->id)->where('status', 'rejected')->count(),
            'total' => JoinRequest::where('institution_id', $institution->id)->count(),
        ];
        
        return view('institution.join-requests.index', compact('requests', 'institution', 'stats'));
    }

    /**
     * Show a specific join request.
     */
    public function showRequest($id): View
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->with('user')
            ->findOrFail($id);
        
        return view('institution.join-requests.show', compact('joinRequest', 'institution'));
    }

    /**
     * Approve a join request.
     */
    public function approveRequest($id): RedirectResponse
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->findOrFail($id);
        
        if ($joinRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }
        
        // Check if institution can accept new members
        if (!$institution->canAddUser()) {
            return redirect()->back()
                ->with('error', 'Institution has reached its maximum member limit.');
        }
        
        $user = $joinRequest->user;
        
        // Update request status
        $joinRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        // ✅ ADD USER TO PIVOT TABLE
        $user->institutions()->syncWithoutDetaching([
            $institution->id => [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]
        ]);
        
        // ✅ Update legacy institution_id if user has no primary
        if (!$user->institution_id) {
            $user->update([
                'institution_id' => $institution->id,
            ]);
        }
        
        // Notify the user
        NotificationHelper::joinRequestApproved(
            $user->id,
            $institution->name,
            $institution->id
        );
        
        return redirect()->route('institution.join-requests.index')
            ->with('success', "{$user->full_name} has been approved and added to the institution.");
    }

    /**
     * Reject a join request.
     */
    public function rejectRequest(Request $request, $id): RedirectResponse
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }
        
        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->findOrFail($id);
        
        if ($joinRequest->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }
        
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);
        
        $joinRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);
        
        // Notify the user
        NotificationHelper::joinRequestRejected(
            $joinRequest->user_id,
            $institution->name,
            $request->rejection_reason
        );
        
        return redirect()->route('institution.join-requests.index')
            ->with('success', 'Join request has been rejected.');
    }

    /**
     * Helper: Add user to institution (legacy + pivot).
     */
    private function addUserToInstitution(User $user, Institution $institution): void
    {
        // Add to pivot table
        $user->institutions()->syncWithoutDetaching([
            $institution->id => [
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]
        ]);
        
        // Update legacy field
        $user->update([
            'institution_id' => $institution->id,
        ]);
    }
}