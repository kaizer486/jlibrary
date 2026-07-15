@extends('layouts.super-admin')

@section('title', 'Edit News Item')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('super-admin.news-items.index') }}" class="text-gray-600 hover:text-gray-800 transition">
            <i class="ti ti-arrow-left text-2xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit News Item</h1>
            <p class="text-gray-500 text-sm mt-1">Update the news item content</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
        <form action="{{ route('super-admin.news-items.update', $newsItem) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                       value="{{ old('title', $newsItem->title) }}" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                <textarea name="content" id="content" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('content', $newsItem->content) }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Brief description or summary of the news item</p>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Link -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        <option value="">Select Category</option>
                        <option value="Books" {{ old('category', $newsItem->category) == 'Books' ? 'selected' : '' }}> Books</option>
                        <option value="Events" {{ old('category', $newsItem->category) == 'Events' ? 'selected' : '' }}> Events</option>
                        <option value="Certificates" {{ old('category', $newsItem->category) == 'Certificates' ? 'selected' : '' }}> Certificates</option>
                        <option value="Announcements" {{ old('category', $newsItem->category) == 'Announcements' ? 'selected' : '' }}>Announcements</option>
                        <option value="Authors" {{ old('category', $newsItem->category) == 'Authors' ? 'selected' : '' }}> Authors</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 mb-1">Link (Optional)</label>
                    <input type="text" name="link" id="link" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('link', $newsItem->link) }}" placeholder="/library">
                    <p class="text-xs text-gray-500 mt-1">URL to direct users when clicked</p>
                    @error('link')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Published Date & Order -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-1">Published Date <span class="text-red-500">*</span></label>
                    <input type="date" name="published_at" id="published_at" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('published_at', $newsItem->published_at->format('Y-m-d')) }}" required>
                    @error('published_at')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" id="order" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('order', $newsItem->order) }}">
                    @error('order')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Featured & Status -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" 
                               {{ old('is_featured', $newsItem->is_featured) ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700"> Featured</span>
                    </label>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $newsItem->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    Update News Item
                </button>
                <a href="{{ route('super-admin.news-items.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection