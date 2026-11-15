@extends('layouts.app')

@section('content')
<div class="container mx-auto px-2 py-3 md:px-4 md:py-6">
    <!-- Header -->
    <div class="mb-3 md:mb-6">
        <h1 class="text-lg md:text-3xl font-bold text-gray-900 mb-0.5 md:mb-1">📚 Global Library</h1>
        <p class="text-[10px] md:text-sm text-gray-600">Discover thousands of books from all libraries and institutions</p>
    </div>
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg md:rounded-xl shadow-sm p-2 md:p-4 mb-3 md:mb-6">
        <form method="GET" action="{{ route('library.index') }}" class="flex flex-col gap-1.5 md:gap-3">
           <div class="flex flex-col md:flex-row gap-1.5 md:gap-3 items-start md:items-center">
    <div class="w-full md:flex-1">
        <input type="text" name="search" placeholder="Search..." 
               value="{{ request('search') }}"
               class="w-full px-2 md:px-4 py-1 md:py-2 text-[10px] md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
    </div>
    
    <!-- JLIBRARY MARKET BUTTON — right after search, before category -->
    <a href="{{ request('format') === 'hardcopy' 
                ? route('library.index', request()->except('format', 'page')) 
                : route('library.index', array_merge(request()->except('page'), ['format' => 'hardcopy'])) }}"
       class="w-full md:w-auto inline-flex items-center justify-center gap-1 px-3 md:px-4 py-1 md:py-2 text-[10px] md:text-sm rounded-lg transition whitespace-nowrap border font-medium
              {{ request('format') === 'hardcopy'
                  ? 'bg-jlibrary-600 text-white border-jlibrary-600 hover:bg-jlibrary-700'
                  : 'bg-white text-jlibrary-600 border-jlibrary-300 hover:bg-jlibrary-50' }}">
        <i class="ti ti-building-store"></i>
        <span class="hidden sm:inline">JLIBRARY MARKET</span>
        <span class="sm:hidden">Market</span>
    </a>
    
    <div class="w-full md:flex-1 md:max-w-xs">
        <select name="category" class="w-full px-2 md:px-4 py-1 md:py-2 text-[10px] md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 bg-white">
            <option value="">📚 All Categories</option>
            <!-- ...all your existing optgroups stay the same... -->
        </select>
    </div>
    
    <button type="submit" class="w-full md:w-auto bg-jlibrary-600 text-white px-3 md:px-5 py-1 md:py-2 text-[10px] md:text-sm rounded-lg hover:bg-jlibrary-700 transition whitespace-nowrap">
        <i class="ti ti-search"></i> <span class="hidden sm:inline">Search</span>
    </button>
    
    @if(request('search') || request('category') || request('format'))
        <a href="{{ route('library.index') }}" class="w-full md:w-auto bg-gray-200 text-gray-700 px-3 md:px-4 py-1 md:py-2 text-[10px] md:text-sm rounded-lg hover:bg-gray-300 transition whitespace-nowrap text-center">
            <i class="ti ti-x"></i> <span class="hidden sm:inline">Clear</span>
        </a>
    @endif
</div>

        </form>
    </div>
    
        @if(request('format') === 'hardcopy')
        <div class="bg-jlibrary-50 border-l-4 border-jlibrary-600 rounded-r-lg px-3 md:px-4 py-2 md:py-3 mb-3 md:mb-4">
            <p class="text-xs md:text-sm text-gray-700">
                <strong class="text-jlibrary-800">Physical books available to order. Select any book and fill in your delivery details to place an order.
            </p>
        </div>
    @endif
    
    <!-- Books Grid -->
    @if($books->count() > 0)
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-1 md:gap-2 lg:gap-3">
            @foreach($books as $book)
                @php
                    if ($book->institution_id) {
                        $bookUrl = route('institution.public.show', [
                            'institutionId' => $book->institution_id,
                            'book' => $book->id
                        ]);
                    } else {
                        $bookUrl = route('library.show', $book->id);
                    }
                @endphp
                
                <div class="bg-white rounded-md md:rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-200 hover:border-jlibrary-400 max-w-[90px] md:max-w-[180px] lg:max-w-[200px] xl:max-w-[220px] mx-auto w-full">
                    <a href="{{ $bookUrl }}" class="block">
                        <div class="relative w-full aspect-[2/3] max-h-[100px] md:max-h-[200px] lg:max-h-[220px] bg-gradient-to-br from-jlibrary-500 to-jlibrary-700 flex items-center justify-center overflow-hidden">
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" 
                                     alt="{{ $book->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <i class="ti ti-book text-lg md:text-4xl text-white/40 group-hover:scale-110 transition-transform duration-300"></i>
                            @endif
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 hidden md:flex items-end justify-center pb-2.5">
                                <span class="text-white text-[11px] font-semibold bg-jlibrary-600/80 px-3 py-1 rounded-full">
                                    <i class="ti ti-eye"></i> View
                                </span>
                            </div>
                            
                            <!-- Institution Badge -->
                            <div class="absolute bottom-0.5 left-0.5 bg-black/70 text-white px-0.5 md:px-1.5 py-0.5 rounded text-[5px] md:text-[9px] max-w-[20px] md:max-w-[65px] truncate">
                                @if($book->institution_id && $book->institution)
                                    <i class="ti ti-building text-[5px] md:text-[9px]"></i> 
                                    <span class="hidden sm:inline">{{ Str::limit($book->institution->name ?? '', 6) }}</span>
                                @else
                                    🌐
                                @endif
                            </div>
                            
                            <!-- Bookmark Button -->
                            <div class="absolute top-0.5 right-0.5 z-10 scale-75 md:scale-100">
                                <x-bookmark-button :item="$book" type="book" size="xs" />
                            </div>
                        </div>
                    </a>
                    
                    <!-- Book Info -->
                    <div class="p-0.5 md:p-2 text-center">
                        <a href="{{ $bookUrl }}" class="block">
                            <h3 class="font-semibold text-[6px] md:text-xs text-gray-800 mb-0 line-clamp-1 hover:text-jlibrary-600 transition">{{ Str::limit($book->title, 8) }}</h3>
                        </a>
                        <p class="text-[5px] md:text-[10px] text-gray-500 mb-0 line-clamp-1 hidden sm:block">{{ $book->author ?? 'Unknown' }}</p>
                        
                        <div class="flex items-center justify-center gap-0.5 md:gap-2 text-[5px] md:text-[10px] mt-0.5 md:mt-1">
                            <span class="flex items-center gap-0.5 text-blue-600 font-medium">
                                <i class="ti ti-eye text-blue-500 text-[6px] md:text-[11px]"></i> 
                                <span class="hidden md:inline">{{ number_format($book->views_count ?? 0) }}</span>
                            </span>
                            <span class="text-gray-300 text-[5px] md:text-[10px] hidden md:inline">|</span>
                            <span class="flex items-center gap-0.5 text-green-600 font-medium">
                                <i class="ti ti-download text-green-500 text-[6px] md:text-[11px]"></i> 
                                <span class="hidden md:inline">{{ number_format($book->downloads ?? 0) }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-3 md:mt-6">
            {{ $books->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-12 text-center">
            <i class="ti ti-books text-3xl md:text-6xl text-gray-400 mb-2 md:mb-4 block"></i>
            <h3 class="text-base md:text-xl font-semibold mb-1 md:mb-2">No books found</h3>
            <p class="text-xs md:text-sm text-gray-500">
                @if(request('format') === 'hardcopy')
                    No hardcopy books match your search. Try clearing filters.
                @else
                    Try adjusting your search or filter criteria
                @endif
            </p>
            <a href="{{ route('library.index') }}" class="inline-block mt-2 md:mt-4 text-xs md:text-sm text-jlibrary-600 hover:text-jlibrary-700">Clear filters</a>
        </div>
    @endif
</div>
@endsection