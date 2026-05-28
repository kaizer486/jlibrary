@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('marketplace.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700 mb-4 inline-block">
            <i class="ti ti-arrow-left"></i> Back to Marketplace
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Sell Your Book</h1>
        <p class="text-gray-600">Share your knowledge and earn money</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('marketplace.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Book Title *</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea name="description" rows="5" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-jlibrary-500"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Price (USD) *</label>
                <input type="number" name="price" step="0.01" min="0" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                @error('price')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
                <input type="file" name="cover_image" accept="image/*" class="w-full">
                <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 2MB)</p>
                @error('cover_image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Book File (PDF) *</label>
                <input type="file" name="book_file" accept=".pdf" required class="w-full">
                <p class="text-xs text-gray-500 mt-1">PDF only (Max 20MB)</p>
                @error('book_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-2">How it works</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>📝 Submit your book for admin review</li>
                    <li>✅ Admin approves within 48 hours</li>
                    <li>💰 You earn 80% of each sale</li>
                    <li>🌍 Reach readers worldwide</li>
                </ul>
            </div>
            
            <button type="submit" class="w-full bg-jlibrary-600 text-white px-6 py-3 rounded-lg hover:bg-jlibrary-700 transition font-semibold">
                Submit for Approval
            </button>
        </form>
    </div>
</div>
@endsection