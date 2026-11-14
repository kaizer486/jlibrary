<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunityGroup;
use App\Models\CommunityMember;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = CommunityGroup::with('creator')
            ->withCount(['members', 'messages']);

        // Search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort
        switch ($request->sort ?? 'latest') {
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
        $totalGroups   = CommunityGroup::count();
        $totalMembers  = \DB::table('community_members')->count();
        $totalMessages = \DB::table('community_messages')->count();

        return view('admin.communities.index', compact(
            'groups',
            'totalGroups',
            'totalMembers',
            'totalMessages'
        ));
    }

    public function show(CommunityGroup $group)
    {
        $group->load(['creator', 'members.user', 'messages.user']);
        $group->loadCount(['members', 'messages']);

        return view('admin.communities.show', compact('group'));
    }

    public function removeMember(CommunityGroup $group, CommunityMember $member)
    {
        // Guard: member must belong to this group
        if ((int) $member->group_id !== (int) $group->id) {
            abort(404);
        }

        // Guard: don't allow removing the creator
        if ((int) $member->user_id === (int) $group->created_by) {
            return back()->with('error', 'Cannot remove the group creator. Delete the group instead.');
        }

        $member->delete();

        return back()->with('success', 'Member removed from the group.');
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