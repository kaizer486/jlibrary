@extends('layouts.institution')

@section('title', 'Institution Books')

@section('content')

@php
    // ==========================================
    // SECURITY CHECKS
    // ==========================================
    
    // Check if user belongs to an institution
    if (!auth()->user()->institution_id) {
        abort(403, 'You do not belong to any institution.');
    }
    
    // Check if institution exists
    if (!isset($institution) || !$institution) {
        abort(404, 'Institution not found.');
    }
    
    // Check if user has access to this institution
    if (auth()->user()->institution_id != $institution->id) {
        abort(403, 'You do not have access to this institution.');
    }
@endphp

<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📚 Institution Books</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $institution->name }}</p>
        </div>
        @can('create', App\Models\Book::class)
            <a href="{{ route('institution.books.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shadow-md flex items-center gap-2">
                <i class="ti ti-plus"></i> Add New Book
            </a>
        @endcan
    </div>
</div>

<!-- ========================================== -->
<!-- BOOK STATISTICS                            -->
<!-- ========================================== -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">Total Books</p>
        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Approved</p>
        <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-yellow-500">
        <p class="text-sm text-gray-500">Pending</p>
        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500">
        <p class="text-sm text-gray-500">Rejected</p>
        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</p>
    </div>
</div>

<!-- ========================================== -->
<!-- SEARCH & FILTERS                           -->
<!-- ========================================== -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search books by title or author..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
        </div>
        <div>
            <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500">
                <option value="">All Types</option>
                <option value="free" {{ request('type') == 'free' ? 'selected' : '' }}>🆓 Free</option>
                <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>💰 Paid</option>
            </select>
        </div>
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
            <i class="ti ti-search"></i> Filter
        </button>
        <a href="{{ route('institution.books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
            <i class="ti ti-x"></i> Clear
        </a>
    </form>
</div>

<!-- ========================================== -->
<!-- BOOKS TABLE                                -->
<!-- ========================================== -->
@if($books->count() > 0)
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
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
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i class="ti ti-book text-indigo-600"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $book->title }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $book->author ?? 'Unknown' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($book->is_paid)
                            ${{ number_format($book->price, 2) }}
                        @else
                            <span class="text-green-600 font-medium">FREE</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($book->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            @can('update', $book)
                                <a href="{{ route('institution.books.edit', $book) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </a>
                            @endcan
                            @can('delete', $book)
                                <form action="{{ route('institution.books.destroy', $book) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this book?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-6">{{ $books->links() }}</div>
@else
<div class="bg-white rounded-xl shadow-sm p-12 text-center">
    <i class="ti ti-books text-6xl text-gray-400 mb-4 block"></i>
    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
    <p class="text-gray-500">No books have been added to this institution yet.</p>
    @can('create', App\Models\Book::class)
        <a href="{{ route('institution.books.create') }}" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="ti ti-plus"></i> Add Your First Book
        </a>
    @endcan
</div>
@endif

@endsection