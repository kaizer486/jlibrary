@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Marketplace</h1>
            <p class="text-gray-600">Buy and sell books from other learners</p>
        </div>
        @auth
            <a href="{{ route('marketplace.create') }}" class="bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                <i class="ti ti-plus"></i> Sell Your Book
            </a>
        @endauth
    </div>
    
    @if(isset($listings) && $listings->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($listings as $listing)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <!-- Cover Image -->
                    <div class="h-48 bg-gradient-to-r from-jlibrary-500 to-jlibrary-700 flex items-center justify-center relative">
                        @if($listing->cover_image && Storage::exists($listing->cover_image))
                            <img src="{{ Storage::url($listing->cover_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="ti ti-book text-6xl text-white/50"></i>
                        @endif
                        <div class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-lg text-sm font-semibold">
                            ${{ number_format($listing->price, 2) }}
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-1">{{ $listing->title }}</h3>
                        <p class="text-gray-500 text-sm mb-2">by {{ $listing->seller->full_name ?? 'Unknown' }}</p>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($listing->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span><i class="ti ti-eye"></i> {{ number_format($listing->views ?? 0) }}</span>
                            <span><i class="ti ti-download"></i> {{ number_format($listing->downloads ?? 0) }}</span>
                        </div>
                        
                        <a href="{{ route('marketplace.show', $listing) }}" 
                           class="block text-center bg-jlibrary-600 text-white px-3 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $listings->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-shopping-cart text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Listings Yet</h3>
            <p class="text-gray-500">Be the first to sell your book on Marketplace!</p>
            @auth
                <a href="{{ route('marketplace.create') }}" class="inline-block mt-4 bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    Sell Your Book
                </a>
            @else
                <a href="{{ route('login') }}" class="inline-block mt-4 bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    Login to Sell
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection