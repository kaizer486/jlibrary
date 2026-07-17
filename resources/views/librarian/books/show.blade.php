@extends('layouts.librarian')

@section('title', $book->title)
@section('page-title', '📖 ' . $book->title)

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('librarian.books.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $book->title }}</h1>
                <p class="text-slate-400">by {{ $book->author ?? 'Unknown' }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-full 
                @if($book->status === 'approved') bg-emerald-500/20 text-emerald-400
                @elseif($book->status === 'pending') bg-yellow-500/20 text-yellow-400
                @else bg-red-500/20 text-red-400 @endif">
                {{ ucfirst($book->status) }}
            </span>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cover -->
                <div class="md:col-span-1">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full rounded-lg">
                    @else
                        <div class="w-full aspect-[3/4] bg-slate-800 rounded-lg flex items-center justify-center">
                            <i class="ti ti-book text-6xl text-purple-400"></i>
                        </div>
                    @endif
                </div>

                <!-- Details -->
                <div class="md:col-span-2 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-400">Category</p>
                            <p class="text-white font-medium">{{ $book->category ?? 'Uncategorized' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Price</p>
                            <p class="text-white font-medium">
                                @if($book->is_paid)
                                    TSh {{ number_format($book->price, 2) }}
                                @else
                                    <span class="text-emerald-400">FREE</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">Description</p>
                        <p class="text-slate-300">{{ $book->description ?? 'No description available.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-400">Total Pages</p>
                            <p class="text-white font-medium">{{ $book->total_pages ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Views</p>
                            <p class="text-white font-medium">{{ number_format($book->views_count ?? 0) }}</p>
                        </div>
                    </div>

                    <!-- Shelf Location -->
                    <div class="border-t border-slate-700 pt-4">
                        <p class="text-xs text-slate-400 mb-2">📍 Shelf Location</p>
                        <div class="grid grid-cols-3 gap-2">
                            @if($book->shelf_number)
                                <div>
                                    <p class="text-xs text-slate-500">Shelf</p>
                                    <p class="text-white text-sm font-medium">{{ $book->shelf_number }}</p>
                                </div>
                            @endif
                            @if($book->floor)
                                <div>
                                    <p class="text-xs text-slate-500">Floor</p>
                                    <p class="text-white text-sm font-medium">{{ $book->floor }}</p>
                                </div>
                            @endif
                            @if($book->section)
                                <div>
                                    <p class="text-xs text-slate-500">Section</p>
                                    <p class="text-white text-sm font-medium">{{ $book->section }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 mt-6 pt-4 border-t border-slate-700">
                <a href="{{ route('librarian.books.edit', $book) }}" class="btn-library">
                    <i class="ti ti-edit"></i> Edit Book
                </a>
                <a href="{{ route('librarian.books.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2.5 rounded-lg hover:bg-slate-700 transition">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
                @if($book->file_path)
                   <a href="{{ url('media/' . $book->file_path) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition">
                        <i class="ti ti-file-pdf"></i> View PDF
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection