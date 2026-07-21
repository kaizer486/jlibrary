<?php

namespace App\Http\Controllers;

use App\Models\CommunityGroup;
use App\Models\CommunityMember;
use App\Models\CommunityMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    // Display all groups
    public function index(Request $request)
    {
        $query = CommunityGroup::withCount('members');
        
        // Search functionality
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        $groups = $query->latest()->paginate(12);
        
        // Get user's groups if logged in
        $myGroups = [];
        if (Auth::check()) {
            $myGroups = CommunityMember::where('user_id', Auth::id())->pluck('group_id')->toArray();
        }
        
        return view('community.index', compact('groups', 'myGroups'));
    }
    
    // Show single group with chat
    public function show(CommunityGroup $group)
    {
        // Check if user is a member
        $isMember = false;
        $userRole = null;
        
        if (Auth::check()) {
            $membership = CommunityMember::where('group_id', $group->id)
                                         ->where('user_id', Auth::id())
                                         ->first();
            $isMember = $membership !== null;
            $userRole = $membership->role ?? null;
        }
        
        // Get messages with user info
        $messages = CommunityMessage::where('group_id', $group->id)
                                    ->with('user')
                                    ->latest()
                                    ->limit(50)
                                    ->get()
                                    ->reverse();
        
        // Get members count
        $memberCount = CommunityMember::where('group_id', $group->id)->count();
        
        return view('community.show', compact('group', 'isMember', 'messages', 'memberCount', 'userRole'));
    }
    
    // Create a new group
    public function create()
    {
        return view('community.create');
    }
    
    // Store new group
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:community_groups,name',
            'description' => 'required|string|max:500',
            'cover_image' => 'nullable|image|max:2048'
        ]);
        
        $group = CommunityGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'is_active' => true,
            'is_featured' => false,
        ]);
        
        // Add creator as admin member
        CommunityMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('community-covers', 'public');
            $group->cover_image = $path;
            $group->save();
        }
        
        return redirect()->route('community.show', $group)
                         ->with('success', 'Group created successfully!');
    }
    
    // Join a group
    public function join(CommunityGroup $group)
    {
        // Check if already a member
        $exists = CommunityMember::where('group_id', $group->id)
                                 ->where('user_id', Auth::id())
                                 ->exists();
        
        if (!$exists) {
            CommunityMember::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(),
                'role' => 'member',
                'joined_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'You joined the group!');
        }
        
        return redirect()->back()->with('info', 'You are already a member.');
    }
    
    // Leave a group
    public function leave(CommunityGroup $group)
    {
        CommunityMember::where('group_id', $group->id)
                       ->where('user_id', Auth::id())
                       ->delete();
        
        return redirect()->route('community.index')
                         ->with('success', 'You left the group.');
    }
    
    // Send message to group
    public function sendMessage(Request $request, CommunityGroup $group)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        // Check if user is a member
        $isMember = CommunityMember::where('group_id', $group->id)
                                   ->where('user_id', Auth::id())
                                   ->exists();
        
        if (!$isMember) {
            return redirect()->back()->with('error', 'You must join the group to send messages.');
        }
        
        CommunityMessage::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);
        
        return redirect()->back()->with('success', 'Message sent!');
    }
    
    // Delete a message (only for admins/moderators)
    public function deleteMessage(CommunityGroup $group, CommunityMessage $message)
    {
        // Check if user is admin of the group
        $isAdmin = CommunityMember::where('group_id', $group->id)
                                  ->where('user_id', Auth::id())
                                  ->whereIn('role', ['admin', 'moderator'])
                                  ->exists();
        
        // Or check if user owns the message
        $isOwner = $message->user_id === Auth::id();
        
        if ($isAdmin || $isOwner) {
            $message->delete();
            return redirect()->back()->with('success', 'Message deleted.');
        }
        
        return redirect()->back()->with('error', 'You cannot delete this message.');
    }
    
    // Get messages via AJAX (for real-time feel)
    public function getMessages(CommunityGroup $group, $lastId = null)
    {
        $query = CommunityMessage::where('group_id', $group->id)->with('user');
        
        if ($lastId) {
            $query->where('id', '>', $lastId);
        }
        
        $messages = $query->latest()->limit(50)->get()->reverse();
        
        return response()->json($messages);
    }
    
    // My groups page
    public function myGroups()
    {
        $myGroups = CommunityGroup::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->withCount('members')->get();
        
        return view('community.my-groups', compact('myGroups'));
    }
}