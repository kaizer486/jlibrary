@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">All Marketplace Listings</h1>
    <a href="{{ route('admin.marketplace.pending') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
        View Pending
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seller</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($listings as $listing)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $listing->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $listing->seller->full_name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($listing->price, 2) }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs 
                        {{ $listing->status === 'approved' ? 'bg-green-100 text-green-700' : 
                           ($listing->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $listing->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $listings->links() }}
</div>
@endsection