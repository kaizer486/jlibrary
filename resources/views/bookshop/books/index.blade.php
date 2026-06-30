@extends('layouts.bookshop')

@section('title', 'Bookshop Books')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📚 Bookshop Books</h1>
                <p class="text-gray-500 text-sm mt-1">Manage your bookshop inventory</p>
            </div>
            <a href="{{ route('bookshop.books.create') }}" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                <i class="ti ti-plus"></i> Add New Book
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Total Books</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($stats['active'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Out of Stock</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['out_of_stock'] ?? 0) }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
            <p class="text-gray-500 text-sm">Low Stock</p>
            <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats['low_stock'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search books by title or author..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div>
                <select name="stock" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Low Stock (≤ 5)</option>
                    <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                </select>
            </div>
            <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('bookshop.books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Books Grid -->
    @if($books->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($books as $book)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition group">
            <!-- Cover Image -->
            <div class="h-48 bg-gray-100 flex items-center justify-center relative">
                @if($book->cover_image)
                    <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                @else
                    <i class="ti ti-book text-6xl text-gray-300"></i>
                @endif
                @if($book->stock_quantity <= 0)
                    <span class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">Out of Stock</span>
                @elseif($book->stock_quantity <= 5)
                    <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">Low Stock</span>
                @endif
            </div>
            
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 truncate">{{ $book->title }}</h3>
                <p class="text-sm text-gray-500 truncate">{{ $book->author ?? 'Unknown Author' }}</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="text-lg font-bold text-orange-600">TSh {{ number_format($book->price, 2) }}</span>
                    <span class="text-sm text-gray-500">{{ $book->stock_quantity }} in stock</span>
                </div>
                <div class="flex gap-2 mt-3">
                    <a href="{{ route('bookshop.books.edit', $book) }}" class="flex-1 text-center bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-sm transition">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <a href="{{ route('bookshop.books.show', $book) }}" class="flex-1 text-center bg-gray-50 hover:bg-gray-100 text-gray-600 px-3 py-1.5 rounded-lg text-sm transition">
                        <i class="ti ti-eye"></i> View
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
        <p class="text-gray-500">Add your first book to start selling!</p>
        <a href="{{ route('bookshop.books.create') }}" class="mt-4 inline-block bg-orange-600 text-white px-6 py-2 rounded-lg hover:bg-orange-700 transition">
            <i class="ti ti-plus"></i> Add Book
        </a>
    </div>
    @endif
</div>
@endsection