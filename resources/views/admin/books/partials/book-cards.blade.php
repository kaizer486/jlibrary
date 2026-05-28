@foreach($books as $book)
<div class="book-card bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group relative" 
     data-id="{{ $book->id }}">
    
    <!-- Checkbox for bulk actions -->
    <div class="absolute top-3 left-3 z-20">
        <input type="checkbox" class="book-checkbox w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500" value="{{ $book->id }}">
    </div>
    
    <!-- Cover Image Section -->
    <div class="relative h-48 bg-gradient-to-r from-gray-800 to-gray-900 overflow-hidden">
        @if($book->cover_image)
            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="ti ti-books text-6xl text-gray-600"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        
        <!-- Status Badge -->
        <div class="absolute top-3 right-3 z-10">
            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $book->status === 'approved' ? 'bg-green-500 text-white' : ($book->status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') }}">
                {{ ucfirst($book->status) }}
            </span>
        </div>
        
        <!-- Price Badge -->
        <div class="absolute bottom-3 left-3 z-10">
            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $book->is_paid ? 'bg-blue-500 text-white' : 'bg-emerald-500 text-white' }}">
                {{ $book->is_paid ? '$'.number_format($book->price, 2) : 'FREE' }}
            </span>
        </div>
    </div>
    
    <!-- Content Section -->
    <div class="p-4">
        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1">{{ $book->title }}</h3>
        <p class="text-sm text-gray-500 mb-2">
            <i class="ti ti-user"></i> {{ $book->author }}
        </p>
        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ Str::limit($book->description ?? 'No description available.', 80) }}</p>
        
        <!-- Stats Row -->
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
                    <i class="ti ti-book"></i>
                    <span>{{ $book->total_pages ?? 0 }} pgs</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.books.show', $book) }}" class="text-blue-600 hover:text-blue-700 transition p-1" title="View">
                    <i class="ti ti-eye text-base"></i>
                </a>
                <a href="{{ route('admin.books.edit', $book) }}" class="text-purple-600 hover:text-purple-700 transition p-1" title="Edit">
                    <i class="ti ti-edit text-base"></i>
                </a>
                <button onclick="deleteBook({{ $book->id }}, '{{ addslashes($book->title) }}')" class="text-red-600 hover:text-red-700 transition p-1" title="Delete">
                    <i class="ti ti-trash text-base"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

@if($books->count() == 0)
<div class="col-span-full text-center py-12">
    <i class="ti ti-books text-6xl text-gray-300 mb-3 block"></i>
    <p class="text-gray-500">No books found</p>
</div>
@endif