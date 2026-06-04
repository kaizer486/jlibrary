<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class JoinRequestController extends Controller
{
    // Show institutions on user dashboard
    public function index()
    {
        $institutions = Institution::where('status', 'approved')
            ->withCount('users')
            ->get();
            
        $userRequests = JoinRequest::where('user_id', auth()->id())
            ->get()
            ->keyBy('institution_id');
            
        return view('dashboard', compact('institutions', 'userRequests'));
    }
    
    // Store join request
    public function store(Request $request)
    {
        $request->validate([
            'institution_id' => 'required|exists:institutions,id',
            'message' => 'nullable|string|max:500',
        ]);
        
        $institution = Institution::findOrFail($request->institution_id);
        
        // Check if already a member
        if (auth()->user()->institution_id === $institution->id) {
            return back()->with('error', 'You are already a member of this institution.');
        }
        
        // Check if already requested
        $existingRequest = JoinRequest::where('user_id', auth()->id())
            ->where('institution_id', $institution->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingRequest) {
            return back()->with('error', 'You already have a ' . $existingRequest->status . ' request for this institution.');
        }
        
        JoinRequest::create([
            'user_id' => auth()->id(),
            'institution_id' => $institution->id,
            'message' => $request->message,
            'status' => 'pending',
        ]);
        
        return back()->with('success', 'Join request sent successfully! Awaiting approval.');
    }
    
    // View user's requests
    public function myRequests()
    {
        $requests = JoinRequest::where('user_id', auth()->id())
            ->with('institution')
            ->latest()
            ->paginate(10);
            
        return view('join-requests.my-requests', compact('requests'));
    }
    
    // Cancel pending request
    public function cancel(JoinRequest $joinRequest)
    {
        if ($joinRequest->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($joinRequest->status !== 'pending') {
            return back()->with('error', 'Cannot cancel a request that has already been processed.');
        }
        
        $joinRequest->delete();
        
        return back()->with('success', 'Join request cancelled.');
    }
}