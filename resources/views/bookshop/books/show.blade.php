@extends('layouts.bookshop')

@section('title', $book->title)

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('bookshop.books.index') }}" class="text-orange-600 hover:text-orange-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">{{ $book->title }}</h1>
            <p class="text-orange-100 text-sm">by {{ $book->author ?? 'Unknown Author' }}</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Cover Image -->
                <div class="md:col-span-1">
                    <div class="bg-gray-100 rounded-xl p-4 flex items-center justify-center h-64">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="h-full object-contain">
                        @else
                            <i class="ti ti-book text-8xl text-gray-300"></i>
                        @endif
                    </div>
                </div>

                <!-- Details -->
                <div class="md:col-span-2 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-400">Price</p>
                            <p class="text-2xl font-bold text-orange-600">TSh {{ number_format($book->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Stock</p>
                            <p class="text-2xl font-bold {{ $book->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $book->stock_quantity }}
                                @if($book->stock_quantity <= 5 && $book->stock_quantity > 0)
                                    <span class="text-sm text-yellow-600 font-normal">(Low Stock)</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        {!! $book->status_badge !!}
                    </div>

                    @if($book->description)
                    <div>
                        <p class="text-xs text-gray-400">Description</p>
                        <p class="text-gray-600">{{ $book->description }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        @if($book->category)
                        <div>
                            <p class="text-xs text-gray-400">Category</p>
                            <p class="text-gray-600">{{ ucfirst($book->category) }}</p>
                        </div>
                        @endif
                        @if($book->isbn)
                        <div>
                            <p class="text-xs text-gray-400">ISBN</p>
                            <p class="text-gray-600">{{ $book->isbn }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @if($book->pages)
                        <div>
                            <p class="text-xs text-gray-400">Pages</p>
                            <p class="text-gray-600">{{ $book->pages }}</p>
                        </div>
                        @endif
                        @if($book->publication_year)
                        <div>
                            <p class="text-xs text-gray-400">Published</p>
                            <p class="text-gray-600">{{ $book->publication_year }}</p>
                        </div>
                        @endif
                    </div>

                    @if($book->publisher)
                    <div>
                        <p class="text-xs text-gray-400">Publisher</p>
                        <p class="text-gray-600">{{ $book->publisher }}</p>
                    </div>
                    @endif

                    <div class="flex gap-3 pt-4 border-t">
                        <a href="{{ route('bookshop.books.edit', $book) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="ti ti-edit"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('bookshop.books.destroy', $book) }}" 
                              onsubmit="return confirm('Delete this book?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="ti ti-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection