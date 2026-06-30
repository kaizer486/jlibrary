@extends('layouts.admin')



@section('title', 'Add New Book')
@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-jlibrary-600">Dashboard</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <a href="{{ route('admin.books.index') }}" class="hover:text-jlibrary-600">Books</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <span class="text-gray-900">Add New Book</span>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900">📚 Add New Book</h1>
        <p class="text-gray-600 mt-1">Upload a new book to the library collection.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Book Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Author <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="author" value="{{ old('author') }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                        @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="price" step="0.01" value="{{ old('price', 0) }}" 
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="is_paid" value="1" {{ old('is_paid') ? 'checked' : '' }} class="w-4 h-4 text-jlibrary-600 rounded">
                            <span class="text-sm text-gray-700">This is a paid book</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Total Pages
                        </label>
                        <input type="number" name="total_pages" value="{{ old('total_pages', 0) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Book File (PDF) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="book_file" accept=".pdf" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-jlibrary-50 file:text-jlibrary-700">
                        <p class="text-xs text-gray-400 mt-1">Max 20MB. Only PDF files allowed.</p>
                        @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Cover Image (Optional)
                        </label>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-jlibrary-50 file:text-jlibrary-700">
                        <p class="text-xs text-gray-400 mt-1">Max 2MB. JPG, PNG only.</p>
                        @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            
            <!-- Description - Full Width -->
            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" rows="5" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">{{ old('description') }}</textarea>
            </div>
            
            <!-- Form Actions -->
     <div class="flex gap-3 mt-8 pt-6 border-t">
    <button type="submit" style="background-color: #1f42a3c0; color: white;" class="flex-1 px-6 py-3 rounded-lg shadow-md hover:shadow-lg transition-all duration-300 font-semibold flex items-center justify-center gap-2">
        <i class="ti ti-device-floppy text-lg"></i>
        <span>📚 ADD BOOK TO LIBRARY</span>
    </button>
    <a href="{{ route('admin.books.index') }}" class="px-6 py-3 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition text-center font-medium text-gray-700">
        ❌ Cancel
    </a>
</div>
        </form>
    </div>
</div>
@endsection