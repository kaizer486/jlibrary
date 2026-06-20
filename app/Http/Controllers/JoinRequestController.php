<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\JoinRequest;
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
        
        // Check if user is already a member
        if ($user->institution_id === $institution->id) {
            return redirect()->back()
                ->with('error', 'You are already a member of this institution.');
        }
        
        // Check if institution can accept new members
        if (!$institution->canAddUser()) {
            return redirect()->back()
                ->with('error', 'This institution has reached its maximum member limit.');
        }
        
        // Check if user has reached pending request limit
        $pendingCount = JoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
            
        if ($pendingCount >= 3) {
            return redirect()->back()
                ->with('error', 'You have too many pending join requests. Please wait for them to be processed.');
        }
        
        // Check for existing request
        $existingRequest = JoinRequest::where('user_id', $user->id)
            ->where('institution_id', $institution->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingRequest) {
            $status = $existingRequest->status === 'pending' ? 'pending' : 'processed';
            return redirect()->back()
                ->with('error', "You already have a {$status} request for this institution.");
        }
        
        // Create join request
        $joinRequest = JoinRequest::create([
            'user_id' => $user->id,
            'institution_id' => $institution->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);
        
        return redirect()->back()
            ->with('success', 'Join request sent successfully! Awaiting approval.');
    }
    
    /**
     * View user's requests.
     */
    public function myRequests(): View
    {
        $requests = JoinRequest::where('user_id', auth()->id())
            ->with('institution')
            ->latest()
            ->paginate(request('per_page', 10));
            
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
        
        // Update request status
        $joinRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        // Add user to institution
        $user = $joinRequest->user;
        $user->update([
            'institution_id' => $institution->id,
            'joined_institution_at' => now(),
        ]);
        
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
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('institution.join-requests.index')
            ->with('success', 'Join request has been rejected.');
    }
}