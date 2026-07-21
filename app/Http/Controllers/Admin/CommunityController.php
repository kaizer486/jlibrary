<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityGroup::with('creator')->withCount(['members' => function($query) {
            $query->where('group_id', 'community_groups.id');
        }]);

        // Search filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
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
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $groups = $query->paginate(20);

        // Stats
        $totalGroups = CommunityGroup::count();
        $totalMembers = \DB::table('community_members')->count();

        return view('admin.communities.index', compact('groups', 'totalGroups', 'totalMembers'));
    }

    public function show(CommunityGroup $group)
    {
        $group->load(['creator', 'members.user', 'messages.user']);
        $group->loadCount(['members' => function($query) {
            $query->where('group_id', 'community_groups.id');
        }, 'messages']);
        
        return view('admin.communities.show', compact('group'));
    }

    public function destroy(CommunityGroup $group)
    {
        $group->messages()->delete();
        $group->members()->delete();
        $group->delete();

        return redirect()->route('admin.communities.index')
            ->with('success', 'Community group deleted successfully!');
    }

    public function toggleStatus(CommunityGroup $group)
    {
        $group->is_active = !$group->is_active;
        $group->save();

        $status = $group->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Community group {$status} successfully!");
    }
}