@extends('layouts.library')

@section('title', $book->title)

@section('content')

<!-- Back Button -->
<div class="mb-4">
    <a href="{{ route('institution.public.index', $institution->id) }}" 
       class="text-purple-300/70 hover:text-purple-200 transition inline-flex items-center gap-1 text-sm">
        <i class="ti ti-arrow-left"></i> Back to Library
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    
    <!-- Cover -->
    <div class="md:col-span-1">
        <div class="library-card overflow-hidden">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-auto">
            @else
                <div class="w-full aspect-[2/3] bg-purple-900/30 flex items-center justify-center">
                    <i class="ti ti-book text-8xl text-purple-400/30"></i>
                </div>
            @endif
        </div>
        
        <!-- Price & Action -->
        <div class="library-card p-4 mt-4">
            @if($book->is_paid)
                <p class="text-2xl font-bold text-green-400">TSh {{ number_format($book->price, 2) }}</p>
                <a href="{{ route('book.purchase', $book->id) }}" class="btn-library w-full justify-center mt-3" style="background: linear-gradient(135deg, #059669, #047857);">
                    <i class="ti ti-shopping-cart"></i> Purchase Book
                </a>
            @else
                <p class="text-2xl font-bold text-green-400">🆓 FREE</p>
                @if($book->file_path)
                    <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank" class="btn-library w-full justify-center mt-3">
                        <i class="ti ti-file-pdf"></i> Read / Download
                    </a>
                @else
                    <p class="text-sm text-white/40 mt-2">No PDF available for preview</p>
                @endif
            @endif

            <!-- ========================================== -->
            <!-- BORROW BUTTON                              -->
            <!-- ========================================== -->
            @auth
                @if($book->isAvailableToBorrow() && auth()->user()->institution_id == $book->institution_id)
                    <a href="{{ route('librarian.borrowings.create', ['book_id' => $book->id]) }}" 
                       class="w-full mt-2 inline-block text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                        <i class="ti ti-bookmark"></i> Borrow Book
                    </a>
                @elseif($book->isBorrowed())
                    <p class="w-full mt-2 text-center text-amber-400 py-2 rounded-lg border border-amber-500/20 text-sm">
                        <i class="ti ti-clock"></i> Currently borrowed
                    </p>
                @endif
            @endauth
        </div>
        
        <!-- Quick Stats -->
        <div class="library-card p-4 mt-4">
            <div class="grid grid-cols-3 gap-2 text-center">
                <div>
                    <p class="text-lg font-bold text-purple-300">{{ number_format($book->views_count ?? 0) }}</p>
                    <p class="text-xs text-white/40">Views</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-purple-300">{{ number_format($book->downloads ?? 0) }}</p>
                    <p class="text-xs text-white/40">Downloads</p>
                </div>
                <div>
                    <p class="text-lg font-bold text-purple-300">{{ $book->total_pages ?? 0 }}</p>
                    <p class="text-xs text-white/40">Pages</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Details -->
    <div class="md:col-span-2">
        <div class="library-card p-6">
            <h1 class="text-3xl font-bold book-title-white">{{ $book->title }}</h1>
            <p class="text-lg book-text-muted mt-1">by <span class="font-semibold text-purple-300">{{ $book->author ?? 'Unknown' }}</span></p>
            
            <!-- Badges -->
            <div class="flex flex-wrap gap-2 mt-3">
                @if($book->category)
                    <span class="location-tag">
                        <i class="ti ti-tag"></i> {{ $book->category }}
                    </span>
                @endif
                <span class="location-tag bg-emerald-500/10 text-emerald-400 border-emerald-500/20">
                    <i class="ti ti-check"></i> {{ ucfirst($book->status) }}
                </span>
                @if($book->is_paid)
                    <span class="badge-paid">💰 Paid</span>
                @else
                    <span class="badge-free">🆓 Free</span>
                @endif
                @if($book->isBorrowed())
                    <span class="location-tag bg-blue-500/10 text-blue-400 border-blue-500/20">
                        <i class="ti ti-bookmark"></i> Borrowed
                    </span>
                @endif
            </div>
            
            <!-- Shelf Location -->
            @if($book->shelf_number || $book->shelf_name)
                <div class="mt-4 p-4 bg-purple-900/20 rounded-xl border border-purple-500/20">
                    <p class="text-sm font-semibold text-purple-300 flex items-center gap-2">
                        <i class="ti ti-map-pin"></i> Location in Library
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                        @if($book->shelf_number)
                            <div>
                                <p class="text-xs text-white/40">Shelf Number</p>
                                <p class="font-semibold text-white">{{ $book->shelf_number }}</p>
                            </div>
                        @endif
                        @if($book->shelf_name)
                            <div>
                                <p class="text-xs text-white/40">Shelf Name</p>
                                <p class="font-semibold text-white">{{ $book->shelf_name }}</p>
                            </div>
                        @endif
                        @if($book->column_location)
                            <div>
                                <p class="text-xs text-white/40">Column</p>
                                <p class="font-semibold text-white">{{ $book->column_location }}</p>
                            </div>
                        @endif
                        @if($book->position)
                            <div>
                                <p class="text-xs text-white/40">Position</p>
                                <p class="font-semibold text-white">{{ $book->position }}</p>
                            </div>
                        @endif
                        @if($book->floor)
                            <div>
                                <p class="text-xs text-white/40">Floor</p>
                                <p class="font-semibold text-white">{{ $book->floor }}</p>
                            </div>
                        @endif
                        @if($book->section)
                            <div>
                                <p class="text-xs text-white/40">Section</p>
                                <p class="font-semibold text-white">{{ $book->section }}</p>
                            </div>
                        @endif
                    </div>
                    <p class="text-sm text-white/40 mt-2">
                        <i class="ti ti-location"></i> Full Location: 
                        @php
                            $parts = [];
                            if($book->shelf_number) $parts[] = "Shelf: {$book->shelf_number}";
                            if($book->floor) $parts[] = "Floor: {$book->floor}";
                            if($book->section) $parts[] = "Section: {$book->section}";
                            if($book->column_location) $parts[] = "Column: {$book->column_location}";
                            echo implode(' | ', $parts) ?: 'Location not specified';
                        @endphp
                    </p>
                </div>
            @endif
            
            <!-- Description -->
            <div class="mt-4">
                <h3 class="font-semibold text-white">Description</h3>
                <p class="text-white/60 mt-1 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
            </div>
            
            <!-- Book Details -->
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 p-4 bg-white/5 rounded-xl">
                <div>
                    <p class="text-xs text-white/40">ISBN</p>
                    <p class="font-medium text-white text-sm">{{ $book->isbn ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-white/40">Publisher</p>
                    <p class="font-medium text-white text-sm">{{ $book->publisher ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-white/40">Year</p>
                    <p class="font-medium text-white text-sm">{{ $book->publication_year ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-white/40">Language</p>
                    <p class="font-medium text-white text-sm">{{ $book->language ?? 'English' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Books -->
@if($relatedBooks->count() > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2 font-playfair">
            <i class="ti ti-books text-purple-400"></i> Related Books
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach($relatedBooks as $related)
                <a href="{{ route('institution.public.show', [$institution->id, $related->id]) }}" 
                   class="library-card p-3 hover:shadow-lg transition group block">
                    <div class="aspect-[2/3] bg-purple-900/20 rounded-lg overflow-hidden">
                        @if($related->cover_image)
                            <img src="{{ asset('storage/' . $related->cover_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full bg-purple-900/20 flex items-center justify-center">
                                <i class="ti ti-book text-3xl text-purple-400/30"></i>
                            </div>
                        @endif
                    </div>
                    <div class="mt-2">
                        <p class="book-title text-sm truncate group-hover:text-purple-300">{{ $related->title }}</p>
                        <p class="book-author truncate">{{ $related->author ?? 'Unknown' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

<!-- Library Location -->
<div class="library-card p-4 mt-8">
    <h3 class="font-semibold text-white flex items-center gap-2">
        <i class="ti ti-map-pin text-purple-400"></i> Library Location
    </h3>
    <p class="text-white/60 text-sm mt-1">
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

@endsection