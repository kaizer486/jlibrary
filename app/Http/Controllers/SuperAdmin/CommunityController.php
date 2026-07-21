<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityGroup::with('creator')
            ->withCount([
                'members as members_count' => function($query) {
                    $query->where('group_id', 'community_groups.id');
                },
                'messages as messages_count' => function($query) {
                    $query->where('group_id', 'community_groups.id');
                }
            ]);

        // Search filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->status && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sort = $request->sort ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'members':
                $query->orderBy('members_count', 'desc');
                break;
            case 'messages':
                $query->orderBy('messages_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $groups = $query->paginate(20);

        // Stats
        $totalGroups = CommunityGroup::count();
        $activeGroups = CommunityGroup::where('is_active', true)->count();
        $inactiveGroups = CommunityGroup::where('is_active', false)->count();
        $featuredGroups = CommunityGroup::where('is_featured', true)->count();
        
        // Get total members and messages
        $totalMembers = \DB::table('community_members')->count();
        $totalMessages = \DB::table('community_messages')->count();

        return view('super-admin.communities.index', compact(
            'groups',
            'totalGroups',
            'activeGroups',
            'inactiveGroups',
            'totalMembers',
            'totalMessages',
            'featuredGroups'
        ));
    }

    public function show(CommunityGroup $group)
    {
        $group->load(['creator', 'members.user', 'messages.user']);
        $group->loadCount([
            'members as members_count' => function($query) {
                $query->where('group_id', 'community_groups.id');
            },
            'messages as messages_count' => function($query) {
                $query->where('group_id', 'community_groups.id');
            }
        ]);
        
        return view('super-admin.communities.show', compact('group'));
    }

    public function destroy(CommunityGroup $group)
    {
        // Delete all messages first
        $group->messages()->delete();
        
        // Delete all members
        $group->members()->delete();
        
        // Delete the group
        $group->delete();

        return redirect()->route('super-admin.communities.index')
            ->with('success', 'Community group deleted successfully!');
    }

    public function toggleStatus(CommunityGroup $group)
    {
        $group->is_active = !$group->is_active;
        $group->save();

        $status = $group->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Community group {$status} successfully!");
    }

    public function toggleFeature(CommunityGroup $group)
    {
        $group->is_featured = !$group->is_featured;
        $group->save();

        $status = $group->is_featured ? 'featured' : 'unfeatured';
        return redirect()->back()->with('success', "Community group {$status} successfully!");
    }
}