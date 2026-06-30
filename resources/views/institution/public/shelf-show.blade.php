@extends('layouts.library')

@section('title', $shelf->name . ' - ' . $institution->name)

@section('content')

<!-- Back Button -->
<div class="mb-4">
    <a href="{{ route('institution.public.index', $institution->id) }}" 
       class="text-purple-300/70 hover:text-purple-200 transition inline-flex items-center gap-1 text-sm">
        <i class="ti ti-arrow-left"></i> Back to Library
    </a>
</div>

<!-- ========================================== -->
<!-- SHELF HEADER                               -->
<!-- ========================================== -->
<div class="library-card p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl md:text-3xl font-bold text-white font-playfair">
                    📚 {{ $shelf->name }}
                </h1>
                <span class="text-sm px-3 py-1 rounded-full 
                    @if($shelf->status === 'full') bg-red-500/20 text-red-400
                    @elseif($shelf->status === 'active') bg-emerald-500/20 text-emerald-400
                    @else bg-gray-500/20 text-gray-400 @endif">
                    {{ ucfirst($shelf->status) }}
                </span>
            </div>
            <p class="text-purple-100/60 text-sm mt-1">
                <i class="ti ti-hash"></i> Code: {{ $shelf->code }}
                @if($shelf->floor)
                    | <i class="ti ti-map-pin"></i> Floor: {{ $shelf->floor }}
                @endif
                @if($shelf->section)
                    | Section: {{ $shelf->section }}
                @endif
            </p>
            @if($shelf->description)
                <p class="text-white/40 text-sm mt-2">{{ $shelf->description }}</p>
            @endif
        </div>
        <div class="flex items-center gap-4 text-sm">
            <div class="text-center">
                <p class="text-2xl font-bold text-purple-300">{{ $books->total() }}</p>
                <p class="text-xs text-white/40">Books</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-purple-300">{{ $shelf->current_count }}/{{ $shelf->capacity }}</p>
                <p class="text-xs text-white/40">Capacity</p>
            </div>
        </div>
    </div>
    
    <!-- Capacity Progress -->
    <div class="mt-4">
        @php
            $percentage = $shelf->capacity > 0 ? round(($shelf->current_count / $shelf->capacity) * 100) : 0;
        @endphp
        <div class="flex justify-between text-xs text-white/40 mb-1">
            <span>Capacity Usage</span>
            <span>{{ $percentage }}%</span>
        </div>
        <div class="w-full bg-white/10 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 
                @if($percentage >= 90) bg-red-500
                @elseif($percentage >= 70) bg-purple-500
                @else bg-emerald-500 @endif" 
                style="width: {{ $percentage }}%"></div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- VISUAL BOOKSHELF WITH BOOK SPINES          -->
<!-- ========================================== -->
<div class="library-card p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <i class="ti ti-books text-purple-400"></i>
            Shelf View ({{ $books->count() }} / {{ $shelf->capacity }} books)
        </h3>
    </div>

    @if($books->count() > 0)
        <div class="bookshelf-rack">
            <!-- Shelf Board (Top) -->
            <div class="shelf-board"></div>
            
            <!-- Books on Shelf -->
            <div class="books-on-shelf">
                @foreach($books as $book)
                    <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
                       class="book-spine-wrapper" 
                       title="{{ $book->title }} - {{ $book->author }}">
                        <div class="book-spine" 
                             style="height: {{ rand(60, 100) }}px; width: {{ rand(18, 30) }}px; 
                                    background: {{ $book->cover_image ? 'url('.asset('storage/'.$book->cover_image).') center/cover' : 'linear-gradient(135deg, #7c3aed, #4f46e5)' }};">
                            @if(!$book->cover_image)
                                <span class="book-spine-title">{{ strtoupper(substr($book->title, 0, 3)) }}</span>
                            @endif
                        </div>
                    </a>
                @endforeach
                
                <!-- Empty spaces on shelf -->
                @php
                    $emptySpaces = $shelf->capacity - $books->count();
                @endphp
                @for($i = 0; $i < $emptySpaces; $i++)
                    <div class="empty-space"></div>
                @endfor
            </div>
            
            <!-- Shelf Board (Bottom) -->
            <div class="shelf-board"></div>
        </div>
        
        <!-- Capacity Stats -->
        <div class="mt-4 flex flex-wrap justify-between text-sm text-white/40">
            <span>📚 {{ $books->count() }} books on shelf</span>
            <span>📦 {{ $emptySpaces }} spaces available</span>
            <span>📊 {{ round(($books->count() / $shelf->capacity) * 100) }}% full</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="mt-2 w-full bg-white/10 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 
                @if($percentage >= 90) bg-red-500
                @elseif($percentage >= 70) bg-purple-500
                @else bg-emerald-500 @endif" 
                style="width: {{ $percentage }}%"></div>
        </div>
    @else
        <div class="text-center py-12 text-white/40">
            <i class="ti ti-books text-5xl block mb-3 text-purple-400/30"></i>
            <p>No books on this shelf yet</p>
        </div>
    @endif
</div>

<!-- ========================================== -->
<!-- BOOKS GRID (List View)                     -->
<!-- ========================================== -->
@if($books->count() > 0)
    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 font-playfair">
        <i class="ti ti-books text-purple-400"></i> 
        All Books on this Shelf ({{ $books->total() }})
    </h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        @foreach($books as $book)
            <a href="{{ route('institution.public.show', [$institution->id, $book->id]) }}" 
               class="book-shelf-card p-3 hover:shadow-lg transition group">
                <div class="aspect-[2/3] bg-purple-900/20 rounded-lg overflow-hidden relative">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
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
                    <p class="text-xs text-purple-400/40 mt-1">
                        <i class="ti ti-map-pin text-[10px]"></i> {{ $book->shelf_number ?? 'No shelf' }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-6">
        {{ $books->appends(request()->query())->links() }}
    </div>
@endif

<!-- ========================================== -->
<!-- STYLES FOR BOOKSHELF                       -->
<!-- ========================================== -->
<style>
.bookshelf-rack {
    background: linear-gradient(180deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid rgba(124, 58, 237, 0.2);
    position: relative;
}

.shelf-board {
    height: 8px;
    background: linear-gradient(180deg, #8B7355, #6B5340);
    border-radius: 4px;
    margin: 8px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

.books-on-shelf {
    display: flex;
    align-items: flex-end;
    gap: 4px;
    min-height: 120px;
    padding: 10px 0;
    flex-wrap: wrap;
}

.book-spine-wrapper {
    display: inline-block;
    text-decoration: none;
    transition: transform 0.2s ease;
    cursor: pointer;
}

.book-spine-wrapper:hover {
    transform: translateY(-5px) scale(1.05);
    z-index: 10;
}

.book-spine {
    border-radius: 3px 3px 0 0;
    box-shadow: 2px 0 5px rgba(0,0,0,0.3);
    position: relative;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,0.05);
}

.book-spine-title {
    color: white;
    font-size: 8px;
    font-weight: bold;
    writing-mode: vertical-rl;
    text-orientation: mixed;
    letter-spacing: 1px;
    text-shadow: 0 0 5px rgba(0,0,0,0.5);
    padding: 2px;
}

.empty-space {
    width: 14px;
    height: 10px;
    background: rgba(255,255,255,0.03);
    border-radius: 2px;
    border: 1px dashed rgba(255,255,255,0.05);
    flex-shrink: 0;
}

/* Different book spine colors for books without covers */
.book-spine:nth-child(6n+1) { background: linear-gradient(135deg, #7c3aed, #4f46e5); }
.book-spine:nth-child(6n+2) { background: linear-gradient(135deg, #dc2626, #b91c1c); }
.book-spine:nth-child(6n+3) { background: linear-gradient(135deg, #059669, #047857); }
.book-spine:nth-child(6n+4) { background: linear-gradient(135deg, #d97706, #b45309); }
.book-spine:nth-child(6n+5) { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
.book-spine:nth-child(6n+6) { background: linear-gradient(135deg, #7c3aed, #4f46e5); }

/* Responsive */
@media (max-width: 768px) {
    .books-on-shelf {
        min-height: 80px;
        gap: 3px;
    }
    .book-spine {
        height: 50px !important;
        width: 12px !important;
    }
    .book-spine-title {
        font-size: 6px;
    }
    .empty-space {
        width: 10px;
        height: 8px;
    }
}
</style>

@endsection