@extends('layouts.bookshop')

@section('title', 'Add Book')

@section('content')
<div class="max-w-3xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('bookshop.books.index') }}" class="text-orange-600 hover:text-orange-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">📚 Add New Book</h1>
            <p class="text-orange-100 text-sm">Add a new book to your bookshop inventory</p>
        </div>

        <form method="POST" action="{{ route('bookshop.books.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="Book title...">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Author</label>
                    <input type="text" name="author" value="{{ old('author') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                           placeholder="Author name...">
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                              placeholder="Book description...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Price (TSh) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" step="0.01" required value="{{ old('price') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="0.00">
                        @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" required value="{{ old('stock_quantity', 0) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="0">
                        @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white">
                            <option value="">Select Category</option>
                            <option value="fiction" {{ old('category') == 'fiction' ? 'selected' : '' }}>📖 Fiction</option>
                            <option value="non-fiction" {{ old('category') == 'non-fiction' ? 'selected' : '' }}>📚 Non-Fiction</option>
                            <option value="science" {{ old('category') == 'science' ? 'selected' : '' }}>🔬 Science</option>
                            <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>💻 Technology</option>
                            <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>🎓 Education</option>
                            <option value="history" {{ old('category') == 'history' ? 'selected' : '' }}>📜 History</option>
                            <option value="art" {{ old('category') == 'art' ? 'selected' : '' }}>🎨 Art</option>
                            <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>💼 Business</option>
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
                            <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>📦 Out of Stock</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">ISBN</label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="978-3-16-148410-0">
                        @error('isbn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pages</label>
                        <input type="number" name="pages" value="{{ old('pages') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="Number of pages">
                        @error('pages') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Publisher</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="Publisher name">
                        @error('publisher') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Publication Year</label>
                        <input type="number" name="publication_year" value="{{ old('publication_year') }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"
                               placeholder="2024">
                        @error('publication_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB. Allowed: JPG, PNG, JPEG</p>
                    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-device-floppy"></i> Save Book
                </button>
                <a href="{{ route('bookshop.books.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection