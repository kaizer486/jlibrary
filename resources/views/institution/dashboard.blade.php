@extends('layouts.librarian')

@section('title', 'Institution Dashboard')
@section('page-title', '🏛️ Institution Dashboard')

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
                    <p class="text-slate-400 mt-1">Manage your institution from here.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('institution.books.create') }}" class="btn-library">
                        <i class="ti ti-plus"></i> Add Book
                    </a>
                    <a href="{{ route('institution.shelves.create') }}" class="btn-library-outline">
                        <i class="ti ti-plus"></i> Add Shelf
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="library-stat flex items-center justify-between">
            <div>
                <p class="number">{{ $stats['total_books'] ?? 0 }}</p>
                <p class="label">Total Books</p>
            </div>
            <i class="ti ti-books text-purple-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #34d399;">
            <div>
                <p class="number">{{ $stats['total_members'] ?? 0 }}</p>
                <p class="label">Total Members</p>
            </div>
            <i class="ti ti-users text-purple-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #fbbf24;">
            <div>
                <p class="number">{{ $stats['pending_requests'] ?? 0 }}</p>
                <p class="label">Join Requests</p>
            </div>
            <i class="ti ti-user-plus text-yellow-400/40 text-3xl"></i>
        </div>
        
        <div class="library-stat flex items-center justify-between" style="border-left-color: #f87171;">
            <div>
                <p class="number">{{ $stats['pending_withdrawal_requests'] ?? 0 }}</p>
                <p class="label">Pending Withdrawals</p>
            </div>
            <i class="ti ti-wallet text-red-400/40 text-3xl"></i>
        </div>
    </div>

    <!-- Subscription Status -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-slate-400 text-sm">Subscription Status</p>
                <p class="text-xl font-bold text-white">{{ $subscription['plan_label'] }}</p>
                <p class="text-sm {{ $subscription['is_active'] ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $subscription['status_label'] }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm text-slate-400">Days Left</p>
                    <p class="text-2xl font-bold text-white">{{ $subscription['days_left'] }}</p>
                </div>
                <a href="{{ route('institution.subscription.index') }}" class="btn-library-outline">
                    <i class="ti ti-settings"></i> Manage
                </a>
            </div>
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
                    <a href="{{ route('institution.books.index') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
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
                                    <th class="pb-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @foreach($recentBooks as $book)
                                    <tr class="hover:bg-slate-800/50 transition">
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-white">{{ Str::limit($book->title, 30) }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pr-4 text-slate-400">{{ $book->author ?? 'Unknown' }}</td>
                                        <td class="py-3 pr-4">
                                            @if($book->status === 'approved')
                                                <span class="badge-approved">✅ Approved</span>
                                            @elseif($book->status === 'pending')
                                                <span class="badge-pending">⏳ Pending</span>
                                            @else
                                                <span class="badge-rejected">❌ Rejected</span>
                                            @endif
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
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            
            <!-- Pending Join Requests -->
            <div class="library-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-white text-lg flex items-center gap-2">
                        <i class="ti ti-user-plus text-yellow-400"></i> Join Requests
                    </h3>
                    <a href="{{ route('institution.join-requests.index') }}" class="text-sm text-purple-400 hover:text-purple-300 font-medium">
                        View All →
                    </a>
                </div>
                
                @if(isset($recentRequests) && $recentRequests->count() > 0)
                    <div class="space-y-2">
                        @foreach($recentRequests as $request)
                            <div class="flex items-center justify-between p-2 bg-slate-800/50 rounded-lg">
                                <div>
                                    <p class="text-white text-sm font-medium">{{ $request->user->full_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $request->user->email }}</p>
                                </div>
                                <span class="badge-pending">⏳ Pending</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 text-sm text-center py-4">No pending requests</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="library-card bg-gradient-to-br from-purple-900/20 to-pink-900/10 border-purple-500/20">
                <h3 class="font-semibold text-white text-lg flex items-center gap-2 mb-4">
                    <i class="ti ti-zap text-purple-400"></i> Quick Actions
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('institution.books.create') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-plus block text-purple-400 text-lg"></i>
                        Add Book
                    </a>
                    <a href="{{ route('institution.shelves.create') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-layout-grid block text-purple-400 text-lg"></i>
                        Add Shelf
                    </a>
                    <a href="{{ route('institution.members.index') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-users block text-purple-400 text-lg"></i>
                        Members
                    </a>
                    <a href="{{ route('institution.borrowings.index') }}" class="bg-slate-800 hover:bg-purple-500/20 text-gray-300 hover:text-white px-3 py-2.5 rounded-lg text-sm font-medium text-center border border-slate-700 transition">
                        <i class="ti ti-bookmark block text-purple-400 text-lg"></i>
                        Borrowings
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection