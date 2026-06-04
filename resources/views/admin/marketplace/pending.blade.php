@extends('layouts.master')

@section('page-content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Pending Marketplace Listings</h1>
    <a href="{{ route('admin.marketplace.all') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
        View All Listings
    </a>
</div>

@if(isset($listings) && $listings->count() > 0)
<div class="space-y-4">
    @foreach($listings as $listing)
    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-16 h-20 bg-jlibrary-100 rounded-lg flex items-center justify-center">
                <i class="ti ti-book text-2xl text-jlibrary-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">{{ $listing->title }}</h3>
                <p class="text-sm text-gray-500">by {{ $listing->seller->full_name }}</p>
                <p class="text-sm text-jlibrary-600 font-semibold">${{ number_format($listing->price, 2) }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.marketplace.approve', $listing) }}">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="ti ti-check"></i> Approve
                </button>
            </form>
            <button onclick="showRejectModal({{ $listing->id }})" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                <i class="ti ti-x"></i> Reject
            </button>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $listings->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-check text-6xl text-green-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Pending Listings</h3>
    <p class="text-gray-500">All marketplace listings have been reviewed.</p>
</div>
@endif

<!-- Reject Modal -->
<div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Listing</h3>
        <form id="reject-form" method="POST">
            @csrf
            <textarea name="admin_notes" rows="3" class="w-full px-4 py-2 border rounded-lg mb-4" placeholder="Reason for rejection (optional)"></textarea>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
    function showRejectModal(listingId) {
        const modal = document.getElementById('reject-modal');
        const form = document.getElementById('reject-form');
        form.action = '/admin/marketplace/' + listingId + '/reject';
        modal.classList.remove('hidden');
    }
    
    function closeRejectModal() {
        const modal = document.getElementById('reject-modal');
        modal.classList.add('hidden');
    }
</script>
@endsection