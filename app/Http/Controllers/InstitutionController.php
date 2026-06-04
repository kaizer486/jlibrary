<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Book;
use App\Models\JoinRequest;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Show user's own institution (My Institution)
     */
    public function myInstitution()
    {
        $user = auth()->user();
        
        // If user doesn't have an institution
        if (!$user->institution_id) {
            return view('institution.my-institution-empty');
        }
        
        $institution = Institution::withCount('users', 'books')->findOrFail($user->institution_id);
        
        // Get books from this institution
        $institutionBooks = Book::where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->latest()
            ->paginate(12);
        
        // Get members of this institution
        $members = $user->institution->users()->latest()->paginate(10);
        
        return view('institution.my-institution', compact('institution', 'institutionBooks', 'members', 'user'));
    }
    
    /**
     * Show all institutions user can discover (not a member of)
     */
    public function discover()
    {
        $userId = auth()->id();
        $userInstitutionId = auth()->user()->institution_id;
        
        // Get institutions that user is NOT a member of
        $institutions = Institution::where('status', 'approved')
            ->where('id', '!=', $userInstitutionId)
            ->withCount('users', 'books')
            ->latest()
            ->paginate(12);
        
        // Get user's pending requests
        $userRequests = JoinRequest::where('user_id', $userId)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->keyBy('institution_id');
        
        return view('institution.discover', compact('institutions', 'userRequests'));
    }
    
    /**
     * Show a specific institution details (for discover view)
     */
    public function show($id)
    {
        $institution = Institution::withCount('users', 'books')->findOrFail($id);
        
        // Check if user is already a member
        $isMember = auth()->user()->institution_id == $institution->id;
        
        // Check if user has a pending request
        $existingRequest = JoinRequest::where('user_id', auth()->id())
            ->where('institution_id', $id)
            ->first();
        
        // Get books from this institution
        $institutionBooks = Book::where('institution_id', $id)
            ->where('status', 'approved')
            ->latest()
            ->paginate(12);
        
        // Get members of this institution
        $members = $institution->users()->latest()->paginate(10);
        
        return view('institution.show', compact('institution', 'isMember', 'existingRequest', 'institutionBooks', 'members'));
    }
}