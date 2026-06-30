@extends('layouts.librarian')

@section('title', 'Members Directory')
@section('page-title', '👥 Members Directory')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">View all members of {{ $institution->name }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.members.create') }}" class="btn-library">
                <i class="ti ti-plus"></i> Add Member
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $allMembers->count() }}</p>
            <p class="text-xs text-slate-400">👥 Total Members</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">
                {{ $allMembers->whereNotNull('email_verified_at')->count() }}
            </p>
            <p class="text-xs text-slate-400">✅ Active</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-400">
                {{ $allMembers->whereNull('email_verified_at')->count() }}
            </p>
            <p class="text-xs text-slate-400">⏳ Pending</p>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name or email..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="role" class="search-bar w-auto">
                <option value="">All Roles</option>
                @foreach($roleLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.members.directory') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Members by Role -->
    @if($groupedMembers->count() > 0)
        <div class="space-y-6">
            @foreach($groupedMembers as $role => $roleMembers)
                @if($roleMembers->count() > 0)
                    @php
                        $label = $roleLabels[$role] ?? ucfirst($role) . 's';
                        $color = $roleColors[$role] ?? 'from-slate-600 to-slate-700';
                        $badgeColor = $roleBadgeColors[$role] ?? 'bg-slate-700 text-slate-300';
                        $badgeLabel = $roleBadgeLabels[$role] ?? ucfirst($role);
                    @endphp
                    
                    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
                        <div class="bg-gradient-to-r {{ $color }} px-6 py-3 border-b border-slate-700">
                            <div class="flex justify-between items-center">
                                <h2 class="text-white font-semibold flex items-center gap-2 text-sm">
                                    <i class="ti ti-users"></i> {{ $label }}
                                </h2>
                                <span class="text-white/80 text-xs bg-white/20 px-2 py-0.5 rounded-full">
                                    {{ $roleMembers->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="divide-y divide-slate-800">
                            @foreach($roleMembers as $member)
                                <div class="flex items-center gap-4 p-3 hover:bg-slate-800/50 transition">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm">{{ substr($member->full_name, 0, 1) }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-white text-sm">{{ $member->full_name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $member->email }}</p>
                                        <p class="text-xs text-slate-500">{{ $member->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        @if($member->email_verified_at)
                                            <span class="badge-approved text-xs">✅ Active</span>
                                        @else
                                            <span class="badge-pending text-xs">⏳ Pending</span>
                                        @endif
                                        <span class="text-xs {{ $badgeColor }} px-2 py-0.5 rounded-full block mt-1">
                                            {{ $badgeLabel }}
                                        </span>
                                    </div>
                                    @if(auth()->user()->canManageUser($member))
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <a href="{{ route('institution.members.edit', $member) }}" 
                                               class="text-blue-400 hover:text-blue-300 transition p-1" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            @if(auth()->user()->canDeleteUser($member) && auth()->user()->id !== $member->id)
                                                <form method="POST" action="{{ route('institution.members.destroy', $member) }}" 
                                                      onsubmit="return confirm('Remove {{ $member->full_name }} from the institution?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-300 transition p-1" title="Remove">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-users text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Members Found</h3>
            <p class="text-slate-400">No members have joined this institution yet.</p>
            <a href="{{ route('institution.members.create') }}" class="inline-block mt-4 btn-library">
                <i class="ti ti-plus"></i> Add First Member
            </a>
        </div>
    @endif

</div>

@endsection