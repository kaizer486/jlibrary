@extends('layouts.author')

@section('title', 'Add New Listing')
@section('page-title', 'Add New Listing')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-shopping-bag-plus"></i> Add New Listing
            </h1>
            <p class="text-purple-100 text-sm mt-1">List a book for sale in the marketplace</p>
        </div>

        <form action="{{ route('marketplace.store') }}" method="POST" enctype="multipart/form-data" class="p-6"
              x-data="{ format: '{{ old('book_type', 'softcopy') }}' }">
            @csrf

            <!-- Basic Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required value="{{ old('title') }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                    <select name="category_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500"
                          placeholder="Describe your book...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Format & Pricing -->
            <div class="mt-8 pt-6 border-t-2 border-purple-100">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-currency-dollar text-purple-600"></i>
                    Book Format &amp; Pricing
                </h3>

                <div class="grid md:grid-cols-3 gap-4">
                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'softcopy' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="book_type" value="softcopy" x-model="format" class="sr-only">
                        <div class="text-center">
                            <div class="text-3xl mb-1"></div>
                            <p class="font-semibold text-gray-800">Softcopy</p>
                            <p class="text-xs text-gray-500 mt-0.5">Digital PDF</p>
                        </div>
                    </label>

                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'hardcopy' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="book_type" value="hardcopy" x-model="format" class="sr-only">
                        <div class="text-center">
                            <div class="text-3xl mb-1"></div>
                            <p class="font-semibold text-gray-800">Hardcopy</p>
                            <p class="text-xs text-gray-500 mt-0.5">Physical book</p>
                        </div>
                    </label>

                    <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                           :class="format === 'both' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300'">
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
                    <!-- Softcopy Price -->
                    <div x-show="format === 'softcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📱 Softcopy Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">TSh</span>
                            <input type="number" name="softcopy_price" step="0.01" min="0"
                                   id="price-input"
                                   value="{{ old('softcopy_price') }}"
                                   class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        </div>
                        @error('softcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Hardcopy Price -->
                    <div x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📦 Hardcopy Price <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">TSh</span>
                            <input type="number" name="hardcopy_price" step="0.01" min="0"
                                   value="{{ old('hardcopy_price') }}"
                                   class="w-full pl-16 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        </div>
                        @error('hardcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Stock -->
                    <div x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            📦 Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock" min="0"
                               value="{{ old('stock', 0) }}"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-400 mt-1">Number of physical copies available</p>
                        @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Files -->
            <div class="mt-8 pt-6 border-t-2 border-purple-100 grid md:grid-cols-2 gap-6">
                <div x-show="format === 'softcopy' || format === 'both'" x-cloak>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Book File (PDF) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="book_file" accept=".pdf,.epub,.mobi"
                           :required="format === 'softcopy' || format === 'both'"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-xs text-gray-400 mt-1">PDF, EPUB, MOBI (Max 50MB)</p>
                    @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="format === 'hardcopy'" x-cloak class="md:col-span-2">
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 flex items-start gap-2">
                        <i class="ti ti-info-circle text-purple-600 mt-0.5"></i>
                        <p class="text-xs text-purple-800">
                            <strong>Hardcopy-only listing:</strong> no PDF file required. You can add one later if you switch to "Both".
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    <p class="text-xs text-gray-400 mt-1">Max 5MB. JPG or PNG.</p>
                    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Commission Info -->
            <div class="mt-5 bg-purple-50 rounded-xl p-4">
                <p class="text-sm text-purple-700 flex items-center gap-1">
                    <i class="ti ti-info-circle"></i>
                    Platform commission: <strong>20%</strong>. You keep <strong>80%</strong>.
                </p>
                <div class="mt-2 flex gap-4 text-sm flex-wrap">
                    <span>Commission: <span id="commission-display" class="font-semibold">TSh 0.00</span></span>
                    <span>Your Earnings: <span id="earnings-display" class="font-semibold text-green-600">TSh 0.00</span></span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Publish Listing
                </button>
                <a href="{{ route('seller.listings') }}"
                   class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const priceInput = document.getElementById('price-input');
        if (priceInput) {
            priceInput.addEventListener('input', function () {
                const price = parseFloat(this.value) || 0;
                document.getElementById('commission-display').textContent = 'TSh ' + (price * 0.20).toFixed(2);
                document.getElementById('earnings-display').textContent = 'TSh ' + (price * 0.80).toFixed(2);
            });
        }
    });
</script>
@endpush
@endsection