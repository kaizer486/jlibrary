@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <i class="ti ti-bookmark text-3xl text-purple-600"></i>
                <h1 class="text-3xl font-bold text-gray-800">My Bookmarks</h1>
            </div>
            <p class="text-gray-500">Books you've saved for later reading</p>
        </div>
        
        @if($bookmarks->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bookmarks as $bookmark)
                    @php $book = $bookmark->bookmarkable; @endphp
                    @if($book)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition group relative">
                            <!-- Book Cover -->
                            <div class="relative h-40 bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                @if($book->cover_image)
                                    <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                @else
                                    <i class="ti ti-book text-5xl text-white/50"></i>
                                @endif
                                
                                <!-- Remove Bookmark Button -->
                                <div class="absolute top-2 right-2">
                                    <form action="{{ route('bookmark.destroy', $bookmark->id) }}" method="POST" onsubmit="return confirm('Remove from bookmarks?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center hover:bg-red-600 transition text-white">
                                            <i class="ti ti-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Book Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 mb-1 line-clamp-1">{{ $book->title }}</h3>
                                <p class="text-gray-500 text-sm mb-2">{{ $book->author ?? 'Unknown Author' }}</p>
                                
                                <!-- Price Badge -->
                                <div class="mb-3">
                                    @if(isset($book->is_paid) && $book->is_paid)
                                        <span class="inline-block bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded-lg">
                                           TSh {{ number_format($book->price ?? 0, 0) }}
                                        </span>
                                    @else
                                        <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded-lg">
                                            Free
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="ti ti-calendar mr-1"></i>
                                        <span>Saved {{ $bookmark->created_at->diffForHumans() }}</span>
                                    </div>
                                    <a href="{{ route('library.show', $book) }}" 
                                       class="bg-purple-600 text-white px-3 py-1 rounded-lg hover:bg-purple-700 transition text-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Orphaned Bookmark -->
                        <div class="bg-white rounded-xl shadow-md overflow-hidden border-2 border-red-200">
                            <div class="relative h-40 bg-gray-200 flex items-center justify-center">
                                <i class="ti ti-book-off text-4xl text-gray-400"></i>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-red-600">Book Not Found</h3>
                                <p class="text-sm text-gray-500">This book may have been deleted.</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <form action="{{ route('bookmark.destroy', $bookmark->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Remove this bookmark?')">
                                            Remove Bookmark
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $bookmarks->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-bookmark text-purple-600 text-4xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No bookmarks yet</h3>
                <p class="text-gray-500 mb-6">Save books you want to read later</p>
                <a href="{{ route('library.index') }}" class="inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-2 rounded-xl hover:bg-purple-700 transition">
                    Browse Library
                    <i class="ti ti-arrow-right"></i>
                </a>
            </div>
        @endif
        
    </div>
</div>
@endsection