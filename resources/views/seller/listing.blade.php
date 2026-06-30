@extends('layouts.app')

@section('title', 'My Books')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Books</h1>
        <a href="{{ route('marketplace.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if($listings->count() > 0)
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($listings as $listing)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                <div class="relative h-40 bg-gradient-to-r from-purple-100 to-pink-100">
                    @if($listing->cover_image)
                        <img src="{{ asset('storage/' . $listing->cover_image) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ti ti-book text-5xl text-purple-400"></i>
                        </div>
                    @endif
                    
                    <span class="absolute top-2 right-2 px-2 py-1 rounded-full text-xs 
                        {{ $listing->status === 'approved' ? 'bg-green-500 text-white' : 'bg-yellow-500 text-white' }}">
                        {{ ucfirst($listing->status) }}
                    </span>
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800">{{ $listing->title }}</h3>
                    <p class="text-sm text-gray-500">by {{ $listing->author }}</p>
                    <p class="text-lg font-bold text-purple-600 mt-2">TSh {{ number_format($listing->price, 2) }}</p>
                    
                    <div class="flex items-center gap-2 mt-3">
                        <span class="text-xs text-gray-500">{{ $listing->sales_count }} sales</span>
                        <span class="text-xs text-gray-500">•</span>
                        <span class="text-xs text-gray-500">{{ $listing->views }} views</span>
                    </div>
                    
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('marketplace.edit', $listing) }}" class="flex-1 text-center px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 text-sm">
                            Edit
                        </a>
                        <form action="{{ route('marketplace.destroy', $listing) }}" method="POST" onsubmit="return confirm('Delete this listing?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-sm">
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
            <i class="ti ti-package-off text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Yet</h3>
            <p class="text-gray-500">You haven't uploaded any books to the marketplace.</p>
            <a href="{{ route('marketplace.create') }}" class="mt-4 inline-block bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                Upload Your First Book
            </a>
        </div>
    @endif
</div>
@endsection