@extends('layouts.library')

@section('title', $institution->name . ' - Library')

@section('content')

<!-- ========================================== -->
<!-- HERO SECTION - Wood-Trimmed Library Sign   -->
<!-- ========================================== -->
<div class="library-hero">
    <div class="hero-content">
        <div class="flex flex-col items-center text-center gap-3">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="ti ti-library text-purple-200 text-2xl"></i>
                    <h1 class="text-2xl md:text-3xl font-bold text-white font-playfair">
                        Welcome to {{ $institution->name }}
                    </h1>
                </div>
              <p class="text-purple-100/80 text-sm md:text-base mx-auto whitespace-nowrap">
    {{ $institution->description ?? 'Explore thousands of books - Find books by category, shelf, or author' }}
</p>
                @if($location)
                    <p class="text-purple-100/60 text-sm mt-1">
                        <i class="ti ti-map-pin"></i> {{ $location->address ?? $institution->address }}
                        @if($location->city), {{ $location->city }}@endif
                    </p>
                @endif
            </div>

        </div>
        <!-- Search -->
        <div class="mt-6">
            <form method="GET" class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" placeholder="Search for books..."
                           value="{{ request('search') }}"
                           class="search-bar">
                </div>
                <div>
                    <select name="shelf" class="search-bar">
                        <option value="">All Shelves</option>
                        @foreach($shelves as $shelf)
                            <option value="{{ $shelf->code }}" {{ request('shelf') == $shelf->code ? 'selected' : '' }}>
                                {{ $shelf->code }} - {{ $shelf->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="category" class="search-bar">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-library">
                    <i class="ti ti-search"></i> Search
                </button>
               
            </form>

            <div class="flex flex-wrap gap-1 mt-3">
                <a href="#shelves" class="browse-tab browse-tab-active">
                    <i class="ti ti-layout-grid"></i> Browse Shelves
                </a>
                <a href="#popular" class="browse-tab">
                    <i class="ti ti-trending-up"></i> Popular Books
                </a>
                <a href="#recent" class="browse-tab">
                    <i class="ti ti-clock"></i> New Arrivals
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SHELF LAYOUT - BOOKSHELF RACK              -->
<!-- ========================================== -->
@if($shelves->count() > 0)
    <div id="shelves" class="mb-10">
        <h2 class="section-title">
            <i class="ti ti-layout-grid text-purple-300"></i> Shelf Layout
        </h2>

        <div class="bookshelf-rack">
            @php
                $shelfRows = $shelves->take(12)->chunk(4);
            @endphp

            @foreach($shelfRows as $row)
                <div class="shelf-row">
                    @foreach($row as $shelf)
                        @php
                            $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;
                            $shelfBooks = $shelf->books ?? collect();
                            $visibleBooks = $shelfBooks->take(8);
                            $overflow = $shelfBooks->count() - $visibleBooks->count();
                        @endphp
                        
                        {{-- ✅ REMOVED <a> - using <div> instead --}}
                        <div class="shelf-bay" onclick="window.location='{{ route('institution.public.shelf.show', [$institution->id, $shelf->id]) }}'">
                            <div class="shelf-bay-label">
                                <span class="shelf-bay-code">{{ $shelf->code }}</span>
                                <span class="shelf-bay-count">{{ $shelf->books_count }} books</span>
                            </div>
                            <p class="shelf-bay-name">{{ $shelf->name }}</p>

                            <!-- ========================================== -->
                            <!-- BOOK COVERS - FIXED HEIGHT CONTAINER      -->
                            <!-- ========================================== -->
                            <div class="book-spines-container">
                                @forelse($visibleBooks as $book)
                                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}"
                                       class="spine-book-cover" title="{{ $book->title }}">
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                                 alt="{{ $book->title }}"
                                                 class="spine-book-img">
                                        @else
                                            <div class="spine-fallback-cover">
                                                <span>{{ Str::limit($book->title, 8, '') }}</span>
                                            </div>
                                        @endif
                                    </a>
                                @empty
                                    <span class="empty-shelf-text">Empty shelf</span>
                                @endforelse

                                @if($overflow > 0)
                                    <div class="spine-overflow-cover">+{{ $overflow }}</div>
                                @endif
                            </div>

                            <div class="shelf-capacity">
                                <span class="dot"></span>
                                <div class="bar">
                                    <div class="bar-fill" style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                            </div>
                            
                            @if($shelf->floor)
                                <p class="text-xs text-white mt-1">
                                    <i class="ti ti-map-pin text-[10px]"></i> {{ $shelf->floor }}
                                </p>
                            @endif
                        </div>
                        {{-- ✅ END OF shelf-bay div --}}
                    @endforeach
                </div>
            @endforeach
        </div>
        
        @if($shelves->count() > 12)
            <div class="text-center mt-3">
                <span class="text-sm text-white/30">+ {{ $shelves->count() - 12 }} more shelves</span>
            </div>
        @endif
    </div>
@else
    <div id="shelves" class="mb-10">
        <h2 class="section-title">
            <i class="ti ti-layout-grid text-purple-300"></i> Shelf Layout
        </h2>
        <div class="bookshelf-rack text-center py-12 text-white/30">
            <i class="ti ti-books text-4xl block mb-2 text-purple-400/30"></i>
            No shelves available yet
        </div>
    </div>
@endif

<!-- ========================================== -->
<!-- POPULAR BOOKS & RECENTLY ADDED - PRO SHELF -->
<!-- ========================================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

    <!-- Popular Books -->
    <div id="popular">
        <h2 class="section-title">
            <i class="ti ti-trending-up text-purple-300"></i> Popular Books
        </h2>
        <div class="featured-shelf">
            <div class="featured-books-row">
                @php $popularBooks = $books->sortByDesc('views_count')->take(6); @endphp
                @forelse($popularBooks as $i => $book)
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" class="featured-book">
                        <div class="featured-book-cover">
                            <span class="featured-book-rank">{{ $i + 1 }}</span>
                            @if($book->is_paid)
                                <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                            @else
                                <span class="featured-book-badge free">FREE</span>
                            @endif
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}">
                            @else
                                <div class="placeholder-icon">
                                    <i class="ti ti-book text-3xl text-white/60"></i>
                                </div>
                            @endif
                        </div>
                        <p class="featured-book-title">{{ $book->title }}</p>
                        <p class="featured-book-author">{{ $book->author ?? 'Unknown' }}</p>
                        <p class="featured-book-meta">
                            <i class="ti ti-eye"></i> {{ number_format($book->views_count ?? 0) }} views
                        </p>
                    </a>
                @empty
                    <div class="featured-empty">No popular books yet</div>
                @endforelse
            </div>
            <div class="featured-shelf-beam"></div>
        </div>
    </div>

    <!-- Recently Added -->
    <div id="recent">
        <h2 class="section-title">
            <i class="ti ti-clock text-purple-300"></i> Recently Added
        </h2>
        <div class="featured-shelf">
            <div class="featured-books-row">
                @php $recentBooks = $books->sortByDesc('created_at')->take(6); @endphp
                @forelse($recentBooks as $book)
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" class="featured-book">
                        <div class="featured-book-cover">
                            @if($book->is_paid)
                                <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                            @else
                                <span class="featured-book-badge free">FREE</span>
                            @endif
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}">
                            @else
                                <div class="placeholder-icon">
                                    <i class="ti ti-book text-3xl text-white/60"></i>
                                </div>
                            @endif
                        </div>
                        <p class="featured-book-title">{{ $book->title }}</p>
                        <p class="featured-book-author">{{ $book->author ?? 'Unknown' }}</p>
                        <p class="featured-book-meta muted">
                            <i class="ti ti-calendar"></i> {{ $book->created_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <div class="featured-empty">No recent books</div>
                @endforelse
            </div>
            <div class="featured-shelf-beam"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- ALL BOOKS GRID - WOODEN SHELF STYLE        -->
<!-- ========================================== -->
@if($books->count() > 0)
    <div class="mb-8">
        <h2 class="section-title">
            <i class="ti ti-books text-purple-400"></i> All Books
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach($books as $book)
                <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
                   class="book-shelf-card p-3 hover:shadow-lg transition group">
                    <div class="aspect-[2/3] bg-purple-900/20 rounded-lg overflow-hidden relative">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-book text-4xl text-purple-400/40"></i>
                            </div>
                        @endif
                        @if($book->is_paid)
                            <span class="absolute top-2 right-2 badge-paid text-xs">💰</span>
                        @else
                            <span class="absolute top-2 right-2 badge-free text-xs">FREE</span>
                        @endif
                    </div>
                    <div class="mt-2">
                        <p class="book-title text-sm truncate group-hover:text-purple-300">{{ $book->title }}</p>
                        <p class="book-author truncate">{{ $book->author ?? 'Unknown' }}</p>
                        @if($book->shelf_number)
                            <p class="text-xs text-purple-400/40 mt-1">
                                <i class="ti ti-map-pin text-[10px]"></i> {{ $book->shelf_number }}
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $books->appends(request()->query())->links() }}
        </div>
    </div>
@else
    <div class="book-shelf-card p-12 text-center text-white/40">
        No books available in this library yet
    </div>
@endif

<!-- ========================================== -->
<!-- LIBRARY LOCATION                           -->
<!-- ========================================== -->
@if($location || $institution->address)
    <div class="library-card p-4 mt-8">
        <h3 class="font-semibold text-white flex items-center gap-2">
            <i class="ti ti-map-pin text-purple-400"></i> Library Location
        </h3>
        <p class="text-white text-sm mt-1">
            {{ $institution->address ?? 'Address not specified' }}
            @if($institution->city), {{ $institution->city }}@endif
            @if($institution->region), {{ $institution->region }}@endif
        </p>
        <div class="flex flex-wrap gap-4 mt-2 text-sm text-white/40">
            @if($institution->phone)
                <span><i class="ti ti-phone"></i> {{ $institution->phone }}</span>
            @endif
            @if($institution->email)
                <span><i class="ti ti-mail"></i> {{ $institution->email }}</span>
            @endif
        </div>
    </div>
@endif

@endsection