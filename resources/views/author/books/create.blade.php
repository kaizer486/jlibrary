@extends('layouts.author')

@section('title', 'Add New Book')
@section('page-title', 'Add New Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('author.books.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to My Books
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-book-plus"></i> Add New Book
            </h1>
            <p class="text-orange-100 text-sm mt-1">Add a new book to your collection</p>
        </div>

        <form method="POST" action="{{ route('author.books.store') }}" enctype="multipart/form-data" class="p-6"
              x-data="{ format: '{{ old('book_type', 'softcopy') }}' }">
            @csrf

            <!-- Row 1: Basic Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Author <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="author" value="{{ old('author', auth()->user()->full_name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
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
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-orange-500 appearance-none"
                                style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                            <option value="">-- Select Category --</option>
                            @foreach($categories ?? [] as $category => $subs)
                                <option value="{{ $category }}" {{ old('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sub-Category</label>
                    <div class="relative">
                        <select name="sub_category" id="sub-category-select"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-orange-500 appearance-none"
                                style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"%236b7280\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><polyline points=\"6 9 12 15 18 9\"/></svg>'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.5em 1.5em; padding-right: 3rem;">
                            <option value="">-- Select Sub-Category --</option>
                        </select>
                    </div>
                    @error('sub_category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- ========================================== -->
            <!-- NEW: Book Format & Pricing                 -->
            <!-- ========================================== -->
            <div class="mt-8 pt-6 border-t-2 border-orange-100">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-currency-dollar text-orange-600"></i>
                    Book Format &amp; Pricing
                </h3>

                <div class="grid md:grid-cols-3 gap-4">
                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'softcopy' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="book_type" value="softcopy" x-model="format" class="sr-only">
                        <div class="text-center">
                            <div class="text-3xl mb-1"></div>
                            <p class="font-semibold text-gray-800">Softcopy</p>
                            <p class="text-xs text-gray-500 mt-0.5">Digital PDF</p>
                        </div>
                    </label>

                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'hardcopy' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="book_type" value="hardcopy" x-model="format" class="sr-only">
                        <div class="text-center">
                            <div class="text-3xl mb-1"></div>
                            <p class="font-semibold text-gray-800">Hardcopy</p>
                            <p class="text-xs text-gray-500 mt-0.5">Physical book</p>
                        </div>
                    </label>

                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'both' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="book_type" value="both" x-model="format" class="sr-only">
                        <div class="text-center">
                            <div class="text-3xl mb-1"></div>
                            <p class="font-semibold text-gray-800">Both</p>
                            <p class="text-xs text-gray-500 mt-0.5">Digital + Physical</p>
                        </div>
                    </label>
                </div>
                @error('book_type') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

                <div class="grid md:grid-cols-2 gap-6 mt-5">
                    <!-- Softcopy price -->
                    <div x-show="format === 'softcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📱 Softcopy Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TSh</span>
                            <input type="number" name="softcopy_price" step="0.01" min="0"
                                   value="{{ old('softcopy_price') }}"
                                   class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Set 0 for free digital download</p>
                        @error('softcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Hardcopy price -->
                    <div x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📦 Hardcopy Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">TSh</span>
                            <input type="number" name="hardcopy_price" step="0.01" min="0"
                                   value="{{ old('hardcopy_price') }}"
                                   class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                        </div>
                        @error('hardcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Stock quantity -->
                    <div x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📦 Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" min="0"
                               value="{{ old('stock_quantity', 0) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                        <p class="text-xs text-gray-400 mt-1">Number of physical copies available</p>
                        @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Row 4: Pages & Published Date -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Total Pages</label>
                    <input type="number" name="total_pages" value="{{ old('total_pages', 0) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Published Date</label>
                    <input type="date" name="published_date" value="{{ old('published_date') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Row 6: Files -->
            <div class="grid md:grid-cols-2 gap-6 mt-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book File (PDF) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="book_file" accept=".pdf" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-gray-400 mt-1">Max 20MB. Only PDF files allowed.</p>
                    @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Image (Optional)</label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <p class="text-xs text-gray-400 mt-1">Max 2MB. JPG, PNG only. Recommended: 500x700px</p>
                    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Row 7: Description -->
            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="5"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Add Book
                </button>
                <a href="{{ route('author.books.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    // Dynamic sub-category population
    @if(isset($categories))
    const categories = @json($categories);
    const categorySelect = document.getElementById('category-select');
    const subCategorySelect = document.getElementById('sub-category-select');
    const oldSubCategory = @json(old('sub_category'));

    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;
            const subCategories = categories[selectedCategory] || [];

            subCategorySelect.innerHTML = '<option value="">-- Select Sub-Category --</option>';

            subCategories.forEach(function(sub) {
                const option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                if (sub === oldSubCategory) option.selected = true;
                subCategorySelect.appendChild(option);
            });
        });

        if (categorySelect.value) {
            categorySelect.dispatchEvent(new Event('change'));
        }
    }
    @endif
</script>
@endsection