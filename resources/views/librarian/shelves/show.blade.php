@extends('layouts.librarian')

@section('title', $shelf->name)
@section('page-title', '🗄️ ' . $shelf->name)

@section('content')

<div class="max-w-5xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('librarian.shelves.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Shelves
        </a>
    </div>

    <!-- Shelf Header - Dark Glassmorphism -->
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-slate-800 rounded-xl border border-slate-700">
                    <p class="text-2xl font-bold text-white">{{ $shelf->books()->count() }}</p>
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

            <!-- Capacity Progress - Purple Gradient -->
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
                        @if($shelf->column)
                            <span class="text-xs bg-slate-700 border border-slate-600 rounded-full px-3 py-1 text-slate-300">📐 Column: {{ $shelf->column }}</span>
                        @endif
                        @if($shelf->row)
                            <span class="text-xs bg-slate-700 border border-slate-600 rounded-full px-3 py-1 text-slate-300">📏 Row: {{ $shelf->row }}</span>
                        @endif
                    </div>
                </div>
            @endif
            
            <div class="mt-6 flex gap-3">
                <a href="{{ route('librarian.shelves.edit', $shelf) }}" class="btn-library">
                    <i class="ti ti-edit"></i> Edit Shelf
                </a>
                <form method="POST" action="{{ route('librarian.shelves.destroy', $shelf) }}" 
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

    <!-- Books on this Shelf -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-700 bg-slate-800/50">
            <h3 class="font-semibold text-white flex items-center gap-2">
                <i class="ti ti-books text-purple-400"></i>
                Books on this Shelf ({{ $books->count() }})
            </h3>
        </div>
        
        @if($books->count() > 0)
            <div class="divide-y divide-slate-800">
                @foreach($books as $book)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-slate-800/50 transition">
                        <div class="flex items-center gap-3">
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded">
                            @else
                                <div class="w-10 h-14 bg-slate-800 rounded flex items-center justify-center">
                                    <i class="ti ti-book text-purple-400"></i>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-white">{{ $book->title }}</p>
                                <p class="text-xs text-slate-400">{{ $book->author ?? 'Unknown' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                @if($book->status === 'approved') badge-approved
                                @elseif($book->status === 'pending') badge-pending
                                @else badge-rejected @endif">
                                {{ ucfirst($book->status) }}
                            </span>
                            <a href="{{ route('librarian.books.show', $book) }}" class="text-purple-400 hover:text-purple-300 text-sm">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('librarian.books.edit', $book) }}" class="text-blue-400 hover:text-blue-300 text-sm">
                                <i class="ti ti-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-slate-400">
                <i class="ti ti-books text-4xl mb-2 block text-slate-600"></i>
                No books on this shelf yet.
                <a href="{{ route('librarian.books.create') }}" class="text-purple-400 hover:underline block mt-2">
                    Add a book to this shelf →
                </a>
            </div>
        @endif
    </div>

</div>

@endsection