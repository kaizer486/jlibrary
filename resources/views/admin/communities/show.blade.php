@extends('layouts.admin')

@section('title', $group->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.communities.index') }}" class="text-indigo-600 hover:text-indigo-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Communities
        </a>
    </div>

    <!-- Group Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="h-48 bg-gradient-to-r from-indigo-600 to-purple-600 relative">
            @if($group->cover_image)
                <img src="{{ url('media/' . $group->cover_image) }}" alt="{{ $group->name }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-6">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $group->name }}</h1>
                    <p class="text-indigo-100 text-sm">
                        <i class="ti ti-users"></i> {{ number_format($group->members_count ?? 0) }} members
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Left Column - Info -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">Group Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Description</p>
                        <p class="text-gray-700">{{ $group->description }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Created By</p>
                        <p class="text-gray-700">{{ $group->creator->full_name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Created</p>
                        <p class="text-gray-700">{{ $group->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Status</p>
                        <p>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $group->is_active ? '✅ Active' : '❌ Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Admin Actions -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">Admin Actions</h3>
                <div class="space-y-2">
                    <form action="{{ route('admin.communities.toggle-status', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-sm px-4 py-2 rounded-lg transition
                            {{ $group->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            {{ $group->is_active ? '❌ Deactivate Group' : '✅ Activate Group' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.communities.destroy', $group) }}" 
                          onsubmit="return confirm('Delete this group permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-sm bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="ti ti-trash"></i> Delete Group
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Members -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-users text-indigo-600"></i>
                        Members ({{ number_format($group->members_count ?? 0) }})
                    </h3>
                </div>
                <div class="space-y-2">
                    @forelse($group->members as $member)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ substr($member->user->full_name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $member->user->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->user->email ?? '' }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $member->joined_at->diffForHumans() ?? '' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No members in this group</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection