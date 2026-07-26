@extends('layouts.author')

@section('title', 'My Listings')
@section('page-title', 'My Marketplace Listings')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Listings</h2>
            <p class="text-gray-500">Manage your marketplace products</p>
        </div>
        <a href="{{ route('marketplace.create') }}" class="btn-author flex items-center gap-2">
            <i class="ti ti-plus"></i>
            Add New Listing
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Listings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $listings->total() }}</p>
                </div>
                <div class="stat-icon bg-orange-100 text-orange-600">
                    <i class="ti ti-shopping-bag"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Sales</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                    <i class="ti ti-book text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $listing->title }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($listing->description, 50) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800">TSh {{ number_format($listing->price, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2.5 py-1 rounded-full {{ $listing->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $listing->orders_count ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('marketplace.edit', $listing) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">Edit</a>
                                <form action="{{ route('marketplace.destroy', $listing) }}" method="POST" class="inline" onsubmit="return confirm('Delete this listing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="ti ti-shopping-bag-off text-4xl mb-2 block text-gray-300"></i>
                            <p>No listings yet.</p>
                            <a href="{{ route('marketplace.create') }}" class="text-orange-600 hover:underline text-sm mt-1 inline-block">Create your first listing</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($listings->hasPages())
        <div class="px-4 py-3 border-t">
            {{ $listings->links() }}
        </div>
        @endif
    </div>
</div>
@endsection