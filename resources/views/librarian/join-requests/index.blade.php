@extends('layouts.librarian')

@section('title', 'Join Requests')
@section('page-title', '👥 Join Requests')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Manage member join requests</p>
        </div>
        <div>
            <span class="text-xs bg-yellow-500/20 text-yellow-400 px-3 py-1.5 rounded-full">
                {{ $pendingCount }} pending requests
            </span>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-400">{{ $pendingCount }}</p>
            <p class="text-xs text-slate-400">⏳ Pending</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $approvedCount }}</p>
            <p class="text-xs text-slate-400">✅ Approved</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $rejectedCount }}</p>
            <p class="text-xs text-slate-400">Rejected</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}> Rejected</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('librarian.join-requests') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Requests Table -->
    @if($requests->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Message</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Requested</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($requests as $request)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($request->user->full_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-white">{{ $request->user->full_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $request->user->email }}</td>
                                <td class="px-4 py-3 text-slate-400 max-w-[200px] truncate">
                                    {{ $request->message ?? 'No message' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($request->status === 'pending')
                                        <span class="badge-pending">⏳ Pending</span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge-approved">✅ Approved</span>
                                    @else
                                        <span class="badge-rejected">❌ Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-400 text-sm">{{ $request->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($request->status === 'pending')
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('librarian.join-requests.approve', $request) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-emerald-400 hover:text-emerald-300 transition" title="Approve">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('librarian.join-requests.reject', $request) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Reject">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500">
                                            {{ $request->updated_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $requests->withQueryString()->links() }}
        </div>
        
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-user-plus text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Join Requests</h3>
            <p class="text-slate-500">No pending join requests at the moment.</p>
        </div>
    @endif

</div>

@endsection