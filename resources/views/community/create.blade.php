@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create a New Group</h1>
        <p class="text-gray-600">Build your community around shared interests</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('community.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Group Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Group Name *</label>
                <input type="text" name="name" id="name" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent"
                       placeholder="e.g., Medical Students TZ, Business Leaders">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" id="description" rows="4" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent"
                          placeholder="What is this group about?"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Cover Image -->
            <div class="mb-6">
                <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
                <input type="file" name="cover_image" id="cover_image" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                <p class="text-xs text-gray-500 mt-1">Recommended size: 800x400px</p>
                @error('cover_image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    <i class="ti ti-plus"></i> Create Group
                </button>
                <a href="{{ route('community.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection