@extends('layouts.super-admin')

@section('title', $listing->title)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.marketplace.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Marketplace
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        @if($listing->type === 'notes')
                            <i class="ti ti-notes text-white text-2xl"></i>
                        @elseif($listing->type === 'ebook')
                            <i class="ti ti-book text-white text-2xl"></i>
                        @elseif($listing->type === 'course')
                            <i class="ti ti-video text-white text-2xl"></i>
                        @else
                            <i class="ti ti-package text-white text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $listing->title }}</h1>
                        <p class="text-purple-200">{{ $listing->type_label }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($listing->status === 'pending')
                        <form method="POST" action="{{ route('super-admin.marketplace.approve', $listing) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl transition text-sm flex items-center gap-2">
                                <i class="ti ti-check"></i> Approve
                            </button>
                        </form>
                        <button onclick="showRejectModal()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl transition text-sm flex items-center gap-2">
                            <i class="ti ti-x"></i> Reject
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-file-description text-purple-600"></i> Description
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed">{{ $listing->description ?? 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Seller Information -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-user-circle text-purple-600"></i> Seller Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <i class="ti ti-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 text-lg">{{ $listing->seller->full_name ?? 'N/A' }}</p>
                            <p class="text-gray-500">{{ $listing->seller->email ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500 mt-1">Member since {{ $listing->seller->created_at->format('F Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Listing Details -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-info-circle text-purple-600"></i> Listing Details
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Price</span>
                        <span class="font-semibold text-blue-600 text-lg">TSh {{ number_format($listing->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Status</span>
                        <span>
                            @if($listing->status === 'approved')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                            @elseif($listing->status === 'pending')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Institution</span>
                        <span class="text-gray-700">{{ $listing->institution->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Type</span>
                        <span class="capitalize">{{ $listing->type }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Submitted</span>
                        <span class="text-gray-500">{{ $listing->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Downloads</span>
                        <span class="text-gray-700">{{ number_format($listing->downloads ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-chart-bar text-purple-600"></i> Performance
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($listing->views ?? 0) }}</p>
                            <p class="text-xs text-gray-500">Views</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">{{ number_format($listing->sales_count ?? 0) }}</p>
                            <p class="text-xs text-gray-500">Sales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Info (if rejected) -->
    @if($listing->status === 'rejected' && $listing->rejection_reason)
    <div class="mt-6 bg-red-50 rounded-xl border border-red-200 overflow-hidden">
        <div class="px-6 py-3 bg-red-100 border-b border-red-200">
            <h3 class="font-semibold text-red-700 flex items-center gap-2">
                <i class="ti ti-alert-triangle"></i> Rejection Reason
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700">{{ $listing->rejection_reason }}</p>
            <p class="text-xs text-gray-400 mt-2">Reviewed by {{ $listing->reviewer->full_name ?? 'Admin' }} on {{ $listing->reviewed_at->format('F d, Y') }}</p>
        </div>
    </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-gradient-to-r from-red-600 to-red-700 p-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Reject Listing</h3>
                <button onclick="closeRejectModal()" class="text-white/80 hover:text-white">
                    <i class="ti ti-x text-2xl"></i>
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('super-admin.marketplace.reject', $listing) }}">
            @csrf
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required placeholder="Please provide a reason..."></textarea>
                <div class="flex gap-3 mt-6">
                    <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">Confirm Rejection</button>
                    <button type="button" onclick="closeRejectModal()" class="flex-1 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Close modal on click outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection