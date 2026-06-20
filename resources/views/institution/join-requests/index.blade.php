@extends('layouts.institution')

@section('title', 'Join Requests')

@section('content')

@php
    // Security check
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
@endphp

<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">👥 Join Requests</h1>
            <p class="text-gray-500 text-sm mt-1">Manage requests to join {{ $institution->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('institution.dashboard') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm">
                <i class="ti ti-dashboard"></i> Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
        <p class="text-gray-500 text-sm">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-gray-500">
        <p class="text-gray-500 text-sm">Total</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
    </div>
</div>

<!-- Search & Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search by name or email..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
            <i class="ti ti-search"></i> Filter
        </button>
        <a href="{{ route('institution.join-requests.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
            <i class="ti ti-x"></i> Clear
        </a>
    </form>
</div>

<!-- Requests Table -->
@if($requests->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requested</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($requests as $request)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr($request->user->full_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $request->user->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $request->message ? Str::limit($request->message, 60) : 'No message provided' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $request->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700'
                            ];
                            $statusIcons = [
                                'pending' => '⏳',
                                'approved' => '✅',
                                'rejected' => '❌'
                            ];
                            $color = $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700';
                            $icon = $statusIcons[$request->status] ?? '';
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ $icon }} {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('institution.join-requests.show', $request->id) }}" 
                               class="text-purple-600 hover:text-purple-800 transition" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if($request->status === 'pending')
                                <form method="POST" action="{{ route('institution.join-requests.approve', $request->id) }}" 
                                      onsubmit="return confirm('Approve {{ $request->user->full_name }} to join?')" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 transition" title="Approve">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $request->id }})" 
                                        class="text-red-600 hover:text-red-800 transition" title="Reject">
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
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Join Requests</h3>
    <p class="text-gray-500">There are no join requests for your institution at the moment.</p>
</div>
@endif

<!-- ========================================== -->
<!-- REJECT MODAL                               -->
<!-- ========================================== -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">❌ Reject Join Request</h3>
                <button onclick="closeRejectModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="rejectForm" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Reason for Rejection (Optional)
                    </label>
                    <textarea name="rejection_reason" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                              placeholder="Why are you rejecting this request?"></textarea>
                </div>
                <p class="text-xs text-gray-400">This reason will be visible to the user.</p>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg transition font-semibold">
                    Reject Request
                </button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
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