@extends('layouts.master')



@section('title', 'Manage Books')

@section('page-content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Manage Books</h1>
            <p class="text-gray-500 text-sm mt-1">Manage your library collection</p>
        </div>
        <a href="{{ route('admin.books.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <i class="ti ti-plus"></i> Add New Book
        </a>
    </div>
</div>

<!-- Search and Filter Form - Simple POST/GET method -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.books.index') }}" class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <input type="text" name="search" placeholder="Search by title or author..." value="{{ request('search') }}" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="price_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="">All Books</option>
                <option value="free" {{ request('price_type') == 'free' ? 'selected' : '' }}>🆓 Free Only</option>
                <option value="paid" {{ request('price_type') == 'paid' ? 'selected' : '' }}>💰 Paid Only</option>
            </select>
        </div>
        <div>
            <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>📅 Latest First</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>📅 Oldest First</option>
                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>🔤 Title A-Z</option>
                <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>🔤 Title Z-A</option>
            </select>
        </div>
        <div>
            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition w-full">
                🔍 Filter
            </button>
        </div>
        <div>
            <a href="{{ route('admin.books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition inline-block text-center w-full">
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Books</p>
        <p class="text-2xl font-bold text-gray-900">{{ $totalBooks ?? $books->total() }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ $approvedBooks ?? \App\Models\Book::where('status','approved')->count() }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $pendingBooks ?? \App\Models\Book::where('status','pending')->count() }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Free Books</p>
        <p class="text-2xl font-bold text-blue-600">{{ $freeBooks ?? \App\Models\Book::where('is_paid', false)->count() }}</p>
    </div>
</div>

<!-- Books Table -->
@if($books->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($books as $book)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ $book->title }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $book->author }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    @if($book->is_paid)
                        ${{ number_format($book->price, 2) }}
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
                <td class="px-6 py-4 text-sm">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.books.edit', $book) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                        <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline" onsubmit="return confirm('Delete "{{ addslashes($book->title) }}" permanently?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $books->appends(request()->query())->links() }}
</div>

@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
    <p class="text-gray-500 mb-4">Try changing your search filters or add a new book.</p>
    <a href="{{ route('admin.books.create') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
        <i class="ti ti-plus"></i> Add Your First Book
    </a>
</div>
@endif
@endsection