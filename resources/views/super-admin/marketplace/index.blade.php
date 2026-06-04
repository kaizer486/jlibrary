@extends('layouts.super-admin')

@section('title', 'Marketplace Management')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-shopping-cart text-purple-600"></i>
                Marketplace Management
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage all marketplace listings across the platform</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Listings</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalListings) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($pendingListings) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($approvedListings) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-red-500 shadow-sm">
        <p class="text-gray-500 text-sm">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ number_format($rejectedListings) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Sales</p>
        <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($totalSales, 2) }}</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('super-admin.marketplace.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by title or description..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Types</option>
                <option value="notes" {{ request('type') == 'notes' ? 'selected' : '' }}>📝 Study Notes</option>
                <option value="ebook" {{ request('type') == 'ebook' ? 'selected' : '' }}>📚 E-Book</option>
                <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>🎓 Course</option>
                <option value="template" {{ request('type') == 'template' ? 'selected' : '' }}>📄 Template</option>
                <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>📦 Other</option>
            </select>
        </div>
        <div>
            <select name="institution_id" class="px-4 py-2 border border-gray-200 rounded-lg bg-white">
                <option value="">All Institutions</option>
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('super-admin.marketplace.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Listings Table -->
@if($listings->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Listing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($listings as $listing)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-800">{{ Str::limit($listing->title, 40) }}</p>
                            <p class="text-xs text-gray-500">{{ $listing->type_label }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-800">{{ $listing->seller->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $listing->seller->email ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $listing->institution->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-blue-600">TSh {{ number_format($listing->price, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($listing->status === 'approved')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                        @elseif($listing->status === 'pending')
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($listing->sales_count ?? 0) }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('super-admin.marketplace.show', $listing) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            @if($listing->status === 'pending')
                                <form method="POST" action="{{ route('super-admin.marketplace.approve', $listing) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Approve">
                                        <i class="ti ti-check"></i>
                                    </button>
                                </form>
                                <button onclick="showRejectModal({{ $listing->id }})" class="text-red-600 hover:text-red-800" title="Reject">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                            <form method="POST" action="{{ route('super-admin.marketplace.destroy', $listing) }}" class="inline" onsubmit="return confirm('Delete this listing permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $listings->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-shopping-cart text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Marketplace Listings</h3>
    <p class="text-gray-500">Listings will appear here when users create them.</p>
</div>
@endif

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
        <form id="rejectForm" method="POST">
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
    let currentListingId = null;
    
    function showRejectModal(listingId) {
        currentListingId = listingId;
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/super-admin/marketplace/${listingId}/reject`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('rejectForm').reset();
    }
    
    // Close modal on click outside
    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectModal();
        }
    });
</script>
@endsection