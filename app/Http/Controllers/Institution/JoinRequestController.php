<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class JoinRequestController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

      $query = JoinRequest::where('institution_id', $institution->id)
    ->with(['user' => function($q) {
        $q->withTrashed();  
    }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

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

        // ✅ PASS $institution to the view
        return view('institution.join-requests.index', compact('requests', 'stats', 'institution'));
    }

    public function show($id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->with('user')
            ->findOrFail($id);

        // ✅ PASS $institution and $joinRequest to the view
        return view('institution.join-requests.show', compact('institution', 'joinRequest'));
    }

    public function approve($id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->findOrFail($id);

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $joinRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $user = $joinRequest->user;
        $user->institution_id = $institution->id;

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $user->save();

        return redirect()->route('institution.join-requests.index')
            ->with('success', 'Join request approved! User has been added to the institution.');
    }

    public function reject(Request $request, $id)
    {
        $institution = auth()->user()->institution;

        if (!$institution) {
            abort(403, 'You do not belong to any institution.');
        }

        $joinRequest = JoinRequest::where('institution_id', $institution->id)
            ->findOrFail($id);

        if ($joinRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $data = [
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ];

        if ($request->filled('rejection_reason')) {
            $data['rejection_reason'] = $request->rejection_reason;
        }

        $joinRequest->update($data);

        return redirect()->route('institution.join-requests.index')
            ->with('success', 'Join request rejected.');
    }
}