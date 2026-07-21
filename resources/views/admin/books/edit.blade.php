@extends('layouts.admin')

@section('title', 'Edit Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.books.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-edit"></i> Edit Book
            </h1>
            <p class="text-amber-100 text-sm mt-1">Update book information</p>
        </div>

        <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Current Cover Preview -->
            <div class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-sm font-semibold text-gray-700 mb-3">Current Cover</p>
                <div class="flex items-center gap-4">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-24 h-32 object-cover rounded-lg shadow">
                    @else
                        <div class="w-24 h-32 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="ti ti-book text-gray-400 text-3xl"></i>
                        </div>
                    @endif
                    <div class="text-sm text-gray-500">
                        <p>Current cover image</p>
                        <p class="text-xs">Upload a new image below to replace it</p>
                    </div>
                </div>
            </div>

            <!-- Row 1: Basic Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Author <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Row 2: Category & Sub-category -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="category" id="category-select" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none"
                                style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category => $subs)
                                <option value="{{ $category }}" {{ old('category', $book->category) == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Sub-Category
                    </label>
                    <div class="relative">
                        <select name="sub_category" id="sub-category-select" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 appearance-none"
                                style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                            <option value="">-- Select Sub-Category --</option>
                            @if($book->category && isset($categories[$book->category]))
                                @foreach($categories[$book->category] as $sub)
                                    <option value="{{ $sub }}" {{ old('sub_category', $book->sub_category) == $sub ? 'selected' : '' }}>
                                        {{ $sub }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    @error('sub_category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Row 3: Institution & Price -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Select Institution
                    </label>
                    <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Select Institution --</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id', $book->institution_id) == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }} ({{ $inst->type_label ?? $inst->type ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Price
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TSh</span>
                        <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" 
                               class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
            </div>

            <!-- Row 4: Pages & Published Date -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Total Pages
                    </label>
                    <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Published Date
                    </label>
                    <input type="date" name="published_date" value="{{ old('published_date', $book->published_date ? $book->published_date->format('Y-m-d') : '') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Row 5: Checkboxes & Status -->
            <div class="grid md:grid-cols-4 gap-4 mt-5">
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <input type="checkbox" name="is_paid" value="1" {{ $book->is_paid ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">💰 Paid</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <input type="checkbox" name="is_featured" value="1" {{ $book->is_featured ? 'checked' : '' }} class="w-4 h-4 text-yellow-600 rounded focus:ring-yellow-500">
                    <span class="text-sm text-gray-700">⭐ Featured</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <input type="checkbox" name="is_trending" value="1" {{ $book->is_trending ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                    <span class="text-sm text-gray-700">🔥 Trending</span>
                </label>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Status
                    </label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="approved" {{ $book->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="pending" {{ $book->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="rejected" {{ $book->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
                </div>
            </div>

            <!-- Row 6: Files -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book File (PDF)
                    </label>
                    <input type="file" name="book_file" accept=".pdf" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-1">Max 20MB. Leave empty to keep current file.</p>
                    @if($book->file_path)
                        <p class="text-xs text-green-600 mt-1">✅ Current file: {{ basename($book->file_path) }}</p>
                    @endif
                    @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Cover Image (Optional)
                    </label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep current cover. Max 2MB</p>
                </div>
            </div>

            <!-- Row 7: Description -->
            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" rows="5" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $book->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Update Book
                </button>
                <a href="{{ route('admin.books.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center text-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-6 bg-red-50 rounded-2xl border border-red-200 overflow-hidden">
        <div class="px-6 py-3 bg-red-100 border-b border-red-200">
            <h3 class="font-semibold text-red-700 flex items-center gap-2">
                <i class="ti ti-alert-triangle"></i> Danger Zone
            </h3>
        </div>
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-800">Delete this book permanently</p>
                <p class="text-sm text-gray-500">This action cannot be undone. All data will be lost.</p>
            </div>
            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-trash"></i> Delete Book
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categories = @json($categories);
        const categorySelect = document.getElementById('category-select');
        const subCategorySelect = document.getElementById('sub-category-select');
        
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                const selectedCategory = this.value;
                const subCategories = categories[selectedCategory] || [];
                
                subCategorySelect.innerHTML = '<option value="">-- Select Sub-Category --</option>';
                
                subCategories.forEach(function(sub) {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
                    subCategorySelect.appendChild(option);
                });
            });
            
            if (categorySelect.value) {
                categorySelect.dispatchEvent(new Event('change'));
            }
        }
    });
</script>
@endsection