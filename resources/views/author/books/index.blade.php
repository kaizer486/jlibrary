@extends('layouts.app')

@section('title', 'My Books')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-books"></i> My Books
                </h1>
                <p class="text-gray-300 text-sm mt-1">Manage your published books</p>
            </div>
            <a href="{{ route('author.books.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <i class="ti ti-plus"></i> Upload New Book
            </a>
        </div>

        <!-- Books Grid -->
        @if($books->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($books as $book)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                <!-- Cover Image -->
                <div class="relative h-48 bg-gray-200">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="ti ti-book text-6xl text-gray-400"></i>
                        </div>
                    @endif
                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        @if($book->status === 'approved')
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-green-500 text-white">Approved</span>
                        @elseif($book->status === 'pending')
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-yellow-500 text-white">Pending</span>
                        @else
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-red-500 text-white">Rejected</span>
                        @endif
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-4">
                    <h3 class="font-bold text-lg text-gray-800 mb-1 line-clamp-1">{{ $book->title }}</h3>
                    <p class="text-sm text-gray-500 mb-2">by {{ $book->author }}</p>
                    <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ Str::limit($book->description ?? 'No description', 80) }}</p>
                    
                    <!-- Stats -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 text-gray-500 text-xs">
                                <i class="ti ti-download"></i>
                                <span>{{ number_format($book->downloads ?? 0) }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-gray-500 text-xs">
                                <i class="ti ti-star text-yellow-400"></i>
                                <span>{{ $book->averageRating() ?? 0 }}</span>
                            </div>
                            <div class="flex items-center gap-1 text-gray-500 text-xs">
                                <i class="ti ti-wallet"></i>
                                <span>{{ $book->is_paid ? 'TSh '.number_format($book->price, 2) : 'FREE' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('author.books.show', $book) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('author.books.edit', $book) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('author.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
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
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Yet</h3>
            <p class="text-gray-500 mb-4">Upload your first book to start earning royalties.</p>
            <a href="{{ route('author.books.create') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="ti ti-plus"></i> Upload Your First Book
            </a>
        </div>
        @endif

    </div>
</div>
@endsection