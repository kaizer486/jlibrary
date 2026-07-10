@extends('layouts.app')

@section('title', 'My Listings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Marketplace Listings</h1>
        <a href="{{ route('marketplace.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="ti ti-plus"></i> Add New Listing
        </a>
    </div>

    @if($listings->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($listings as $listing)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden">
                    <div class="relative h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        @if($listing->cover_image)
                            <img src="{{ Storage::url($listing->cover_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="ti ti-book text-6xl text-white/50"></i>
                        @endif
                        
                        <div class="absolute top-2 right-2">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold
                                @if($listing->status === 'active') bg-green-500 text-white
                                @elseif($listing->status === 'pending') bg-yellow-500 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $listing->title }}</h3>
                        <p class="text-gray-500 text-sm">by {{ $listing->author }}</p>
                        <p class="text-purple-600 font-bold mt-2">TSh {{ number_format($listing->price, 2) }}</p>
                        
                        <div class="flex gap-2 mt-3">
                            <a href="{{ route('marketplace.edit', $listing) }}" class="flex-1 text-center bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition text-sm">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('marketplace.destroy', $listing) }}" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition text-sm" onclick="return confirm('Delete this listing?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $listings->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-package text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Listings Yet</h3>
            <p class="text-gray-500">You haven't created any marketplace listings.</p>
            <a href="{{ route('marketplace.create') }}" class="inline-block mt-4 bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                <i class="ti ti-plus"></i> Create Your First Listing
            </a>
        </div>
    @endif
</div>
@endsection