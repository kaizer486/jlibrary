<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
use App\Models\JoinRequest;
use Illuminate\Http\Request;
use App\Helpers\LibraryNotificationHelper;

class JoinRequestController extends Controller
{
    /**
     * Display all join requests for the librarian's institution.
     */
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $query = JoinRequest::where('institution_id', $institution->id)
            ->with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15)->appends($request->query());

        $pendingCount = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'pending')
            ->count();

        $approvedCount = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->count();

        $rejectedCount = JoinRequest::where('institution_id', $institution->id)
            ->where('status', 'rejected')
            ->count();

        return view('librarian.join-requests.index', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Approve a join request.
     */
    public function approve(JoinRequest $joinRequest)
    {
        $institution = auth()->user()->institution;
        
        if ($joinRequest->institution_id !== $institution->id) {
            abort(403, 'This request does not belong to your institution.');
        }

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        // 1. Update the join request
        $joinRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // 2. Get the user
        $user = $joinRequest->user;
        
        // 3. Assign user to institution
        $user->institution_id = $institution->id;
        
        // 4. MARK EMAIL AS VERIFIED
        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }
        
        $user->save();

        // ==========================================
        // 5. SEND NOTIFICATIONS
        // ==========================================
        
        // Notify the user that they were approved
        LibraryNotificationHelper::joinApproved($user, $institution);

        // Optional: Notify all librarians that a new member joined
        // LibraryNotificationHelper::notifyLibrarians(
        //     $institution->id,
        //     \App\Models\Notification::TYPE_LIBRARY_MEMBER_JOINED,
        //     '👤 New Member Joined',
        //     "{$user->full_name} has joined the library.",
        //     [
        //         'user_id' => $user->id,
        //         'user_name' => $user->full_name,
        //         'institution_id' => $institution->id,
        //     ]
        // );

        return redirect()->back()->with('success', 'Join request approved! User has been added to the institution and is now active.');
    }

    /**
     * Reject a join request.
     */
    public function reject(Request $request, JoinRequest $joinRequest)
    {
        $institution = auth()->user()->institution;
        
        if ($joinRequest->institution_id !== $institution->id) {
            abort(403, 'This request does not belong to your institution.');
        }

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $rejectionReason = $request->filled('rejection_reason') ? $request->rejection_reason : null;

        $data = [
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ];
        
        if ($rejectionReason) {
            $data['rejection_reason'] = $rejectionReason;
        }

        $joinRequest->update($data);

        // ==========================================
        // SEND REJECTION NOTIFICATION
        // ==========================================
        
        // Notify the user that they were rejected
        $user = $joinRequest->user;
        LibraryNotificationHelper::joinRejected($user, $institution, $rejectionReason);

        return redirect()->back()->with('success', 'Join request rejected.');
    }
}