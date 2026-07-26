@extends('layouts.author')

@section('title', $book->title)
@section('page-title', $book->title)

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('author.books.index') }}" class="text-slate-600 hover:text-slate-800 flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to My Books
            </a>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Left Column - Cover & Stats -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Cover Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 border-slate-200/80 sticky top-6">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 p-4">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <i class="ti ti-photo"></i> Book Cover
                        </h3>
                    </div>
                    <div class="p-6 flex justify-center">
                        @if($book->cover_image)
                           <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full max-w-[250px] rounded-xl shadow-lg">
                        @else
                            <div class="w-full max-w-[250px] h-64 bg-gradient-to-br from-slate-200 to-slate-300 rounded-xl flex items-center justify-center">
                                <i class="ti ti-books text-6xl text-slate-500"></i>
                            </div>
                        @endif
                    </div>
                    <!-- Status Badge -->
                    <div class="px-6 pb-4 text-center">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold inline-block
                            {{ $book->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 
                               ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($book->status) }}
                        </span>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border-2 border-slate-200/80">
                    <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ti ti-chart-bar text-orange-600"></i> Statistics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Downloads</span>
                            <span class="font-semibold">{{ number_format($book->downloads ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Average Rating</span>
                            <span class="font-semibold text-yellow-600">⭐ {{ number_format($book->averageRating() ?? 0, 1) }}/5</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100">
                            <span class="text-slate-500">Total Ratings</span>
                            <span class="font-semibold">{{ $book->ratings()->count() }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-slate-500">Total Reviews</span>
                            <span class="font-semibold">{{ $book->reviews()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Book Info Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-2 border-slate-200/80">
                    <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4">
                        <h3 class="text-white font-semibold flex items-center gap-2">
                            <i class="ti ti-info-circle"></i> Book Information
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Title</p>
                                <p class="mt-1 font-semibold text-slate-800">{{ $book->title }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Author</p>
                                <p class="mt-1 text-slate-700">{{ $book->author }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Price</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    {{ $book->is_paid ? 'TSh '.number_format($book->price, 2) : 'FREE' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Total Pages</p>
                                <p class="mt-1">{{ number_format($book->total_pages ?? 0) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Uploaded</p>
                                <p class="mt-1">{{ $book->created_at->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase tracking-wide">Last Updated</p>
                                <p class="mt-1">{{ $book->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200">
                            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Description</p>
                            <p class="text-slate-700 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid md:grid-cols-3 gap-4">
                    @if($book->file_path)
                        <a href="{{ url('media/' . $book->file_path) }}" target="_blank" 
                           class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                            <i class="ti ti-file-pdf"></i> View PDF
                        </a>
                    @endif
                    @if($book->status === 'pending' || $book->status === 'rejected')
                        <a href="{{ route('author.books.edit', $book) }}" 
                           class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                            <i class="ti ti-edit"></i> Edit Book
                        </a>
                    @endif
                    <form method="POST" action="{{ route('author.books.destroy', $book) }}" 
                          onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                            <i class="ti ti-trash"></i> Delete Book
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-emerald-100 {
        background-color: #d1fae5;
    }
    .text-emerald-700 {
        color: #047857;
    }
    .bg-yellow-100 {
        background-color: #fef3c7;
    }
    .text-yellow-700 {
        color: #b45309;
    }
    .bg-red-100 {
        background-color: #fee2e2;
    }
    .text-red-700 {
        color: #b91c1c;
    }
    
    .sticky {
        position: sticky;
        top: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add any JavaScript for the book show page
        // For example, you could add a "View PDF" modal or confirmation dialogs
    });
</script>
@endpush
@endsection