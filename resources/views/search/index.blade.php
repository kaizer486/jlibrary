@extends('layouts.app')

@section('title', 'Advanced Search')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-search text-purple-600"></i>
                Advanced Search
            </h1>
            <p class="text-gray-500 mt-1">Find your next favorite book</p>
        </div>

        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-5 sticky top-24">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="ti ti-filter"></i>
                        Filters
                    </h3>
                    
                    <form method="GET" action="{{ route('search.index') }}" id="filter-form">
                        <!-- Search Input -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Title, author, or keyword..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>

                        <!-- Price Type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Type</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="price" value="" {{ !request('price') ? 'checked' : '' }} 
                                           class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm text-gray-600">All Books</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="price" value="free" {{ request('price') === 'free' ? 'checked' : '' }}
                                           class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm text-gray-600">Free</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="price" value="paid" {{ request('price') === 'paid' ? 'checked' : '' }}
                                           class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm text-gray-600">Paid</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range Slider -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range (TSh)</label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="Min" class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="Max" class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <!-- Minimum Rating -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                            <select name="rating" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="">Any rating</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★★★★ & up</option>
                                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★★★ & up</option>
                                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★★ & up</option>
                                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★ & up</option>
                            </select>
                        </div>

                        <!-- Sort By -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                                <option value="rating_high" {{ request('sort') === 'rating_high' ? 'selected' : '' }}>Highest Rated</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <!-- Stats -->
                        <div class="pt-4 border-t">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Total Books:</span>
                                <span class="font-semibold">{{ $totalBooks }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-500">Free Books:</span>
                                <span class="font-semibold text-green-600">{{ $freeBooks }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Paid Books:</span>
                                <span class="font-semibold text-purple-600">{{ $paidBooks }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                Apply Filters
                            </button>
                            <a href="{{ route('search.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div class="lg:col-span-3">
                <!-- Results Count -->
                <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-600">
                            Found <span class="font-semibold text-purple-600">{{ $books->total() }}</span> books
                            @if(request('search') || request('price') || request('rating'))
                                matching your criteria
                            @endif
                        </p>
                        <p class="text-sm text-gray-400">Showing {{ $books->firstItem() }}-{{ $books->lastItem() }} of {{ $books->total() }}</p>
                    </div>
                </div>

                <!-- Books Grid -->
                @if($books->count() > 0)
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($books as $book)
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                                <!-- Book Cover -->
                                <div class="relative h-40 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    @if($book->cover_image)
                                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ti ti-book text-5xl text-white/50"></i>
                                    @endif
                                    
                                    @if($book->is_paid)
                                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 rounded-lg text-xs font-semibold">
                                            TSh {{ number_format($book->price, 2) }}
                                        </div>
                                    @else
                                        <div class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-semibold">
                                            Free
                                        </div>
                                    @endif
                                    
                                    <!-- Rating Badge -->
                                    @if($book->averageRating() > 0)
                                        <div class="absolute bottom-2 left-2 bg-black/50 backdrop-blur-sm px-2 py-1 rounded-lg text-white text-xs flex items-center gap-1">
                                            <i class="ti ti-star-filled text-yellow-400 text-xs"></i>
                                            {{ $book->averageRating() }}
                                            <span class="text-white/70">({{ $book->ratingCount() }})</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800 mb-1 line-clamp-1">{{ $book->title }}</h3>
                                    <p class="text-gray-500 text-sm mb-2">{{ $book->author }}</p>
                                    <p class="text-gray-600 text-xs line-clamp-2 mb-3">{{ Str::limit($book->description, 80) }}</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <i class="ti ti-download mr-1"></i>
                                            {{ number_format($book->downloads) }}
                                        </div>
                                        <a href="{{ route('library.show', $book) }}" 
                                           class="bg-purple-600 text-white px-3 py-1 rounded-lg hover:bg-purple-700 transition text-sm">
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
                        <i class="ti ti-books text-6xl text-gray-300 mb-4 block"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No books found</h3>
                        <p class="text-gray-500">Try adjusting your search or filter criteria</p>
                        <a href="{{ route('search.index') }}" class="inline-block mt-4 text-purple-600 hover:underline">
                            Clear all filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection