@extends('layouts.admin')

@section('title', 'Manage Books')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="ti ti-books text-indigo-600"></i>
                Manage Books
            </h1>
            <p class="text-gray-500 text-sm mt-1">Manage all books across the platform</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition flex items-center gap-2 font-semibold">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-indigo-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Books</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalBooks) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ number_format($approvedBooks) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($pendingBooks) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Free Books</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($freeBooks) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm"> Featured</p>
        <p class="text-2xl font-bold text-yellow-600">{{ number_format($featuredBooks) }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-orange-500 shadow-sm">
        <p class="text-gray-500 text-sm"> Trending</p>
        <p class="text-2xl font-bold text-orange-600">{{ number_format($trendingBooks) }}</p>
    </div>
</div>

<!-- Search and Filter Bar -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.books.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="ti ti-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" placeholder="Search by title or author..." value="{{ request('search') }}" 
                   class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <select name="category" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 min-w-[180px]">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                        {{ $category }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="institution_id" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">All Institutions</option>
                @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="price_type" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">All Books</option>
                <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>🆓 Free Only</option>
                <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>💰 Paid Only</option>
            </select>
        </div>
        <div>
            <select name="featured" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">All</option>
                <option value="yes" {{ request('featured') == 'yes' ? 'selected' : '' }}>⭐ Featured</option>
                <option value="no" {{ request('featured') == 'no' ? 'selected' : '' }}>Not Featured</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
        <div>
            <a href="{{ route('admin.books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block">Clear</a>
        </div>
    </form>
</div>

<!-- Books Table -->
@if($books->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Institution</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flags</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Downloads</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($books as $book)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($book->cover_image)
                                <img src="{{ url('media/' . $book->cover_image) }}" class="w-8 h-10 rounded object-cover">
                            @else
                                <div class="w-8 h-10 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="ti ti-book text-gray-400"></i>
                                </div>
                            @endif
                            <span class="font-medium text-gray-800">{{ Str::limit($book->title, 35) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($book->author, 20) }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="text-xs">{{ $book->category_label ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($book->institution_id && $book->institution)
                            {{ $book->institution->name }}
                        @else
                            <span class="text-gray-400 text-xs">Global</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($book->is_paid)
                            <span class="font-semibold text-blue-600">TSh {{ number_format($book->price, 2) }}</span>
                        @else
                            <span class="text-green-600 font-semibold">FREE</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.books.toggle-status', $book) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="px-2 py-1 rounded-full text-xs font-semibold cursor-pointer
                                {{ $book->status === 'approved' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 
                                   ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-red-100 text-red-700 hover:bg-red-200') }}">
                                {{ ucfirst($book->status) }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            @if($book->is_featured)
                                <span class="text-yellow-500" title="Featured"></span>
                            @endif
                            @if($book->is_trending)
                                <span class="text-orange-500" title="Trending"></span>
                            @endif
                            @if(!$book->is_featured && !$book->is_trending)
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($book->downloads ?? 0) }}</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.books.show', $book) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('admin.books.edit', $book) }}" class="text-green-600 hover:text-green-800" title="Edit">
                                <i class="ti ti-edit"></i>
                            </a>
                            @if($book->is_featured)
                                <form action="{{ route('admin.books.toggle-featured', $book) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Unfeature">
                                        <i class="ti ti-star-filled"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.books.toggle-featured', $book) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-yellow-600" title="Mark Featured">
                                        <i class="ti ti-star"></i>
                                    </button>
                                </form>
                            @endif
                            @if($book->is_trending)
                                <form action="{{ route('admin.books.toggle-trending', $book) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-800" title="Untrend">
                                        <i class="ti ti-flame-filled"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.books.toggle-trending', $book) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-400 hover:text-orange-600" title="Mark Trending">
                                        <i class="ti ti-flame"></i>
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $books->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
    <p class="text-gray-500">Click "Add New Book" to get started.</p>
</div>
@endif
@endsection