<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Book;
use App\Models\JoinRequest;
use App\Models\User;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Show all institutions the user belongs to (My Institutions).
     */
    public function myInstitution()
    {
        $user = auth()->user();
        
        // Get institutions from PIVOT TABLE
        $institutions = $user->institutions()
            ->withCount(['users as members_count', 'books', 'shelves'])
            ->get();
        
        // If user has multiple institutions, show list
        if ($institutions->count() > 1) {
            return view('institution.my-institutions', compact('institutions'));
        }
        
        // If user has exactly one institution, redirect to it
        if ($institutions->count() === 1) {
            return redirect()->route('institution.public.index', $institutions->first()->id);
        }
        
        // If user has no institutions, show empty state
        return view('institution.my-institution-empty');
    }
    
    /**
     * Show all institutions user can discover (not a member of).
     */
    public function discover(Request $request)
    {
        $user = auth()->user();
        
        // Get IDs of institutions user belongs to from PIVOT TABLE
        $myInstitutionIds = $user->institutions()->pluck('institution_id')->toArray();
        
        // Build query
        $query = Institution::where('status', 'approved')
            ->whereNotIn('id', $myInstitutionIds)
            ->withCount(['users as members_count', 'books', 'shelves']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('region', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $institutions = $query->latest()->paginate(12)->appends($request->query());
        
        // Get user's pending join requests
        $userRequests = JoinRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get()
            ->keyBy('institution_id');
        
        return view('institution.discover', compact('institutions', 'userRequests'));
    }
    
    /**
     * Show a specific institution details.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        $institution = Institution::withCount(['users as members_count', 'books', 'shelves'])
            ->findOrFail($id);
        
        // Check if user is already a member
        $isMember = $user->institutions()->where('institution_id', $id)->exists();
        
        // Get user's role in this institution
        $userRole = $user->roleInInstitution($id);
        
        // Check if user has a pending request
        $existingRequest = JoinRequest::where('user_id', $user->id)
            ->where('institution_id', $id)
            ->first();
        
        // Get books from this institution
        $institutionBooks = Book::where('institution_id', $id)
            ->where('status', 'approved')
            ->latest()
            ->paginate(12);
        
        // Get members of this institution
        $members = $institution->users()->latest()->paginate(10);
        
        return view('institution.show', compact(
            'institution',
            'isMember',
            'userRole',
            'existingRequest',
            'institutionBooks',
            'members'
        ));
    }
    
    /**
     * Free join an institution (no approval needed).
     * For: Library, Bookstore, Publisher, Research Center, Other
     */
 public function freeJoin(Request $request, $id): \Illuminate\Http\RedirectResponse
{
    \Log::info('freeJoin called', [
        'user_id' => auth()->id(),
        'institution_id' => $id,
        'method' => $request->method(),
        'url' => $request->url(),
    ]);
    
    $user = auth()->user();
    $institution = Institution::findOrFail($id);
    
    // Check if already a member
    $isMember = $user->institutions()->where('institution_id', $id)->exists();
    \Log::info('Membership check', ['is_member' => $isMember]);
    
    if ($isMember) {
        \Log::info('Already member - redirecting back');
        return redirect()->back()
            ->with('error', 'You are already a member of this institution.');
    }
    
    // Check if institution can accept new members
    $canAdd = $institution->canAddUser();
    \Log::info('Can add user check', ['can_add' => $canAdd]);
    
    if (!$canAdd) {
        \Log::info('Cannot add user - redirecting back');
        return redirect()->back()
            ->with('error', 'This institution has reached its maximum member limit.');
    }
    
    \Log::info('Proceeding with join');
    
    // Add to pivot table
    $user->institutions()->attach($id, [
        'role' => 'member',
        'status' => 'active',
        'joined_at' => now(),
    ]);
    
    // Update legacy field if no primary
    if (!$user->institution_id) {
        $user->update([
            'institution_id' => $id,
        ]);
    }
    
    // Send welcome notification
    NotificationHelper::send(
        $user->id,
        'institution_joined',
        '🏛️ Welcome to ' . $institution->name,
        "You have successfully joined {$institution->name}!",
        ['institution_id' => $id, 'type' => 'institution_joined']
    );
    
    \Log::info('Join successful, redirecting to institution');
    
    return redirect()->route('institution.public.index', $id)
        ->with('success', "You have successfully joined {$institution->name}!");
}
  
    /**
     * Leave an institution.
     */
    public function leave(Request $request, $institutionId = null)
    {
        $user = auth()->user();
        
        // If no institution ID provided, use the first one
        if (!$institutionId) {
            $first = $user->institutions()->first();
            if (!$first) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'You are not associated with any institution.']);
                }
                return redirect()->route('dashboard')
                    ->with('error', 'You are not associated with any institution.');
            }
            $institutionId = $first->id;
        }
        
        $institution = Institution::findOrFail($institutionId);
        
        // Check if user is a member
        if (!$user->institutions()->where('institution_id', $institutionId)->exists()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You are not a member of this institution.']);
            }
            return redirect()->back()->with('error', 'You are not a member of this institution.');
        }
        
        // Get user's role
        $userRole = $user->roleInInstitution($institutionId);
        
        // If user is admin, check if they're the only admin
        if ($userRole === 'admin' || $userRole === 'institution_admin') {
            $adminCount = $institution->users()->wherePivot('role', 'admin')->count();
            
            if ($adminCount <= 1) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You are the only admin. Please assign another admin before leaving.'
                    ]);
                }
                return redirect()->back()
                    ->with('error', 'You are the only admin. Please assign another admin before leaving.');
            }
        }
        
        // Check if this is the primary institution
        $isPrimary = ($user->institution_id == $institutionId);
        
        // Remove from pivot
        $user->institutions()->detach($institutionId);
        
        // Delete the join request record when user leaves
        JoinRequest::where('user_id', $user->id)
            ->where('institution_id', $institutionId)
            ->delete();
        
        // If this was the primary, clear it
        if ($isPrimary) {
            $user->update([
                'institution_id' => null,
                'is_institution_admin' => false,
            ]);
        }
        
        // If user has no more institutions, reset everything
        if ($user->institutions()->count() === 0) {
            $user->update([
                'is_institution_admin' => false,
                'role' => 'user',
            ]);
            $user->removeRole('institution_admin');
            $user->removeRole('librarian');
            $user->removeRole('instructor');
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => "You have left '{$institution->name}' successfully."
            ]);
        }
        
        return redirect()->route('my.institution')
            ->with('success', "You have left '{$institution->name}' successfully.");
    }
}