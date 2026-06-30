@extends('layouts.librarian')

@section('title', 'Book Management')
@section('page-title', '📖 Book Management')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">Manage your library collection</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('librarian.books.create') }}" class="btn-library">
                <i class="ti ti-plus"></i> Add New Book
            </a>
            <button onclick="window.location.reload()" class="btn-library-outline">
                <i class="ti ti-refresh"></i>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-purple-500">
            <p class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">Total Books</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-emerald-500">
            <p class="text-2xl font-bold text-emerald-400">{{ $stats['approved'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">✅ Approved</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-yellow-500">
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">⏳ Pending</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 border-l-4 border-red-500">
            <p class="text-2xl font-bold text-red-400">{{ $stats['rejected'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">❌ Rejected</p>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" placeholder="Search by title, author..." 
                       value="{{ request('search') }}"
                       class="search-bar">
            </div>
            <select name="status" class="search-bar w-auto">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
            </select>
            <select name="shelf" class="search-bar w-auto">
                <option value="">All Shelves</option>
                @foreach($shelves ?? [] as $shelf)
                    <option value="{{ $shelf->code }}" {{ request('shelf') == $shelf->code ? 'selected' : '' }}>
                        {{ $shelf->code }} - {{ $shelf->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-library">
                <i class="ti ti-search"></i> Filter
            </button>
            <a href="{{ route('librarian.books.index') }}" class="bg-slate-800 text-slate-400 px-4 py-2 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                <i class="ti ti-x"></i> Clear
            </a>
        </form>
    </div>

    <!-- Books Table -->
    @if($books->count() > 0)
        <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-800/50 text-left border-b border-slate-700">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Book</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Author</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Shelf</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($books as $book)
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($book->cover_image)
                                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-10 h-14 object-cover rounded-lg">
                                        @else
                                            <div class="w-10 h-14 bg-slate-800 rounded-lg flex items-center justify-center">
                                                <i class="ti ti-book text-purple-400"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-white">{{ Str::limit($book->title, 30) }}</p>
                                            <p class="text-xs text-slate-400">{{ $book->category ?? 'Uncategorized' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $book->author ?? 'Unknown' }}</td>
                                <td class="px-4 py-3">
                                    @if($book->shelf_number)
                                        <span class="text-xs bg-purple-500/20 text-purple-300 px-2.5 py-1 rounded-full font-medium">
                                            {{ $book->shelf_number }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-500">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    @if($book->is_paid)
                                        <span class="text-purple-400">TSh {{ number_format($book->price, 2) }}</span>
                                    @else
                                        <span class="text-emerald-400 font-bold">FREE</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($book->status === 'approved')
                                        <span class="badge-approved">✅ Approved</span>
                                    @elseif($book->status === 'pending')
                                        <span class="badge-pending">⏳ Pending</span>
                                    @else
                                        <span class="badge-rejected">❌ Rejected</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($book->status === 'pending')
                                            <form method="POST" action="{{ route('librarian.books.approve', $book) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-emerald-400 hover:text-emerald-300 transition" title="Approve">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('librarian.books.reject', $book) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Reject">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('librarian.books.show', $book) }}" class="text-purple-400 hover:text-purple-300 transition" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('librarian.books.edit', $book) }}" class="text-blue-400 hover:text-blue-300 transition" title="Edit">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('librarian.books.destroy', $book) }}" 
                                              onsubmit="return confirm('Delete this book?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Delete">
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
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $books->withQueryString()->links() }}
        </div>
        
    @else
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-12 text-center">
            <i class="ti ti-books text-6xl text-slate-600 mb-4 block"></i>
            <h3 class="text-xl font-semibold text-white mb-2">No Books Found</h3>
            <p class="text-slate-400">Start building your library collection.</p>
            <a href="{{ route('librarian.books.create') }}" class="inline-block mt-4 btn-library">
                <i class="ti ti-plus"></i> Add Your First Book
            </a>
        </div>
    @endif

</div>

@endsection