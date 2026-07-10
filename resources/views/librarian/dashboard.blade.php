@extends('layouts.librarian')

@section('title', 'Dashboard')
@section('page-title', '📊 Dashboard')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 max-w-7xl mx-auto py-8">
    
    <!-- ========================================== -->
    <!-- WELCOME SECTION - GLASS CARD               -->
    <!-- ========================================== -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                        <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                            <i class="ti ti-user text-white text-xl"></i>
                        </span>
                        Welcome back, {{ auth()->user()->full_name }}
                    </h2>
                    <p class="text-slate-600 mt-1">Manage your library collection, shelves, and members from here.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('librarian.books.create') }}" class="bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium">
                        <i class="ti ti-plus"></i> Add Book
                    </a>
                    <a href="{{ route('librarian.shelves.create') }}" class="bg-white hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl transition flex items-center gap-2 text-sm font-medium border-2 border-slate-300">
                        <i class="ti ti-plus"></i> Add Shelf
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- STATS CARDS - CLEAR DEMARCATION            -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_books'] ?? 0 }}</p>
                <p class="text-xs text-slate-600 font-medium">Total Books</p>
            </div>
            <i class="ti ti-books text-orange-400/60 text-3xl"></i>
        </div>
        
        <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-2xl font-bold text-amber-600">{{ $stats['pending_books'] ?? 0 }}</p>
                <p class="text-xs text-slate-600 font-medium">Pending Approval</p>
            </div>
            <i class="ti ti-clock text-amber-400/60 text-3xl"></i>
        </div>
        
        <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved_books'] ?? 0 }}</p>
                <p class="text-xs text-slate-600 font-medium">Approved Books</p>
            </div>
            <i class="ti ti-check text-emerald-400/60 text-3xl"></i>
        </div>
        
        <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-5 flex items-center justify-between hover:shadow-lg transition">
            <div>
                <p class="text-2xl font-bold text-red-600">{{ $stats['rejected_books'] ?? 0 }}</p>
                <p class="text-xs text-slate-600 font-medium">Rejected Books</p>
            </div>
            <i class="ti ti-x text-red-400/60 text-3xl"></i>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SECONDARY STATS                            -->
    <!-- ========================================== -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border-2 border-slate-200/80 shadow-md p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $stats['total_members'] ?? 0 }}</p>
            <p class="text-xs text-slate-600">👥 Total Members</p>
        </div>
        <div class="bg-white rounded-xl border-2 border-slate-200/80 shadow-md p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['total_shelves'] ?? 0 }}</p>
            <p class="text-xs text-slate-600">🗄️ Total Shelves</p>
        </div>
        <div class="bg-white rounded-xl border-2 border-slate-200/80 shadow-md p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $stats['total_categories'] ?? 0 }}</p>
            <p class="text-xs text-slate-600">📂 Categories</p>
        </div>
        <div class="bg-white rounded-xl border-2 border-slate-200/80 shadow-md p-4 text-center">
            <p class="text-2xl font-bold text-amber-600">{{ $stats['total_downloads'] ?? 0 }}</p>
            <p class="text-xs text-slate-600">⬇️ Total Downloads</p>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MAIN GRID                                  -->
    <!-- ========================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- ========================================== -->
        <!-- RECENT BOOKS - LEFT COLUMN                 -->
        <!-- ========================================== -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-800 text-lg flex items-center gap-2">
                        <i class="ti ti-books text-orange-500"></i> Recent Books
                    </h3>
                    <a href="{{ route('librarian.books.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                        View All →
                    </a>
                </div>
                
                @if(isset($recentBooks) && $recentBooks->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 text-xs uppercase tracking-wider border-b-2 border-slate-200">
                                    <th class="pb-2 font-medium">Book</th>
                                    <th class="pb-2 font-medium">Author</th>
                                    <th class="pb-2 font-medium">Shelf</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($recentBooks as $book)
                                    <tr class="hover:bg-orange-50/50 transition">
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                @if($book->cover_image)
                                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-8 h-10 object-cover rounded">
                                                @else
                                                    <div class="w-8 h-10 bg-orange-100 rounded flex items-center justify-center">
                                                        <i class="ti ti-book text-orange-400 text-xs"></i>
                                                    </div>
                                                @endif
                                                <span class="font-medium text-slate-800 truncate max-w-[120px]">{{ $book->title }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-slate-600">{{ $book->author ?? 'Unknown' }}</td>
                                        <td class="py-3 pr-4">
                                            @if($book->shelf_number)
                                                <span class="text-xs bg-orange-500/20 text-orange-700 px-2 py-0.5 rounded-full border border-orange-500/20">{{ $book->shelf_number }}</span>
                                            @else
                                                <span class="text-xs text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if($book->status === 'approved')
                                                <span class="text-xs bg-emerald-500/20 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-500/20">✅ Approved</span>
                                            @elseif($book->status === 'pending')
                                                <span class="text-xs bg-yellow-500/20 text-yellow-700 px-2 py-0.5 rounded-full border border-yellow-500/20">⏳ Pending</span>
                                            @else
                                                <span class="text-xs bg-red-500/20 text-red-700 px-2 py-0.5 rounded-full border border-red-500/20">❌ Rejected</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-right">
                                            <a href="{{ route('librarian.books.edit', $book) }}" class="text-orange-600 hover:text-orange-700 text-sm">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="{{ route('librarian.books.show', $book) }}" class="text-orange-600 hover:text-orange-700 text-sm ml-2">
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
                        <i class="ti ti-books text-4xl mb-2 block text-orange-400/30"></i>
                        <p>No books added yet.</p>
                        <a href="{{ route('librarian.books.create') }}" class="text-orange-600 hover:underline text-sm">Add your first book →</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========================================== -->
        <!-- RIGHT COLUMN                               -->
        <!-- ========================================== -->
        <div class="space-y-6">
            
            <!-- ========================================== -->
            <!-- SHELF MAP                                  -->
            <!-- ========================================== -->
            <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-6">
                <h3 class="font-semibold text-slate-800 text-lg flex items-center gap-2 mb-4">
                    <i class="ti ti-map-pin text-orange-500"></i> Shelf Map
                </h3>
                
                @if(isset($shelves) && $shelves->count() > 0)
                    <div class="space-y-3">
                        @php
                            $floors = $shelves->groupBy('floor')->sortKeys();
                        @endphp
                        @foreach($floors as $floor => $shelfItems)
                            <div>
                                <p class="text-xs font-semibold text-orange-600 uppercase tracking-wider mb-1.5">
                                    {{ $floor ?? 'Unassigned' }}
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($shelfItems as $shelf)
                                        <a href="{{ route('librarian.shelves.show', $shelf) }}" 
                                           class="text-xs bg-orange-500/10 hover:bg-orange-500/20 text-orange-700 px-2.5 py-1 rounded-full border border-orange-500/20 transition">
                                            {{ $shelf->code }}
                                            <span class="text-orange-400/60 text-[10px]">({{ $shelf->current_count }}/{{ $shelf->capacity }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 text-slate-500">
                        <i class="ti ti-layout-grid text-3xl mb-2 block text-orange-400/30"></i>
                        <p class="text-sm">No shelves created yet.</p>
                        <a href="{{ route('librarian.shelves.create') }}" class="text-orange-600 hover:underline text-sm">Create a shelf →</a>
                    </div>
                @endif
                
                <div class="mt-4 pt-4 border-t-2 border-slate-200">
                    <a href="{{ route('librarian.shelves.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                        Manage All Shelves →
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- QUICK ACTIONS                              -->
            <!-- ========================================== -->
            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl border-2 border-orange-200/80 shadow-md p-6">
                <h3 class="font-semibold text-slate-800 text-lg flex items-center gap-2 mb-4">
                    <i class="ti ti-zap text-orange-500"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('librarian.books.create') }}" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 px-3 py-2.5 rounded-lg text-sm font-medium text-center border-2 border-slate-200 hover:border-orange-300 transition">
                        <i class="ti ti-plus block text-orange-500 text-lg"></i>
                        Add Book
                    </a>
                    <a href="{{ route('librarian.shelves.create') }}" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 px-3 py-2.5 rounded-lg text-sm font-medium text-center border-2 border-slate-200 hover:border-orange-300 transition">
                        <i class="ti ti-layout-grid block text-orange-500 text-lg"></i>
                        Add Shelf
                    </a>
                    <a href="{{ route('librarian.members.index') }}" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 px-3 py-2.5 rounded-lg text-sm font-medium text-center border-2 border-slate-200 hover:border-orange-300 transition">
                        <i class="ti ti-users block text-orange-500 text-lg"></i>
                        Members
                    </a>
                    <a href="{{ route('librarian.reports.index') }}" class="bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-700 px-3 py-2.5 rounded-lg text-sm font-medium text-center border-2 border-slate-200 hover:border-orange-300 transition">
                        <i class="ti ti-chart-bar block text-orange-500 text-lg"></i>
                        Reports
                    </a>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- PENDING JOIN REQUESTS                      -->
            <!-- ========================================== -->
            @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                <div class="bg-white rounded-2xl border-2 border-slate-200/80 shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-slate-800 text-lg flex items-center gap-2">
                            <i class="ti ti-user-plus text-amber-500"></i> Pending Join Requests
                            <span class="text-xs bg-amber-500/20 text-amber-700 px-2 py-0.5 rounded-full border border-amber-500/20">{{ $pendingRequests->count() }}</span>
                        </h3>
                        <a href="{{ route('librarian.join-requests') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">
                            View All →
                        </a>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($pendingRequests->take(5) as $request)
                            <div class="flex items-center justify-between p-3 bg-orange-50/60 rounded-lg border-2 border-orange-100/80">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white font-bold text-sm">
                                        {{ strtoupper(substr($request->user->full_name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $request->user->full_name }}</p>
                                        <p class="text-xs text-slate-600">{{ $request->user->email }}</p>
                                        @if($request->message)
                                            <p class="text-xs text-slate-500 mt-1">"{{ Str::limit($request->message, 50) }}"</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('librarian.join-requests.approve', $request) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-700 px-3 py-1.5 rounded-lg text-xs font-medium transition border-2 border-emerald-500/20 hover:border-emerald-500/40">
                                            <i class="ti ti-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('librarian.join-requests.reject', $request) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium transition border-2 border-red-500/20 hover:border-red-500/40">
                                            <i class="ti ti-x"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection