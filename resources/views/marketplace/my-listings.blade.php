@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Listings</h1>
            <p class="text-gray-600">Manage your books for sale</p>
        </div>
        <a href="{{ route('marketplace.create') }}" class="bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
            <i class="ti ti-plus"></i> New Listing
        </a>
    </div>
    
    @if($listings->count() > 0)
        <div class="space-y-4">
            @foreach($listings as $listing)
                <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                    <div class="w-16 h-20 bg-jlibrary-100 rounded-lg flex items-center justify-center">
                        <i class="ti ti-book text-2xl text-jlibrary-600"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $listing->title }}</h3>
                        <p class="text-gray-500 text-sm">${{ number_format($listing->price, 2) }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            @if($listing->status === 'approved')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Approved</span>
                            @elseif($listing->status === 'pending')
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded">Pending</span>
                            @else
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">{{ ucfirst($listing->status) }}</span>
                            @endif
                            <span class="text-xs text-gray-500"><i class="ti ti-eye"></i> {{ $listing->views }} views</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('marketplace.show', $listing) }}" class="text-jlibrary-600 hover:text-jlibrary-700">
                            View
                        </a>
                        <form method="POST" action="{{ route('marketplace.destroy', $listing) }}" 
                              onsubmit="return confirm('Delete this listing?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-shopping-cart text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Listings Yet</h3>
            <p class="text-gray-500">Start selling your books today!</p>
            <a href="{{ route('marketplace.create') }}" class="inline-block mt-4 bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                Create Listing
            </a>
        </div>
    @endif
</div>
@endsection