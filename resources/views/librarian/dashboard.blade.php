@extends('layouts.librarian')

@section('title', 'Dashboard')
@section('page-title', '📊 Dashboard')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 shadow-lg">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-white">
                        Welcome back, {{ auth()->user()->full_name }}
                    </h2>
                    <p class="text-slate-400 mt-1">Manage your library collection, shelves, and members from here.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('librarian.books.create') }}" class="btn-library">
                        <i class="ti ti-plus"></i> Add Book
                    </a>
                    <a href="{{ route('librarian.shelves.create') }}" class="btn-library-outline">
                        <i class="ti ti-plus"></i> Add Shelf
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards - Dark Glassmorphism -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="library-stat flex items-center justify-between">
            <div>
                <p class="number">{{ $stats['total_books'] ?? 0 }}</p>
                <p class="label">Total Books</p>
            </div>
            <i class="ti ti-books text-purple-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #fbbf24;">
            <div>
                <p class="number">{{ $stats['pending_books'] ?? 0 }}</p>
                <p class="label">Pending Approval</p>
            </div>
            <i class="ti ti-clock text-yellow-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #34d399;">
            <div>
                <p class="number">{{ $stats['approved_books'] ?? 0 }}</p>
                <p class="label">Approved Books</p>
            </div>
            <i class="ti ti-check text-emerald-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #f87171;">
            <div>
                <p class="number">{{ $stats['rejected_books'] ?? 0 }}</p>
                <p class="label">Rejected Books</p>
            </div>
            <i class="ti ti-x text-red-400/40 text-3xl"></i>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 text-white">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white-300">{{ $stats['total_members'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">👥 Total Members</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white-300">{{ $stats['total_shelves'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">🗄️ Total Shelves</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white-300">{{ $stats['total_categories'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📂 Categories</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-white-300">{{ $stats['total_downloads'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">⬇️ Total Downloads</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Books -->
        <div class="lg:col-span-2">
            <div class="library-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white text-lg flex items-center gap-2">
                        <i class="ti ti-books text-purple-400"></i> Recent Books
                    </h3>
                    <a href="{{ route('librarian.books.index') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
                        View All →
                    </a>
                </div>
                
                @if(isset($recentBooks) && $recentBooks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700">
                                    <th class="pb-2 font-medium">Book</th>
                                    <th class="pb-2 font-medium">Author</th>
                                    <th class="pb-2 font-medium">Shelf</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($recentBooks as $book)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                @if($book->cover_image)
                                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-8 h-10 object-cover rounded">
                                                @else
                                                    <div class="w-8 h-10 bg-slate-800 rounded flex items-center justify-center">
                                                        <i class="ti ti-book text-purple-400 text-xs"></i>
                                                    </div>
                                                @endif
                                                <span class="font-medium text-white truncate max-w-[120px]">{{ $book->title }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-slate-400">{{ $book->author ?? 'Unknown' }}</td>
                                        <td class="py-3 pr-4">
                                            @if($book->shelf_number)
                                                <span class="text-xs bg-purple-500/20 text-purple-300 px-2 py-0.5 rounded-full">{{ $book->shelf_number }}</span>
                                            @else
                                                <span class="text-xs text-slate-500">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if($book->status === 'approved')
                                                <span class="badge-approved">✅ Approved</span>
                                            @elseif($book->status === 'pending')
                                                <span class="badge-pending">⏳ Pending</span>
                                            @else
                                                <span class="badge-rejected">❌ Rejected</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('librarian.books.edit', $book) }}" class="text-purple-400 hover:text-purple-300 text-sm">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="{{ route('librarian.books.show', $book) }}" class="text-purple-400 hover:text-purple-300 text-sm ml-2">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <i class="ti ti-books text-4xl mb-2 block text-purple-400/30"></i>
                        <p>No books added yet.</p>
                        <a href="{{ route('librarian.books.create') }}" class="text-purple-400 hover:underline text-sm">Add your first book →</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Shelf Map & Quick Actions -->
        <div class="space-y-6">
            
            <!-- Shelf Map -->
            <div class="library-card">
                <h3 class="font-semibold text-white text-lg flex items-center gap-2 mb-4">
                    <i class="ti ti-map-pin text-purple-400"></i> Shelf Map
                </h3>
                
                @if(isset($shelves) && $shelves->count() > 0)
                    <div class="space-y-3">
                        @php
                            $floors = $shelves->groupBy('floor')->sortKeys();
                        @endphp
                        @foreach($floors as $floor => $shelfItems)
                            <div>
                                <p class="text-xs font-semibold text-purple-400 uppercase tracking-wider mb-1.5">
                                    {{ $floor ?? 'Unassigned' }}
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($shelfItems as $shelf)
                                        <a href="{{ route('librarian.shelves.show', $shelf) }}" 
                                           class="text-xs bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 px-2.5 py-1 rounded-full border border-purple-500/20 transition">
                                            {{ $shelf->code }}
                                            <span class="text-purple-400/40 text-[10px]">({{ $shelf->current_count }}/{{ $shelf->capacity }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-500">
                        <i class="ti ti-layout-grid text-3xl mb-2 block text-purple-400/30"></i>
                        <p class="text-sm">No shelves created yet.</p>
                        <a href="{{ route('librarian.shelves.create') }}" class="text-purple-400 hover:underline text-sm">Create a shelf →</a>
                    </div>
                @endif
                
                <div class="mt-4 pt-4 border-t border-slate-700">
                    <a href="{{ route('librarian.shelves.index') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
                        Manage All Shelves →
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="library-card bg-gradient-to-br from-purple-900/20 to-pink-900/10 border-purple-500/20">
                <h3 class="font-semibold text-white text-lg flex items-center gap-2 mb-4">
                    <i class="ti ti-zap text-purple-400"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('librarian.books.create') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-plus block text-purple-400 text-lg"></i>
                        Add Book
                    </a>
                    <a href="{{ route('librarian.shelves.create') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-layout-grid block text-purple-400 text-lg"></i>
                        Add Shelf
                    </a>
                    <a href="{{ route('librarian.members.index') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-users block text-purple-400 text-lg"></i>
                        Members
                    </a>
                    <a href="{{ route('librarian.reports.index') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-chart-bar block text-purple-400 text-lg"></i>
                        Reports
                    </a>
                </div>
            </div>

        <!-- ========================================== -->
        <!-- PENDING JOIN REQUESTS                       -->
        <!-- ========================================== -->
        @if(isset($pendingRequests) && $pendingRequests->count() > 0)
            <div class="mt-6">
                <div class="library-card">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-white text-lg flex items-center gap-2">
                            <i class="ti ti-user-plus text-yellow-400"></i> Pending Join Requests
                            <span class="text-xs bg-yellow-500/20 text-yellow-400 px-2 py-0.5 rounded-full">{{ $pendingRequests->count() }}</span>
                        </h3>
                        <a href="{{ route('librarian.join-requests') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
                            View All →
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($pendingRequests->take(5) as $request)
                            <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($request->user->full_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-white">{{ $request->user->full_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $request->user->email }}</p>
                                        @if($request->message)
                                            <p class="text-xs text-slate-500 mt-1">"{{ Str::limit($request->message, 50) }}"</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('librarian.join-requests.approve', $request) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-medium transition border border-emerald-500/20">
                                            <i class="ti ti-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('librarian.join-requests.reject', $request) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 px-3 py-1.5 rounded-lg text-xs font-medium transition border border-red-500/20">
                                            <i class="ti ti-x"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        </div>
    </div>

</div>

@endsection