@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">📚 Library</h1>
        <p class="text-gray-600">Discover thousands of books to read and learn from</p>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-8">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search by title or author..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                    <option value="">All Books</option>
                    <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                <button type="submit" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    <i class="ti ti-search"></i> Search
                </button>
                @if(request('search') || request('type'))
                    <a href="{{ route('library.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
    
    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group relative">
                    <!-- Book Cover -->
                    <div class="relative h-48 bg-gradient-to-br from-jlibrary-500 to-jlibrary-700 flex items-center justify-center">
                        @if($book->cover_image)
                            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                        @else
                            <i class="ti ti-book text-6xl text-white/50"></i>
                        @endif
                        
                        <!-- Price Badge -->
                        @if($book->is_paid)
                            <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-lg text-sm font-semibold">
                                ${{ number_format($book->price, 2) }}
                            </div>
                        @else
                            <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-lg text-sm font-semibold">
                                Free
                            </div>
                        @endif
                        
                        <!-- Bookmark Button -->
                        <div class="absolute bottom-2 right-2 z-10">
                            <x-bookmark-button :item="$book" type="book" size="sm" />
                        </div>
                    </div>
                    
                    <!-- Book Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-1">{{ $book->title }}</h3>
                        <p class="text-gray-500 text-sm mb-2">{{ $book->author }}</p>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($book->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="ti ti-download mr-1"></i>
                                <span>{{ number_format($book->downloads) }}</span>
                            </div>
                            <a href="{{ route('library.show', $book) }}" 
                               class="bg-jlibrary-600 text-white px-3 py-1 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No books found</h3>
            <p class="text-gray-500">Try adjusting your search or filter criteria</p>
            <a href="{{ route('library.index') }}" class="inline-block mt-4 text-jlibrary-600 hover:text-jlibrary-700">Clear filters</a>
        </div>
    @endif
</div>
@endsection