@extends('layouts.librarian')

@section('title', 'Members Directory')
@section('page-title', '👥 Members Directory')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">View and manage library members</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('librarian.members.export') }}" class="btn-library-outline">
                <i class="ti ti-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Stats - Dark Glassmorphism -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">Total Members</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">✅ Active</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">⏳ Pending Approval</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-pink-500">
            <p class="text-2xl font-bold text-pink-400">{{ $stats['librarians'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📚 Librarians</p>
        </div>
    </div>

    <!-- Search & Filters - Dark Glassmorphism -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by name or email..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="role" class="search-bar w-auto">
                <option value="">All Roles</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>👤 Member</option>
                <option value="librarian" {{ request('role') == 'librarian' ? 'selected' : '' }}>📚 Librarian</option>
                <option value="institution_admin" {{ request('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Admin</option>
            </select>
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('librarian.members.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Members Table - Dark Glassmorphism -->
    @if($members->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Member</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Role</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Joined</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($members as $member)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($member->full_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-white">{{ $member->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $member->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium
                                        @if($member->hasRole('librarian')) bg-purple-500/20 text-purple-300 border border-purple-500/20
                                        @elseif($member->hasRole('institution_admin')) bg-blue-500/20 text-blue-300 border border-blue-500/20
                                        @else bg-slate-700 text-slate-300 border border-slate-600 @endif">
                                        {{ $member->getRoleLabel() ?? '👤 Member' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($member->email_verified_at)
                                        <span class="badge-approved">✅ Active</span>
                                    @else
                                        <span class="badge-pending">⏳ Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-sm">{{ $member->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('librarian.members.show', $member) }}" class="text-purple-400 hover:text-purple-300 transition" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if(auth()->user()->canManageUser($member))
                                            <a href="{{ route('librarian.members.edit', $member) }}" class="text-blue-400 hover:text-blue-300 transition" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('librarian.members.update-role', $member) }}" class="inline">
                                                @csrf
                                                <select name="role" class="text-xs bg-slate-800 border border-slate-700 rounded-lg px-2 py-1 text-white focus:ring-1 focus:ring-purple-500" onchange="this.form.submit()">
                                                    <option value="user" {{ $member->hasRole('user') ? 'selected' : '' }}>👤 User</option>
                                                    <option value="librarian" {{ $member->hasRole('librarian') ? 'selected' : '' }}>📚 Librarian</option>
                                                    <option value="institution_admin" {{ $member->hasRole('institution_admin') ? 'selected' : '' }}>🏢 Admin</option>
                                                </select>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $members->withQueryString()->links() }}
        </div>
        
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-users text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white mb-2">No Members Found</h3>
            <p class="text-slate-400">No members have joined your library yet.</p>
        </div>
    @endif

</div>

@endsection