<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstitutionMemberController extends Controller
{
    public function index(Institution $institution)
    {
        $members = User::where('institution_id', $institution->id)->latest()->paginate(15);
        return view('admin.institutions.members', compact('institution', 'members'));
    }
    
    public function create(Institution $institution)
    {
        return view('admin.institutions.members-create', compact('institution'));
    }
    
    public function store(Request $request, Institution $institution)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:institution_admin,librarian,user',
        ]);
        
        $password = Str::random(10);
        
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => $request->role,
            'institution_id' => $institution->id,
            'is_institution_admin' => $request->role === 'institution_admin',
            'wallet_balance' => 0,
            'referral_code' => User::generateReferralCode(),
        ]);
        
        return redirect()->route('admin.institutions.members', $institution)
            ->with('success', "Member added! Temporary password: {$password}");
    }
    
    public function destroy(Institution $institution, User $member)
    {
        if ($member->institution_id !== $institution->id) {
            abort(403);
        }
        
        $member->delete();
        
        return redirect()->route('admin.institutions.members', $institution)->with('success', 'Member removed successfully!');
    }
}