@extends('layouts.super-admin')

@section('title', $group->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.communities.index') }}" class="text-yellow-600 hover:text-yellow-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Communities
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Group Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="h-48 bg-gradient-to-r from-yellow-500 to-red-500 relative">
            @if($group->cover_image)
                <img src="{{ url('media/' . $group->cover_image) }}" alt="{{ $group->name }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent flex items-end p-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-white">{{ $group->name }}</h1>
                        @if($group->is_featured)
                            <span class="px-2 py-1 text-xs font-semibold bg-yellow-500 text-white rounded-full">⭐ Featured</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 text-yellow-100 text-sm mt-1">
                        <span><i class="ti ti-users"></i> {{ number_format($group->members_count) }} members</span>
                        <span><i class="ti ti-message"></i> {{ number_format($group->messages_count) }} messages</span>
                    </div>
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
                        <p class="text-gray-700">{{ $group->description ?? 'No description' }}</p>
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
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $group->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $group->is_active ? '✅ Active' : '❌ Inactive' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Featured</p>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $group->is_featured ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $group->is_featured ? '⭐ Featured' : 'Not Featured' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Super Admin Actions -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3">Super Admin Actions</h3>
                <div class="space-y-2">
                    <form action="{{ route('super-admin.communities.toggle-status', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-sm px-4 py-2 rounded-lg transition
                            {{ $group->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            {{ $group->is_active ? '❌ Deactivate Group' : '✅ Activate Group' }}
                        </button>
                    </form>
                    <form action="{{ route('super-admin.communities.toggle-feature', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-sm px-4 py-2 rounded-lg transition
                            {{ $group->is_featured ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $group->is_featured ? '☆ Unfeature Group' : '⭐ Feature Group' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('super-admin.communities.destroy', $group) }}"
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

        <!-- Right Column - Members & Messages -->
        <div class="md:col-span-2 space-y-6">
            <!-- Members -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-users text-yellow-600"></i>
                        Members ({{ number_format($group->members_count) }})
                    </h3>
                </div>
                <div class="space-y-2">
                    @forelse($group->members as $member)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-red-500 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ substr($member->user->full_name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">{{ $member->user->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $member->user->email ?? '' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">{{ optional($member->joined_at)->diffForHumans() ?? '' }}</span>
                                @if((int) $member->user_id !== (int) $group->created_by)
                                    <form action="{{ route('super-admin.communities.members.remove', [$group, $member]) }}" method="POST"
                                          onsubmit="return confirm('Remove this member from the group?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" title="Remove member">
                                            <i class="ti ti-user-x"></i> Remove
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Creator</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No members in this group</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-message text-blue-600"></i>
                        Messages ({{ number_format($group->messages_count) }})
                    </h3>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($group->messages->sortByDesc('created_at')->take(20) as $message)
                        <div class="flex items-start gap-3 py-2 border-b border-gray-100 last:border-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ substr($message->user->full_name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start gap-2">
                                    <p class="font-medium text-gray-800 text-sm">{{ $message->user->full_name ?? 'Unknown' }}</p>
                                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 break-words">{{ \Illuminate\Support\Str::limit($message->message, 200) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No messages in this group</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection