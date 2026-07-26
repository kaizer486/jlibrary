@extends('layouts.author')

@section('title', $listing->title)
@section('page-title', 'Listing Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('seller.listings') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left"></i> Back to My Listings
        </a>
    </div>
    
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left Column - Book Cover -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="h-64 bg-gradient-to-r from-jlibrary-500 to-jlibrary-700 flex items-center justify-center">
                    @if($listing->cover_image)
                        <img src="{{ Storage::url($listing->cover_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="ti ti-book text-8xl text-white/50"></i>
                    @endif
                </div>
                
                <div class="p-6">
                    <div class="text-3xl font-bold text-jlibrary-600 mb-4">
                        TSh {{ number_format($listing->price, 2) }}
                    </div>
                    
                    @auth
                        @if($isSeller)
                            <div class="mb-4 p-3 bg-gray-100 rounded-lg text-center">
                                <p class="text-sm text-gray-600">This is your listing</p>
                                <div class="flex gap-2 mt-2 justify-center">
                                    <a href="{{ route('marketplace.edit', $listing) }}" class="text-blue-600 text-sm">Edit</a>
                                    <form method="POST" action="{{ route('marketplace.destroy', $listing) }}" 
                                          onsubmit="return confirm('Delete this listing?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @elseif($listing->status === 'approved')
                            <a href="{{ route('marketplace.download', $listing) }}" 
                               class="block text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                                <i class="ti ti-download"></i> Download Book
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                            Login to Purchase
                        </a>
                    @endauth
                    
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <p><i class="ti ti-eye"></i> {{ number_format($listing->views ?? 0) }} views</p>
                        <p><i class="ti ti-download"></i> {{ number_format($listing->downloads ?? 0) }} downloads</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Book Details -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $listing->title }}</h1>
                <p class="text-gray-600 mb-4">by {{ $listing->seller->full_name }}</p>
                
                @if($listing->status === 'pending')
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded-lg mb-4">
                        <i class="ti ti-clock"></i> This listing is pending admin approval
                    </div>
                @endif
                
                <div class="prose max-w-none">
                    <h2 class="text-xl font-semibold mb-2">Description</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $listing->description }}</p>
                </div>
                
                <div class="mt-6 pt-4 border-t">
                    <h3 class="font-semibold mb-2">About the Seller</h3>
                    <p class="text-gray-600">{{ $listing->seller->full_name }}</p>
                    <p class="text-sm text-gray-500">Member since {{ $listing->seller->created_at->format('F Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection