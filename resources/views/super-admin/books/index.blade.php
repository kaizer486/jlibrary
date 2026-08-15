@extends('layouts.super-admin')

@section('title', 'Manage Books')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Manage Books</h1>
            <p class="text-gray-500 text-sm mt-1">Manage all books in the library collection</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('super-admin.books.create') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition flex items-center gap-2">
                <i class="ti ti-plus"></i> Add New Book
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <!-- Total Books -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Books</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalBooks ?? 0 }}</p>
        </div>
        
        <!-- Approved -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Approved</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $approvedBooks ?? 0 }}</p>
        </div>
        
        <!-- Paid -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">💰 Paid</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $paidBooks ?? 0 }}</p>
        </div>
        
        <!-- Free -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">🆓 Free</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $freeBooks ?? 0 }}</p>
        </div>
        
        <!-- Featured -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-600">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">⭐ Featured</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $featuredBooks ?? 0 }}</p>
        </div>
        
        <!-- Trending -->
        <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">🔥 Trending</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $trendingBooks ?? 0 }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search books..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48 focus:ring-2 focus:ring-purple-500">
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500">
                    <option value="all">All Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Price Type</label>
                <select name="price_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500">
                    <option value="all">All Books</option>
                    <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-purple-500">
                    <option value="all">All Categories</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm flex items-center gap-1">
                <i class="ti ti-search"></i> Filter
            </button>
            
            <a href="{{ route('super-admin.books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm flex items-center gap-1">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Author</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($book->cover_image)
                                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded">
                                    @else
                                        <div class="w-10 h-14 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="ti ti-book text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $book->title }}</p>
                                        <p class="text-xs text-gray-500">ID: #{{ $book->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $book->author ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $book->category ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($book->is_paid)
                                    <span class="text-amber-600 font-semibold">TSh {{ number_format($book->price, 2) }}</span>
                                @else
                                    <span class="text-green-600 font-semibold">FREE</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $book->status == 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $book->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $book->status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                                @if($book->is_featured)
                                    <span class="ml-1 text-yellow-500">⭐</span>
                                @endif
                                @if($book->is_trending)
                                    <span class="ml-1 text-orange-500">🔥</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('super-admin.books.show', $book) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a href="{{ route('super-admin.books.edit', $book) }}" class="text-purple-600 hover:text-purple-800" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('super-admin.books.toggle-status', $book) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-{{ $book->status == 'approved' ? 'red' : 'green' }}-600 hover:text-{{ $book->status == 'approved' ? 'red' : 'green' }}-800" title="Toggle Status">
                                            <i class="ti ti-{{ $book->status == 'approved' ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('super-admin.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete this book permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="ti ti-books text-4xl block mb-2 text-gray-300"></i>
                                No books found. <a href="{{ route('super-admin.books.create') }}" class="text-purple-600 hover:underline">Add your first book</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection