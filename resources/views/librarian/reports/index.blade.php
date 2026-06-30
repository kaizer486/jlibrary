@extends('layouts.librarian')

@section('title', 'Reports & Analytics')
@section('page-title', '📊 Reports & Analytics')

@section('content')

<div class="max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <p class="text-amber-600/70 text-sm">View library analytics and reports</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('librarian.reports.export') }}" class="btn-gold-outline">
                <i class="ti ti-download"></i> Export Report
            </a>
            <button onclick="window.location.reload()" class="btn-gold-outline">
                <i class="ti ti-refresh"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-amber-100/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-amber-900">{{ $stats['total_books'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600/70">📚 Total Books</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-books text-amber-600"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-amber-600/50">
                <span class="text-emerald-600">▲ {{ $stats['growth'] ?? 0 }}%</span> from last month
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border border-amber-100/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-amber-900">{{ $stats['total_members'] ?? 0 }}</p>
                    <p class="text-xs text-amber-600/70">👥 Total Members</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-users text-amber-600"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-amber-600/50">
                <span class="text-emerald-600">▲ {{ $stats['member_growth'] ?? 0 }}%</span> from last month
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border border-amber-100/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-amber-900">{{ number_format($stats['total_views'] ?? 0) }}</p>
                    <p class="text-xs text-amber-600/70">👁️ Total Views</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-eye text-amber-600"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-amber-600/50">
                <span class="text-emerald-600">▲ {{ $stats['view_growth'] ?? 0 }}%</span> from last month
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 shadow-sm border border-amber-100/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-2xl font-bold text-amber-900">{{ number_format($stats['total_downloads'] ?? 0) }}</p>
                    <p class="text-xs text-amber-600/70">⬇️ Total Downloads</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                    <i class="ti ti-download text-amber-600"></i>
                </div>
            </div>
            <div class="mt-2 text-xs text-amber-600/50">
                <span class="text-emerald-600">▲ {{ $stats['download_growth'] ?? 0 }}%</span> from last month
            </div>
        </div>
    </div>

    <!-- Revenue Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-amber-800 to-amber-700 rounded-xl p-6 text-white">
            <p class="text-amber-300/80 text-sm">Total Revenue</p>
            <p class="text-3xl font-bold">TSh {{ number_format($revenue['total'] ?? 0, 2) }}</p>
            <div class="mt-2 flex gap-4 text-xs">
                <span>📚 Book Sales: TSh {{ number_format($revenue['book_sales'] ?? 0, 2) }}</span>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-emerald-700 to-emerald-600 rounded-xl p-6 text-white">
            <p class="text-emerald-300/80 text-sm">Library Share (80%)</p>
            <p class="text-3xl font-bold">TSh {{ number_format($revenue['library_share'] ?? 0, 2) }}</p>
            <div class="mt-2 text-xs text-emerald-300/80">
                <span>💰 Your institution's earnings</span>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-700 to-purple-600 rounded-xl p-6 text-white">
            <p class="text-purple-300/80 text-sm">Platform Share (20%)</p>
            <p class="text-3xl font-bold">TSh {{ number_format($revenue['platform_share'] ?? 0, 2) }}</p>
            <div class="mt-2 text-xs text-purple-300/80">
                <span>🏢 Platform maintenance fee</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Popular Books -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-amber-100/50">
            <h3 class="font-semibold text-amber-900 text-lg mb-4 flex items-center gap-2">
                <i class="ti ti-trending-up text-amber-500"></i> Most Popular Books
            </h3>
            @if(isset($popularBooks) && $popularBooks->count() > 0)
                <div class="space-y-3">
                    @foreach($popularBooks as $book)
                        <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-amber-400 w-6">{{ $loop->iteration }}</span>
                                <div>
                                    <p class="font-medium text-amber-900 text-sm">{{ Str::limit($book->title, 30) }}</p>
                                    <p class="text-xs text-amber-600/60">{{ $book->author ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-amber-800">{{ number_format($book->views_count ?? 0) }}</p>
                                <p class="text-xs text-amber-600/50">views</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-amber-600/50 text-center py-4">No data available</p>
            @endif
        </div>

        <!-- Top Categories -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-amber-100/50">
            <h3 class="font-semibold text-amber-900 text-lg mb-4 flex items-center gap-2">
                <i class="ti ti-tags text-amber-500"></i> Top Categories
            </h3>
            @if(isset($topCategories) && $topCategories->count() > 0)
                <div class="space-y-3">
                    @foreach($topCategories as $category)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-amber-700">{{ $category->category ?? 'Uncategorized' }}</span>
                                <span class="font-semibold text-amber-900">{{ $category->total }}</span>
                            </div>
                            <div class="w-full bg-amber-100 rounded-full h-2">
                                @php
                                    $max = $topCategories->first()->total ?? 1;
                                    $percentage = ($category->total / $max) * 100;
                                @endphp
                                <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-amber-600/50 text-center py-4">No data available</p>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-amber-100/50">
        <div class="px-6 py-4 border-b bg-amber-50/50">
            <h3 class="font-semibold text-amber-900 flex items-center gap-2">
                <i class="ti ti-clock text-amber-500"></i> Recent Activity
            </h3>
        </div>
        <div class="divide-y divide-amber-50">
            @if(isset($recentActivity) && $recentActivity->count() > 0)
                @foreach($recentActivity as $activity)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-amber-50/30 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full 
                                @if($activity->type === 'book_added') bg-emerald-100 text-emerald-600
                                @elseif($activity->type === 'member_joined') bg-blue-100 text-blue-600
                                @elseif($activity->type === 'purchase') bg-amber-100 text-amber-600
                                @else bg-gray-100 text-gray-600 @endif 
                                flex items-center justify-center text-sm">
                                @if($activity->type === 'book_added')
                                    <i class="ti ti-book"></i>
                                @elseif($activity->type === 'member_joined')
                                    <i class="ti ti-user-plus"></i>
                                @elseif($activity->type === 'purchase')
                                    <i class="ti ti-shopping-cart"></i>
                                @else
                                    <i class="ti ti-activity"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-amber-900">{{ $activity->description }}</p>
                                <p class="text-xs text-amber-600/50">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-amber-600/50">{{ $activity->created_at->format('h:i A') }}</span>
                    </div>
                @endforeach
            @else
                <div class="px-6 py-8 text-center text-amber-600/50">
                    <i class="ti ti-activity text-3xl block mb-2"></i>
                    No recent activity
                </div>
            @endif
        </div>
    </div>

</div>

@endsection