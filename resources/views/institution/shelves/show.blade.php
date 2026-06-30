@extends('layouts.librarian')

@section('title', $shelf->name)
@section('page-title', '🗄️ ' . $shelf->name)

@section('content')

<div class="max-w-5xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('institution.shelves.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Shelves
        </a>
    </div>

    <!-- ========================================== -->
    <!-- SHELF HEADER                               -->
    <!-- ========================================== -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $shelf->name }}</h1>
                    <p class="text-slate-400 text-sm">Code: <span class="font-mono text-purple-400">{{ $shelf->code }}</span></p>
                </div>
                <div>
                    {!! $shelf->status_badge !!}
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-slate-800 rounded-xl border border-slate-700">
                    <p class="text-2xl font-bold text-white">{{ $books->count() }}</p>
                    <p class="text-xs text-slate-400">Books</p>
                </div>
                <div class="text-center p-3 bg-slate-800 rounded-xl border border-slate-700">
                    <p class="text-2xl font-bold text-white">{{ $shelf->current_count }}/{{ $shelf->capacity }}</p>
                    <p class="text-xs text-slate-400">Capacity</p>
                </div>
                <div class="text-center p-3 bg-slate-800 rounded-xl border border-slate-700">
                    <p class="text-2xl font-bold text-white">{{ $shelf->getAvailableSlots() }}</p>
                    <p class="text-xs text-slate-400">Available Slots</p>
                </div>
                <div class="text-center p-3 bg-slate-800 rounded-xl border border-slate-700">
                    <p class="text-2xl font-bold text-white">{{ $shelf->category ?? 'N/A' }}</p>
                    <p class="text-xs text-slate-400">Category</p>
                </div>
            </div>

            <!-- Capacity Progress -->
            <div class="mt-4">
                <div class="flex justify-between text-sm text-slate-300 mb-1">
                    <span>Capacity Usage</span>
                    <span>{{ $shelf->current_count }} / {{ $shelf->capacity }} ({{ $percentage }}%)</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-3">
                    <div class="h-3 rounded-full transition-all duration-500 
                        @if($percentage >= 90) bg-red-500
                        @elseif($percentage >= 70) bg-purple-500
                        @else bg-emerald-500 @endif" 
                        style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            
            @if($shelf->description)
                <div class="mt-4 p-4 bg-slate-800 rounded-xl border border-slate-700">
                    <h3 class="font-semibold text-white">Description</h3>
                    <p class="text-slate-300 mt-1">{{ $shelf->description }}</p>
                </div>
            @endif
            
            <!-- Location -->
            @if($shelf->getFullLocationAttribute() && $shelf->getFullLocationAttribute() !== ' |  |  | ')
                <div class="mt-4 p-4 bg-slate-800 rounded-xl border border-purple-500/20">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <i class="ti ti-map-pin text-purple-400"></i> Location
                    </h3>
                    <p class="text-slate-300 mt-1">{{ $shelf->getFullLocationAttribute() }}</p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @if($shelf->floor)
                            <span class="text-xs bg-slate-700 border border-slate-600 rounded-full px-3 py-1 text-slate-300">🏢 Floor: {{ $shelf->floor }}</span>
                        @endif
                        @if($shelf->section)
                            <span class="text-xs bg-slate-700 border border-slate-600 rounded-full px-3 py-1 text-slate-300">📂 Section: {{ $shelf->section }}</span>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Actions -->
            <div class="mt-6 flex gap-3">
                <a href="{{ route('institution.shelves.edit', $shelf) }}" class="btn-library">
                    <i class="ti ti-edit"></i> Edit Shelf
                </a>
                <form method="POST" action="{{ route('institution.shelves.destroy', $shelf) }}" 
                      onsubmit="return confirm('Delete this shelf?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg transition">
                        <i class="ti ti-trash"></i> Delete Shelf
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- VISUAL BOOKSHELF WITH BOOK SPINES          -->
    <!-- ========================================== -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <i class="ti ti-books text-purple-400"></i>
                    Shelf View ({{ $books->count() }} / {{ $shelf->capacity }} books)
                </h3>
                <a href="{{ route('institution.books.create') }}" class="text-sm text-purple-400 hover:text-purple-300 transition">
                    <i class="ti ti-plus"></i> Add Book
                </a>
            </div>
        </div>
        
        <div class="p-6">
            @if($books->count() > 0)
                <div class="bookshelf-rack">
                    <!-- Shelf Board (Top) -->
                    <div class="shelf-board"></div>
                    
                    <!-- Books on Shelf -->
                    <div class="books-on-shelf">
                        @foreach($books as $book)
                            <a href="{{ route('institution.books.show', $book) }}" 
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
                <div class="mt-4 flex flex-wrap justify-between text-sm text-slate-400">
                    <span>📚 {{ $books->count() }} books on shelf</span>
                    <span>📦 {{ $emptySpaces }} spaces available</span>
                    <span>📊 {{ round(($books->count() / $shelf->capacity) * 100) }}% full</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-2 w-full bg-slate-800 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500 
                        @if($percentage >= 90) bg-red-500
                        @elseif($percentage >= 70) bg-yellow-500
                        @else bg-emerald-500 @endif" 
                        style="width: {{ $percentage }}%"></div>
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <i class="ti ti-books text-5xl block mb-3 text-slate-600"></i>
                    <p>No books on this shelf yet</p>
                    <a href="{{ route('institution.books.create') }}" class="text-purple-400 hover:underline block mt-2">
                        <i class="ti ti-plus"></i> Add first book
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- ========================================== -->
<!-- STYLES FOR BOOKSHELF                       -->
<!-- ========================================== -->
<style>
.bookshelf-rack {
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #334155;
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