@extends('layouts.librarian')

@section('title', 'Join Request Details')

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
    
    if (!isset($joinRequest) || !$joinRequest) {
        abort(404, 'Join request not found.');
    }
    
    if ($joinRequest->institution_id != $institution->id) {
        abort(403, 'This request does not belong to your institution.');
    }
    
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
    $color = $statusColors[$joinRequest->status] ?? 'bg-gray-100 text-gray-700';
    $icon = $statusIcons[$joinRequest->status] ?? '';
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('institution.join-requests.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Join Requests
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-xl font-bold text-white">Join Request Details</h1>
                    <p class="text-purple-200 text-sm">Request from {{ $joinRequest->user->full_name }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $color }}">
                    {{ $icon }} {{ ucfirst($joinRequest->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ substr($joinRequest->user->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-800">{{ $joinRequest->user->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $joinRequest->user->email }}</p>
                        <p class="text-xs text-gray-400">Requested: {{ $joinRequest->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Details -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Status</p>
                    <p class="mt-1">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                            {{ $icon }} {{ ucfirst($joinRequest->status) }}
                        </span>
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Requested On</p>
                    <p class="text-gray-800 font-medium">{{ $joinRequest->created_at->format('F d, Y h:i A') }}</p>
                    <p class="text-xs text-gray-400">{{ $joinRequest->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Message -->
            @if($joinRequest->message)
                <div class="mt-4 bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-xs text-blue-600 uppercase font-semibold">User's Message</p>
                    <p class="text-gray-800 mt-1">{{ $joinRequest->message }}</p>
                </div>
            @endif

            <!-- Rejection Reason -->
            @if($joinRequest->status === 'rejected' && $joinRequest->rejection_reason)
                <div class="mt-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                    <p class="text-xs text-red-600 uppercase font-semibold">Rejection Reason</p>
                    <p class="text-red-700">{{ $joinRequest->rejection_reason }}</p>
                </div>
            @endif

            <!-- Approval Info -->
            @if($joinRequest->status === 'approved' && $joinRequest->approved_at)
                <div class="mt-4 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                    <p class="text-xs text-green-600 uppercase font-semibold">Approved</p>
                    <p class="text-green-700">Approved on {{ $joinRequest->approved_at->format('F d, Y h:i A') }}</p>
                    <p class="text-sm text-green-600">{{ $joinRequest->user->full_name }} is now a member of {{ $institution->name }}</p>
                </div>
            @endif

            <!-- Actions (only for pending requests) -->
            @if($joinRequest->status === 'pending')
                <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="ti ti-settings text-purple-600"></i> Review Decision
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('institution.join-requests.approve', $joinRequest->id) }}" 
                              onsubmit="return confirm('Approve {{ $joinRequest->user->full_name }} to join {{ $institution->name }}?')">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="ti ti-check"></i> Approve
                            </button>
                        </form>

                        <button onclick="openRejectModal({{ $joinRequest->id }})" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="ti ti-x"></i> Reject
                        </button>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('institution.join-requests.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition">
                    <i class="ti ti-arrow-left"></i> Back to Join Requests
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
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