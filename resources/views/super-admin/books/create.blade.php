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

            <!-- Row 1: Basic Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
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
            </div>

           <!-- Row 2: Category & Sub-category -->
<div class="grid md:grid-cols-2 gap-6 mt-5">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
            Category <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <select name="category" id="category-select" 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500 appearance-none"
                    style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                <option value="">-- Select Category --</option>
                @foreach($categories as $category => $subs)
                    <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
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
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500 appearance-none"
                    style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                <option value="">-- Select Sub-Category --</option>
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
        <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500">
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
            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">TSh</span>
            <input type="number" name="price" step="0.01" value="{{ old('price', 0) }}" 
                   class="w-full pl-14 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
        </div>
    </div>
</div>

            <!-- Row 4: Pages & Published Date -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Total Pages
                    </label>
                    <input type="number" name="total_pages" value="{{ old('total_pages', 0) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Published Date
                    </label>
                    <input type="date" name="published_date" value="{{ old('published_date') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                </div>
            </div>

            <!-- Row 5: Checkboxes -->
            <div class="grid md:grid-cols-3 gap-4 mt-5">
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl">
                    <input type="checkbox" name="is_paid" value="1" {{ old('is_paid') ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded">
                    <span class="text-sm text-gray-700">💰 Paid Book</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 text-yellow-600 rounded">
                    <span class="text-sm text-gray-700">⭐ Featured</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl">
                    <input type="checkbox" name="is_trending" value="1" {{ old('is_trending') ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded">
                    <span class="text-sm text-gray-700">🔥 Trending</span>
                </label>
            </div>

            <!-- Row 6: Files -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
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
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB. JPG, PNG only. Recommended: 500x700px</p>
                    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Row 7: Description - Full Width -->
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

<script>
    // Fix dropdown positioning - ensures dropdown opens downward
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('select');
        selects.forEach(function(select) {
            select.addEventListener('mousedown', function(e) {
                // Force dropdown to open downward
                this.style.transform = 'translateY(0)';
            });
        });
    });

    // Dynamic sub-category population
    const categories = @json($categories);
    const categorySelect = document.getElementById('category-select');
    const subCategorySelect = document.getElementById('sub-category-select');
    
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const subCategories = categories[selectedCategory] || [];
            
            // Clear sub-category select
            subCategorySelect.innerHTML = '<option value="">-- Select Sub-Category --</option>';
            
            // Add sub-category options
            subCategories.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                subCategorySelect.appendChild(option);
            });
            
            // Force dropdown to show downward
            subCategorySelect.style.transform = 'translateY(0)';
        });
        
        // Trigger change if category is pre-selected
        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }
    }
</script>
@endsection