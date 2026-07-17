@extends('layouts.library')

@section('title', $institution->name . ' - Library')

@section('content')

<!-- ========================================== -->
<!-- HERO SECTION                               -->
<!-- ========================================== -->
<div class="library-hero">
    <div class="hero-content">
        <div class="flex flex-col items-center text-center gap-3">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="ti ti-library text-purple-200 text-2xl"></i>
                    <h1 class="text-2xl md:text-3xl font-bold text-white font-playfair">
                        @if($institution->type === 'bookstore')
                            Welcome to {{ $institution->name }} Bookstore
                        @else
                            Welcome to {{ $institution->name }}
                        @endif
                    </h1>
                </div>
                <p class="text-purple-100/80 text-sm md:text-base mx-auto whitespace-nowrap">
                    @if($institution->type === 'bookstore')
                        {{ $institution->description ?? 'Browse our collection of books available for purchase' }}
                    @else
                        {{ $institution->description ?? 'Explore thousands of books - Find books by category, shelf, or author' }}
                    @endif
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
                        
                        <div class="shelf-bay" onclick="window.location='{{ route('institution.public.shelf.show', [$institution->id, $shelf->id]) }}'">
                            <!-- Debug: Show book count -->
                            <div style="position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.7); color: #34d399; font-size: 9px; padding: 2px 8px; border-radius: 10px; z-index: 20; font-weight: bold;">
                                {{ $shelf->books_count ?? 0 }}
                            </div>
                            
                            <div class="shelf-bay-label">
                                <span class="shelf-bay-code">{{ $shelf->code }}</span>
                                <span class="shelf-bay-count">{{ $shelf->books_count ?? 0 }} books</span>
                            </div>
                            <p class="shelf-bay-name">{{ $shelf->name }}</p>

                            <div class="book-spines-container">
                                @forelse($visibleBooks as $book)
                                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}"
                                       class="spine-book-cover" title="{{ $book->title }}">
                                        @if($book->cover_image)
                                           <img src="{{ url('media/' . $book->cover_image) }}" 
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
<!-- POPULAR BOOKS & RECENTLY ADDED             -->
<!-- ========================================== -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

    <!-- Popular Books -->
    <div id="popular">
        <h2 class="section-title">
            <i class="ti ti-trending-up text-purple-300"></i> 
            {{ $institution->type === 'bookstore' ? 'Bestsellers' : 'Popular Books' }}
        </h2>
        <div class="featured-shelf">
            <div class="featured-books-row">
                @php $popularBooks = $books->sortByDesc('views_count')->take(6); @endphp
                @forelse($popularBooks as $i => $book)
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" class="featured-book">
                        <div class="featured-book-cover">
                            <span class="featured-book-rank">{{ $i + 1 }}</span>
                            @if($institution->type === 'bookstore')
                                <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                            @else
                                @if($book->is_paid)
                                    <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                                @else
                                    <span class="featured-book-badge free">FREE</span>
                                @endif
                            @endif
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}">
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
            <i class="ti ti-clock text-purple-300"></i> New Arrivals
        </h2>
        <div class="featured-shelf">
            <div class="featured-books-row">
                @php $recentBooks = $books->sortByDesc('created_at')->take(6); @endphp
                @forelse($recentBooks as $book)
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" class="featured-book">
                        <div class="featured-book-cover">
                            @if($institution->type === 'bookstore')
                                <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                            @else
                                @if($book->is_paid)
                                    <span class="featured-book-badge paid">TSh {{ number_format($book->price) }}</span>
                                @else
                                    <span class="featured-book-badge free">FREE</span>
                                @endif
                            @endif
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}">
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
<!-- ALL BOOKS GRID                             -->
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
                            <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-book text-4xl text-purple-400/40"></i>
                            </div>
                        @endif
                        
                        @if($institution->type === 'bookstore')
                            <span class="absolute top-2 right-2 badge-paid text-xs">TSh {{ number_format($book->price) }}</span>
                        @else
                            @if($book->is_paid)
                                <span class="absolute top-2 right-2 badge-paid text-xs">💰</span>
                            @else
                                <span class="absolute top-2 right-2 badge-free text-xs">FREE</span>
                            @endif
                        @endif
                    </div>
                    <div class="mt-2">
                        <p class="book-title text-sm truncate group-hover:text-purple-300">{{ $book->title }}</p>
                        <p class="book-author truncate">{{ $book->author ?? 'Unknown' }}</p>
                        @if($institution->type === 'bookstore')
                            <p class="text-xs text-emerald-400/60 mt-1">
                                TSh {{ number_format($book->price, 2) }}
                            </p>
                        @elseif($book->shelf_number)
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
        @if($institution->type === 'bookstore')
            No books available in this bookstore yet
        @else
            No books available in this library yet
        @endif
    </div>
@endif

<!-- ========================================== -->
<!-- LIBRARY LOCATION                           -->
<!-- ========================================== -->
@if($location || $institution->address)
    <div class="library-card p-4 mt-8">
        <h3 class="font-semibold text-white flex items-center gap-2">
            <i class="ti ti-map-pin text-purple-400"></i> 
            @if($institution->type === 'bookstore')
                Bookstore Location
            @else
                Library Location
            @endif
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

<!-- ========================================== -->
<!-- STYLES - FORCE BOOK SPINES VISIBLE        -->
<!-- ========================================== -->
<style>
/* Force book spines to be visible */
.shelf-bay .book-spines-container {
    min-height: 100px !important;
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: flex-end !important;
    gap: 4px !important;
    padding: 4px 2px !important;
    overflow: visible !important;
}

.shelf-bay .spine-book-cover {
    display: block !important;
    flex: 0 0 20px !important;
    min-width: 20px !important;
    height: 120px !important;
    border-radius: 2px 2px 0 0 !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    overflow: hidden !important;
    position: relative !important;
}

.shelf-bay .spine-book-cover img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    display: block !important;
}

.shelf-bay .spine-fallback-cover {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(135deg, #7c3aed, #4f46e5) !important;
}

.shelf-bay .spine-fallback-cover span {
    color: white !important;
    font-size: 8px !important;
    writing-mode: vertical-rl !important;
    text-orientation: mixed !important;
    font-weight: 700 !important;
}

.shelf-bay .empty-shelf-text {
    color: rgba(255,255,255,0.2) !important;
    font-size: 0.6rem !important;
    align-self: center !important;
    padding-bottom: 10px !important;
    font-weight: 500 !important;
}

/* Debug badge */
.shelf-bay .debug-count {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(0,0,0,0.7);
    color: #34d399;
    font-size: 9px;
    padding: 2px 8px;
    border-radius: 10px;
    z-index: 20;
    font-weight: bold;
}

/* Add color variation for book spines without covers */
.shelf-bay .spine-book-cover:nth-child(6n+1) { background: linear-gradient(135deg, #7c3aed, #4f46e5) !important; }
.shelf-bay .spine-book-cover:nth-child(6n+2) { background: linear-gradient(135deg, #dc2626, #b91c1c) !important; }
.shelf-bay .spine-book-cover:nth-child(6n+3) { background: linear-gradient(135deg, #059669, #047857) !important; }
.shelf-bay .spine-book-cover:nth-child(6n+4) { background: linear-gradient(135deg, #d97706, #b45309) !important; }
.shelf-bay .spine-book-cover:nth-child(6n+5) { background: linear-gradient(135deg, #2563eb, #1d4ed8) !important; }
.shelf-bay .spine-book-cover:nth-child(6n+6) { background: linear-gradient(135deg, #7c3aed, #4f46e5) !important; }

/* Responsive */
@media (max-width: 640px) {
    .shelf-bay .spine-book-cover {
        height: 80px !important;
        min-width: 14px !important;
        flex: 0 0 14px !important;
    }
    .shelf-bay .book-spines-container {
        min-height: 70px !important;
    }
}
</style>

@endsection