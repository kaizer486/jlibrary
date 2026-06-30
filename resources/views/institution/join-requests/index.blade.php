@extends('layouts.librarian')

@section('title', 'Join Requests')
@section('page-title', '👥 Join Requests')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Manage requests to join {{ $institution->name }}</p>
        </div>
        <a href="{{ route('institution.dashboard') }}" class="btn-library-outline">
            <i class="ti ti-dashboard"></i> Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">⏳ Pending</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['approved'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">✅ Approved</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $stats['rejected'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">❌ Rejected</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-slate-500">
            <p class="text-2xl font-bold text-slate-400">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📊 Total</p>
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
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('institution.join-requests.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
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
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Message</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Requested</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                    @foreach($requests as $request)
    <tr class="hover:bg-slate-800/50 transition">
        <td class="px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">
                        @if($request->user)
                            {{ substr($request->user->full_name, 0, 1) }}
                        @else
                            <i class="ti ti-user text-white text-xs"></i>
                        @endif
                    </span>
                </div>
                <div>
                    <p class="font-medium text-white">
                        @if($request->user)
                            {{ $request->user->full_name }}
                        @else
                            <span class="text-red-400">User Deleted</span>
                        @endif
                    </p>
                    <p class="text-xs text-slate-400">
                        @if($request->user)
                            {{ $request->user->email }}
                        @else
                            <span class="text-red-400/60">User ID: {{ $request->user_id }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </td>
                    
                    <td class="px-4 py-3 text-slate-400 text-sm">
                                    {{ $request->created_at->diffForHumans() }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/20',
                                            'approved' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
                                            'rejected' => 'bg-red-500/20 text-red-400 border border-red-500/20'
                                        ];
                                        $statusIcons = [
                                            'pending' => '⏳',
                                            'approved' => '✅',
                                            'rejected' => '❌'
                                        ];
                                        $color = $statusColors[$request->status] ?? 'bg-slate-700 text-slate-300 border border-slate-600';
                                        $icon = $statusIcons[$request->status] ?? '';
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                        {{ $icon }} {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('institution.join-requests.show', $request->id) }}" 
                                           class="text-purple-400 hover:text-purple-300 transition" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if($request->status === 'pending')
                                            <form method="POST" action="{{ route('institution.join-requests.approve', $request->id) }}" 
                                                  onsubmit="return confirm('Approve {{ $request->user->full_name }} to join?')" class="inline">
                                                @csrf
                                                <button type="submit" class="text-emerald-400 hover:text-emerald-300 transition" title="Approve">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $request->id }})" 
                                                    class="text-red-400 hover:text-red-300 transition" title="Reject">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $requests->links() }}</div>
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-users text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white/60 mb-2">No Join Requests</h3>
            <p class="text-slate-400">There are no join requests for your institution at the moment.</p>
        </div>
    @endif

</div>

<!-- ========================================== -->
<!-- REJECT MODAL                               -->
<!-- ========================================== -->
<div id="rejectModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl max-w-md w-full mx-4 overflow-hidden shadow-2xl">
        <div class="bg-gradient-to-r from-red-900/30 to-red-800/30 px-6 py-4 border-b border-slate-700">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">❌ Reject Join Request</h3>
                <button onclick="closeRejectModal()" class="text-slate-400 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="rejectForm" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Reason for Rejection (Optional)
                    </label>
                    <textarea name="rejection_reason" rows="3" 
                              class="search-bar"
                              placeholder="Why are you rejecting this request?"></textarea>
                </div>
                <p class="text-xs text-slate-400">This reason will be visible to the user.</p>
            </div>
            <div class="flex gap-3 mt-6 pt-4 border-t border-slate-700">
                <button type="submit" class="btn-library flex-1 justify-center">
                    <i class="ti ti-x"></i> Reject Request
                </button>
                <button type="button" onclick="closeRejectModal()" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(requestId) {
    const form = document.getElementById('rejectForm');
    form.action = `/institution/join-requests/${requestId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

document.getElementById('rejectModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>

@endsection