@extends('layouts.librarian')

@section('title', 'Reports & Analytics')
@section('page-title', '📊 Reports & Analytics')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-slate-400 text-sm">View library analytics and reports</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('institution.reports.export') }}" class="btn-library-outline">
                <i class="ti ti-download"></i> Export Report
            </a>
            <button onclick="window.location.reload()" class="btn-library-outline">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $stats['total_books'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">📚 Total Books</p>
                </div>
                <div class="w-10 h-10 bg-purple-900/30 rounded-full flex items-center justify-center">
                    <i class="ti ti-books text-purple-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $stats['total_members'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">👥 Total Members</p>
                </div>
                <div class="w-10 h-10 bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="ti ti-users text-blue-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_views'] ?? 0) }}</p>
                    <p class="text-xs text-slate-400">👁️ Total Views</p>
                </div>
                <div class="w-10 h-10 bg-yellow-900/30 rounded-full flex items-center justify-center">
                    <i class="ti ti-eye text-yellow-400"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_downloads'] ?? 0) }}</p>
                    <p class="text-xs text-slate-400">⬇️ Total Downloads</p>
                </div>
                <div class="w-10 h-10 bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="ti ti-download text-green-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-purple-400">{{ $stats['total_borrowings'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📖 Total Borrowings</p>
        </div>
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-bold text-blue-400">{{ $stats['active_borrowings'] ?? 0 }}</p>
            <p class="text-xs text-slate-400">📚 Active Borrowings</p>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Popular Books -->
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
            <h3 class="font-semibold text-white text-lg mb-4 flex items-center gap-2">
                <i class="ti ti-trending-up text-purple-400"></i> Most Popular Books
            </h3>
            @if($popularBooks->count() > 0)
                <div class="space-y-3">
                    @foreach($popularBooks as $book)
                        <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-purple-400 w-6">{{ $loop->iteration }}</span>
                                <div>
                                    <p class="font-medium text-white text-sm">{{ Str::limit($book->title, 30) }}</p>
                                    <p class="text-xs text-slate-400">{{ $book->author ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-purple-400">{{ number_format($book->views_count ?? 0) }}</p>
                                <p class="text-xs text-slate-500">views</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-4">No data available</p>
            @endif
        </div>

        <!-- Top Categories -->
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
            <h3 class="font-semibold text-white text-lg mb-4 flex items-center gap-2">
                <i class="ti ti-tags text-purple-400"></i> Top Categories
            </h3>
            @if($topCategories->count() > 0)
                <div class="space-y-3">
                    @foreach($topCategories as $category)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-slate-300">{{ $category->category ?? 'Uncategorized' }}</span>
                                <span class="font-semibold text-purple-400">{{ $category->total }}</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2">
                                @php
                                    $max = $topCategories->first()->total ?? 1;
                                    $percentage = ($category->total / $max) * 100;
                                @endphp
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-center py-4">No data available</p>
            @endif
        </div>
    </div>

</div>

@endsection