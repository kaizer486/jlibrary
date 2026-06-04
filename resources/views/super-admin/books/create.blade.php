@extends('layouts.super-admin')

@section('title', 'Add New Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.books.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-book-plus"></i> Add New Book
            </h1>
            <p class="text-purple-200 text-sm mt-1">Add a new book to the library collection</p>
        </div>

        <form method="POST" action="{{ route('super-admin.books.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Book Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Author <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="author" value="{{ old('author') }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Institution
                        </label>
                        <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="">-- Select Institution --</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }} ({{ $inst->type_label }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Assign this book to an institution</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="price" step="0.01" value="{{ old('price', 0) }}" 
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl">
                            <input type="checkbox" name="is_paid" value="1" {{ old('is_paid') ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded">
                            <span class="text-sm text-gray-700">This is a paid book</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Total Pages
                        </label>
                        <input type="number" name="total_pages" value="{{ old('total_pages', 0) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Book File (PDF) <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="book_file" accept=".pdf" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                        <p class="text-xs text-gray-400 mt-1">Max 20MB. Only PDF files allowed.</p>
                        @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Cover Image (Optional)
                        </label>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700">
                        <p class="text-xs text-gray-400 mt-1">Max 2MB. JPG, PNG only. Recommended: 500x700px</p>
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
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">{{ old('description') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Add Book
                </button>
                <a href="{{ route('super-admin.books.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection