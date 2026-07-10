@extends('layouts.super-admin')

@section('title', 'Media Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl p-6 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ti ti-palette text-3xl"></i>
                        <h1 class="text-2xl font-bold">Media Team Dashboard</h1>
                    </div>
                    <p class="text-yellow-100">Welcome back, {{ Auth::user()->full_name }}! Manage content and media.</p>
                </div>
                <div class="hidden md:block">
                    <i class="ti ti-palette text-6xl opacity-30"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Hero Slides -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Hero Slides</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $heroSlidesCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-slideshow text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('super-admin.hero-slides.index') }}" class="text-sm text-yellow-600 hover:text-yellow-700 mt-2 inline-block">
                Manage Slides →
            </a>
        </div>
        
        <!-- News Items -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">News & Updates</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $newsItemsCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-news text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('super-admin.news-items.index') }}" class="text-sm text-yellow-600 hover:text-yellow-700 mt-2 inline-block">
                Manage News →
            </a>
        </div>
        
        <!-- Founders -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Founders</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $foundersCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-users text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('super-admin.founders.index') }}" class="text-sm text-yellow-600 hover:text-yellow-700 mt-2 inline-block">
                Manage Founders →
            </a>
        </div>
        
        <!-- Site Settings -->
        <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Site Settings</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $siteSettingsCount ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="ti ti-settings text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <a href="{{ route('super-admin.site-settings.index') }}" class="text-sm text-yellow-600 hover:text-yellow-700 mt-2 inline-block">
                Manage Settings →
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Add Hero Slide -->
            <a href="{{ route('super-admin.hero-slides.create') }}" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition hover:scale-[1.02] group">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:bg-yellow-200 transition">
                    <i class="ti ti-slideshow text-yellow-600 text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Add Hero Slide</span>
            </a>

            <!-- Add News -->
            <a href="{{ route('super-admin.news-items.create') }}" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition hover:scale-[1.02] group">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:bg-yellow-200 transition">
                    <i class="ti ti-news text-yellow-600 text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Add News</span>
            </a>

            <!-- Add Founder -->
            <a href="{{ route('super-admin.founders.create') }}" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition hover:scale-[1.02] group">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:bg-yellow-200 transition">
                    <i class="ti ti-users text-yellow-600 text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Add Founder</span>
            </a>

            <!-- Site Settings -->
            <a href="{{ route('super-admin.site-settings.index') }}" class="bg-white border border-gray-200 rounded-xl p-4 text-center hover:shadow-lg transition hover:scale-[1.02] group">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center mx-auto mb-2 group-hover:bg-yellow-200 transition">
                    <i class="ti ti-settings text-yellow-600 text-2xl"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Site Settings</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h2>
        
        <div class="space-y-4">
            <!-- Hero Slides Recent -->
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="ti ti-slideshow text-yellow-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Latest Hero Slides</p>
                        <p class="text-xs text-gray-500">
                            {{ \App\Models\HeroSlide::latest()->first()?->title ?? 'No slides yet' }}
                        </p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">
                    {{ \App\Models\HeroSlide::latest()->first()?->created_at?->diffForHumans() ?? 'N/A' }}
                </span>
            </div>

            <!-- News Recent -->
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="ti ti-news text-yellow-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Latest News</p>
                        <p class="text-xs text-gray-500">
                            {{ \App\Models\NewsItem::latest()->first()?->title ?? 'No news yet' }}
                        </p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">
                    {{ \App\Models\NewsItem::latest()->first()?->created_at?->diffForHumans() ?? 'N/A' }}
                </span>
            </div>

            <!-- Founders Recent -->
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="ti ti-users text-yellow-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">Latest Founder</p>
                        <p class="text-xs text-gray-500">
                            {{ \App\Models\Founder::latest()->first()?->name ?? 'No founders yet' }}
                        </p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">
                    {{ \App\Models\Founder::latest()->first()?->created_at?->diffForHumans() ?? 'N/A' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection