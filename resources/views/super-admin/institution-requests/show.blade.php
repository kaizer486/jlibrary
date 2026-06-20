@extends('layouts.super-admin')

@section('title', 'Request Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.institution-requests.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Requests
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h1 class="text-xl font-bold text-white">Institution Creation Request</h1>
                    <p class="text-blue-100 text-sm">Review request from {{ $request->user->full_name }}</p>
                </div>
                {!! $request->status_badge !!}
            </div>
        </div>

        <div class="p-6">
            <!-- Request Details -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Institution Name</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $request->name }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Type</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $request->type_label }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Requested By</p>
                    <p class="text-gray-800 font-medium">{{ $request->user->full_name }}</p>
                    <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Submitted</p>
                    <p class="text-gray-800">{{ $request->created_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>

            <!-- Contact Details -->
            @if($request->email || $request->phone)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Contact Information</p>
                    <div class="grid md:grid-cols-2 gap-4 mt-2">
                        @if($request->email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-gray-800">{{ $request->email }}</p>
                            </div>
                        @endif
                        @if($request->phone)
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="text-gray-800">{{ $request->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Location -->
            @if($request->city || $request->region || $request->address)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Location</p>
                    <div class="mt-2 space-y-1">
                        @if($request->city || $request->region)
                            <p class="text-gray-800">{{ $request->city ?? '' }}{{ $request->city && $request->region ? ', ' : '' }}{{ $request->region ?? '' }}</p>
                        @endif
                        @if($request->address)
                            <p class="text-gray-800 text-sm">{{ $request->address }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Description -->
            @if($request->description)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Description</p>
                    <p class="text-gray-800 mt-1">{{ $request->description }}</p>
                </div>
            @endif

            <!-- Motivation -->
            @if($request->motivation)
                <div class="mt-4 bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-xs text-blue-600 uppercase font-semibold">Motivation</p>
                    <p class="text-gray-800 mt-1">{{ $request->motivation }}</p>
                </div>
            @endif

            <!-- Website -->
            @if($request->website)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Website</p>
                    <a href="{{ $request->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $request->website }}</a>
                </div>
            @endif

            <!-- Document -->
            @if($request->document_path)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Supporting Document</p>
                    <a href="{{ route('super-admin.institution-requests.download', $request->id) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2">
                        <i class="ti ti-file"></i> Download Document
                    </a>
                </div>
            @endif

            <!-- Rejection Reason (if rejected) -->
            @if($request->status === 'rejected' && $request->rejection_reason)
                <div class="mt-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                    <p class="text-xs text-red-600 uppercase font-semibold">Rejection Reason</p>
                    <p class="text-red-700">{{ $request->rejection_reason }}</p>
                </div>
            @endif

            <!-- Approval Actions (only for pending requests) -->
            @if($request->status === 'pending')
                <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="ti ti-settings text-purple-600"></i> Review Decision
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('super-admin.institution-requests.approve', $request->id) }}" 
                              onsubmit="return confirm('Are you sure you want to approve this request? This will create the institution and make {{ $request->user->full_name }} the Institution Admin.')">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                                <i class="ti ti-check"></i> Approve & Create Institution
                            </button>
                        </form>

                        <button onclick="openRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                            <i class="ti ti-x"></i> Reject Request
                        </button>
                    </div>
                </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6 text-center">
                <a href="{{ route('super-admin.institution-requests.index') }}" class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition">
                    <i class="ti ti-arrow-left"></i> Back to Requests
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
                <h3 class="text-xl font-bold text-white">❌ Reject Request</h3>
                <button onclick="closeRejectModal()" class="text-white/80 hover:text-white transition">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('super-admin.institution-requests.reject', $request->id) }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Reason for Rejection <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="3" required 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Please provide a reason for rejecting this request..."></textarea>
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
function openRejectModal() {
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