@extends('layouts.super-admin')

@section('title', 'Manage Communities')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <i class="ti ti-users text-2xl text-yellow-400"></i>
                <h1 class="text-2xl font-bold text-gray-800">Manage Communities</h1>
                <span class="ml-2 px-2 py-1 text-xs font-semibold bg-red-100 text-red-700 rounded-full">Super Admin</span>
            </div>
            <p class="text-gray-500 text-sm">Manage all community groups across the platform</p>
        </div>
        <a href="{{ route('community.create') }}" class="bg-gradient-to-r from-yellow-500 to-red-500 text-white px-4 py-2 rounded-lg transition text-sm flex items-center gap-2 hover:shadow-lg">
            <i class="ti ti-plus"></i> Create Group
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">Total Groups</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalGroups ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($activeGroups ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">Inactive</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($inactiveGroups ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">Total Members</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalMembers ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">Total Messages</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalMessages ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 border-l-4 border-pink-500 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-xs uppercase">⭐ Featured</p>
            <p class="text-2xl font-bold text-pink-600">{{ number_format($featuredGroups ?? 0) }}</p>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('super-admin.communities.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Search by name or description..." 
                       value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-400">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
                </select>
            </div>
            <div>
                <select name="sort" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="members" {{ request('sort') == 'members' ? 'selected' : '' }}>Most Members</option>
                    <option value="messages" {{ request('sort') == 'messages' ? 'selected' : '' }}>Most Messages</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                </select>
            </div>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">Filter</button>
            <a href="{{ route('super-admin.communities.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center">Clear</a>
        </form>
    </div>

    <!-- Groups Table -->
    @if(isset($groups) && $groups->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Group</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Featured</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($groups as $group)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-400 to-red-500 flex items-center justify-center flex-shrink-0">
                                    <i class="ti ti-users text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ Str::limit($group->name, 30) }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($group->description, 40) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $group->creator->full_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-blue-600">{{ number_format($group->members_count ?? 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($group->messages_count ?? 0) }}</td>
                        <td class="px-6 py-4">
                            <form action="{{ route('super-admin.communities.toggle-status', $group) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 rounded-full text-xs font-semibold cursor-pointer
                                    {{ $group->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                    {{ $group->is_active ? '✅ Active' : '❌ Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('super-admin.communities.toggle-feature', $group) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-lg cursor-pointer
                                    {{ $group->is_featured ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-300 hover:text-yellow-400' }}">
                                    {{ $group->is_featured ? '⭐' : '☆' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $group->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('super-admin.communities.show', $group) }}" 
                                   class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('community.show', $group) }}" 
                                   class="text-green-600 hover:text-green-800" title="Open in Community">
                                    <i class="ti ti-external-link"></i>
                                </a>
                                <form method="POST" action="{{ route('super-admin.communities.destroy', $group) }}" 
                                      class="inline" 
                                      onsubmit="return confirm('Delete this group permanently? All messages and data will be lost.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $groups->appends(request()->query())->links() }}
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Communities Found</h3>
        <p class="text-gray-500">Communities will appear here when users create them.</p>
        <!-- DEBUG: Show count -->
        @php
            $debugCount = \App\Models\CommunityGroup::count();
        @endphp
        <p class="text-xs text-gray-400 mt-2">Debug: Total groups in database: {{ $debugCount }}</p>
    </div>
    @endif
</div>
@endsection