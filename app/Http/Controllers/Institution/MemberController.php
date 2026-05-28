<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class MemberController extends Controller
{
    public function index(Request $request)
    {
        $institution = auth()->user()->institution;
        
        $query = User::where('institution_id', $institution->id);
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        $members = $query->latest()->paginate(15);
        
        return view('institution.members.index', compact('members', 'institution'));
    }
    
    public function create()
    {
        return view('institution.members.create');
    }
    
public function store(Request $request)
{
    $institution = auth()->user()->institution;
    
    $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:librarian,user',
    ]);
    
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
    
    return redirect()->route('institution.members.index')
        ->with('success', "Member added! Temporary password: {$password}");
}
    
    public function edit(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403);
        }
        
        return view('institution.members.edit', compact('member'));
    }
    
    public function update(Request $request, User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403);
        }
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->id,
            'role' => 'required|in:librarian,user',
        ]);
        
        $member->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'role' => $request->role,
        ]);
        
        return redirect()->route('institution.members.index')->with('success', 'Member updated successfully!');
    }
    
    public function destroy(User $member)
    {
        $institution = auth()->user()->institution;
        
        if ($member->institution_id !== $institution->id) {
            abort(403);
        }
        
        if ($member->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }
        
        $member->delete();
        
        return redirect()->route('institution.members.index')->with('success', 'Member deleted successfully!');
    }
}