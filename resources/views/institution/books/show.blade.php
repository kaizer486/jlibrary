@extends('layouts.librarian')

@section('title', $book->title)
@section('page-title', '📖 ' . $book->title)

@section('content')

<div class="max-w-4xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('institution.books.index') }}" class="text-slate-400 hover:text-slate-300 transition inline-flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <!-- Book Details Card -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="ti ti-book text-purple-400"></i>
                Book Details
            </h3>
            <span class="text-xs px-3 py-1 rounded-full 
                @if($book->status === 'approved') bg-emerald-500/20 text-emerald-400
                @elseif($book->status === 'pending') bg-yellow-500/20 text-yellow-400
                @else bg-red-500/20 text-red-400 @endif">
                {{ ucfirst($book->status) }}
            </span>
        </div>
        
        <!-- Body -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Left: Cover Image -->
                <div class="md:col-span-1">
                    @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" 
                             alt="{{ $book->title }}" 
                             class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="w-full aspect-[3/4] bg-slate-800 rounded-lg flex items-center justify-center border border-slate-700">
                            <i class="ti ti-book text-6xl text-slate-600"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Right: Details -->
                <div class="md:col-span-2 space-y-4">
                    <!-- Title & Author -->
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $book->title }}</h1>
                        <p class="text-slate-400">by <span class="text-purple-400">{{ $book->author ?? 'Unknown' }}</span></p>
                    </div>
                    
                    <!-- Category -->
                    @if($book->category)
                        <div>
                            <span class="text-xs px-3 py-1 rounded-full bg-purple-500/20 text-purple-400">
                                <i class="ti ti-tag"></i> {{ $book->category }}
                            </span>
                        </div>
                    @endif
                    
                    <!-- Description -->
                    <div>
                        <h4 class="text-sm font-semibold text-slate-400 mb-1">Description</h4>
                        <p class="text-slate-300 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
                    </div>
                    
                    <!-- Location -->
                    <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                        <h4 class="text-sm font-semibold text-slate-400 mb-2 flex items-center gap-2">
                            <i class="ti ti-map-pin text-purple-400"></i> Location
                        </h4>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            @if($book->shelf_number)
                                <div>
                                    <span class="text-slate-500">Shelf Number:</span>
                                    <span class="text-white font-medium">{{ $book->shelf_number }}</span>
                                </div>
                            @endif
                            @if($book->shelf_name)
                                <div>
                                    <span class="text-slate-500">Shelf Name:</span>
                                    <span class="text-white font-medium">{{ $book->shelf_name }}</span>
                                </div>
                            @endif
                            @if($book->floor)
                                <div>
                                    <span class="text-slate-500">Floor:</span>
                                    <span class="text-white font-medium">{{ $book->floor }}</span>
                                </div>
                            @endif
                            @if($book->section)
                                <div>
                                    <span class="text-slate-500">Section:</span>
                                    <span class="text-white font-medium">{{ $book->section }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700">
                            <p class="text-xl font-bold text-white">{{ number_format($book->views_count ?? 0) }}</p>
                            <p class="text-xs text-slate-500">Views</p>
                        </div>
                        <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700">
                            <p class="text-xl font-bold text-white">{{ number_format($book->downloads ?? 0) }}</p>
                            <p class="text-xs text-slate-500">Downloads</p>
                        </div>
                        <div class="bg-slate-800/50 rounded-lg p-3 text-center border border-slate-700">
                            <p class="text-xl font-bold text-white">{{ number_format($book->total_pages ?? 0) }}</p>
                            <p class="text-xs text-slate-500">Pages</p>
                        </div>
                    </div>
                    
                    <!-- Price -->
                    <div class="flex items-center gap-3 pt-2 border-t border-slate-700">
                        @if($book->is_paid)
                            <span class="text-lg font-bold text-emerald-400">TSh {{ number_format($book->price, 2) }}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-amber-500/20 text-amber-400">Paid</span>
                        @else
                            <span class="text-lg font-bold text-emerald-400">🆓 FREE</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-400">Free</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-wrap gap-3 mt-6 pt-4 border-t border-slate-700">
                <a href="{{ route('institution.books.edit', $book) }}" class="btn-library">
                    <i class="ti ti-edit"></i> Edit Book
                </a>
                
                <form method="POST" action="{{ route('institution.books.destroy', $book) }}" 
                      onsubmit="return confirm('Delete this book?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg transition font-medium flex items-center gap-2">
                        <i class="ti ti-trash"></i> Delete Book
                    </button>
                </form>
                
                <a href="{{ route('institution.books.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

</div>

@endsection