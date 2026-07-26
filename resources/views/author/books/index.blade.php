@extends('layouts.author')

@section('title', 'My Books')
@section('page-title', 'My Books')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- ========================================== -->
        <!-- HEADER CARD                                 -->
        <!-- ========================================== -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                        <i class="ti ti-books text-white text-xl"></i>
                    </span>
                    My Books
                </h1>
                <p class="text-slate-600 text-sm mt-1">Manage your published books</p>
            </div>
            <a href="{{ route('author.books.create') }}" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border-2 border-orange-400/30">
                <i class="ti ti-plus"></i> Upload New Book
            </a>
        </div>

        <!-- ========================================== -->
        <!-- BOOKS GRID                                 -->
        <!-- ========================================== -->
        @if($books->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
            <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden hover:shadow-lg hover:border-orange-300/60 transition group">
                <!-- Cover Image -->
                <div class="relative h-48 bg-orange-50/60">
                    @if($book->cover_image)
                       <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ti ti-book text-6xl text-orange-400/40"></i>
                        </div>
                    @endif
                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        @if($book->status === 'approved')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-500 text-white border border-emerald-400/30">✅ Approved</span>
                        @elseif($book->status === 'pending')
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-yellow-500 text-white border border-yellow-400/30">⏳ Pending</span>
                        @else
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white border border-red-400/30">❌ Rejected</span>
                        @endif
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-bold text-lg text-slate-800 mb-1 line-clamp-1">{{ $book->title }}</h3>
                    <p class="text-sm text-slate-500 mb-2">by {{ $book->author }}</p>
                    <p class="text-sm text-slate-600 line-clamp-2 mb-3">{{ Str::limit($book->description ?? 'No description', 80) }}</p>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-between pt-3 border-t-2 border-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 text-slate-500 text-xs">
                                <i class="ti ti-download text-orange-500"></i>
                                <span>{{ number_format($book->downloads ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-slate-500 text-xs">
                                <i class="ti ti-star text-yellow-400"></i>
                                <span>{{ $book->averageRating() ?? 0 }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-slate-500 text-xs">
                                <i class="ti ti-wallet text-orange-500"></i>
                                <span>{{ $book->is_paid ? 'TSh '.number_format($book->price, 2) : 'FREE' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('author.books.show', $book) }}" class="text-blue-600 hover:text-blue-800 transition" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('author.books.edit', $book) }}" class="text-orange-600 hover:text-orange-800 transition" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('author.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $books->links() }}
        </div>

        @else
        <!-- ========================================== -->
        <!-- EMPTY STATE CARD                           -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 p-16 text-center">
            <div class="w-24 h-24 bg-gradient-to-br from-orange-500 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/20">
                <i class="ti ti-books text-4xl text-white"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800 mb-2">No Books Yet</h3>
            <p class="text-slate-500 mb-4">Upload your first book to start earning royalties.</p>
            <a href="{{ route('author.books.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-medium border-2 border-orange-400/30">
                <i class="ti ti-plus"></i> Upload Your First Book
            </a>
        </div>
        @endif

    </div>
</div>

@push('styles')
<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .bg-emerald-500 {
        background-color: #10b981;
    }
    .border-emerald-400\/30 {
        border-color: rgba(52, 211, 153, 0.3);
    }
    .bg-yellow-500 {
        background-color: #f59e0b;
    }
    .border-yellow-400\/30 {
        border-color: rgba(251, 191, 36, 0.3);
    }
    .bg-red-500 {
        background-color: #ef4444;
    }
    .border-red-400\/30 {
        border-color: rgba(248, 113, 113, 0.3);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add any JavaScript for the books index page
        // For example, you could add a search/filter functionality
    });
</script>
@endpush
@endsection