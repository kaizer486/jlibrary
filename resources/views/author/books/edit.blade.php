@extends('layouts.author')

@section('title', 'Edit Book')
@section('page-title', 'Edit Book')

@section('content')

<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('author.books.index') }}" class="text-slate-600 hover:text-slate-800 flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to My Books
            </a>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">

            <!-- Header -->
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 px-6 py-4 border-b-2 border-amber-400/30">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-edit"></i> Edit Book
                </h1>
                <p class="text-amber-100 text-sm mt-1">Update your book information</p>
            </div>

            <!-- Current Cover Preview -->
            <div class="p-6 bg-slate-50/80 border-b-2 border-slate-200/60">
                <p class="text-sm font-semibold text-slate-700 mb-3">Current Cover</p>
                <div class="flex items-center gap-4">
                    @if($book->cover_image)
                        <img src="{{ url('media/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-20 h-28 object-cover rounded-lg shadow">
                    @else
                        <div class="w-20 h-28 bg-slate-200 rounded-lg flex items-center justify-center">
                            <i class="ti ti-book text-slate-400 text-3xl"></i>
                        </div>
                    @endif
                    <div class="text-sm text-slate-500">
                        <p>Current cover image</p>
                        <p class="text-xs">Upload a new image below to replace it</p>
                    </div>
                </div>
            </div>

            <!-- Current Stock Status (hardcopy / both) -->
            @if(in_array($book->book_type, ['hardcopy', 'both'], true))
                @php $stockNow = (int) ($book->stock_quantity ?? 0); @endphp
                <div class="px-6 py-4 border-b-2 border-slate-200/60 {{ $stockNow > 0 ? 'bg-emerald-50/70' : 'bg-red-50/70' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $stockNow > 0 ? 'bg-emerald-500' : 'bg-red-500' }} text-white">
                                <i class="ti {{ $stockNow > 0 ? 'ti-package' : 'ti-package-off' }} text-xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-sm {{ $stockNow > 0 ? 'text-emerald-800' : 'text-red-800' }}">
                                    @if($stockNow > 0)
                                        In Stock — {{ $stockNow }} {{ Str::plural('copy', $stockNow) }} available
                                    @else
                                        Out of Stock
                                    @endif
                                </p>
                                <p class="text-xs {{ $stockNow > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    @if($stockNow > 0)
                                        Buyers can order this book right now.
                                    @else
                                        This book is hidden from the market until you add stock below.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('author.books.update', $book) }}" enctype="multipart/form-data" class="p-6"
                  x-data="{ format: '{{ old('book_type', $book->book_type ?? 'softcopy') }}' }">
                @csrf
                @method('PUT')

                <!-- ========================================== -->
                <!-- BASIC INFO                                 -->
                <!-- ========================================== -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Book Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Author Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Total Pages</label>
                            <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}"
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cover Image (Optional)</label>
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg"
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                            <p class="text-xs text-slate-400 mt-1">Leave empty to keep current cover. Max 2MB</p>
                            @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                                <option value="pending" {{ $book->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="approved" {{ $book->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="rejected" {{ $book->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- BOOK FORMAT & PRICING                      -->
                <!-- ========================================== -->
                <div class="mt-8 pt-6 border-t-2 border-slate-200/60">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i class="ti ti-currency-dollar text-orange-600"></i>
                        Book Format &amp; Pricing
                    </h3>

                    <!-- Format Radios -->
                    <div class="grid md:grid-cols-3 gap-4">
                        <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                               :class="format === 'softcopy' ? 'border-orange-500 bg-orange-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="book_type" value="softcopy" x-model="format" class="sr-only">
                            <div class="text-center">
                                <div class="text-3xl mb-1">📱</div>
                                <p class="font-semibold text-slate-800">Softcopy</p>
                                <p class="text-xs text-slate-500 mt-0.5">Digital PDF</p>
                            </div>
                        </label>

                        <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                               :class="format === 'hardcopy' ? 'border-orange-500 bg-orange-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="book_type" value="hardcopy" x-model="format" class="sr-only">
                            <div class="text-center">
                                <div class="text-3xl mb-1">📦</div>
                                <p class="font-semibold text-slate-800">Hardcopy</p>
                                <p class="text-xs text-slate-500 mt-0.5">Physical book</p>
                            </div>
                        </label>

                        <label class="cursor-pointer p-4 rounded-xl border-2 transition"
                               :class="format === 'both' ? 'border-orange-500 bg-orange-50' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="book_type" value="both" x-model="format" class="sr-only">
                            <div class="text-center">
                                <div class="text-3xl mb-1">📱📦</div>
                                <p class="font-semibold text-slate-800">Both</p>
                                <p class="text-xs text-slate-500 mt-0.5">Digital + Physical</p>
                            </div>
                        </label>
                    </div>
                    @error('book_type') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

                    <!-- Format Fields -->
                    <div class="grid md:grid-cols-2 gap-6 mt-5">

                        <!-- Softcopy Price -->
                        <div x-show="format === 'softcopy' || format === 'both'" x-cloak>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                📱 Softcopy Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">TSh</span>
                                <input type="number" name="softcopy_price" step="0.01" min="0"
                                       value="{{ old('softcopy_price', $book->softcopy_price) }}"
                                       class="w-full pl-16 pr-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Set 0 for free digital download</p>
                            @error('softcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Hardcopy Price -->
                        <div x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                📦 Hardcopy Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">TSh</span>
                                <input type="number" name="hardcopy_price" step="0.01" min="0"
                                       value="{{ old('hardcopy_price', $book->hardcopy_price) }}"
                                       class="w-full pl-16 pr-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            </div>
                            @error('hardcopy_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Available Stock -->
                        <div id="restock" x-show="format === 'hardcopy' || format === 'both'" x-cloak>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                📦 Available Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="stock_quantity" min="0"
                                   value="{{ old('stock_quantity', $book->stock_quantity ?? 0) }}"
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            <p class="text-xs text-slate-400 mt-1">
                                Update this number to <strong>restock</strong> or adjust availability.
                                Set to 0 to hide from market.
                            </p>
                            @error('stock_quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- PDF Upload -->
                    <div x-show="format === 'softcopy' || format === 'both'" x-cloak class="mt-5">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Book File (PDF)</label>
                        <input type="file" name="book_file" accept=".pdf"
                               class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                        <p class="text-xs text-slate-400 mt-1">Max 20MB. Leave empty to keep current file.</p>
                        @if($book->file_path)
                            <p class="text-xs text-emerald-600 mt-1">✅ Current file: {{ basename($book->file_path) }}</p>
                        @endif
                        @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Hardcopy-only info note -->
                    <div x-show="format === 'hardcopy'" x-cloak class="mt-3">
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-3 flex items-start gap-2">
                            <i class="ti ti-info-circle text-orange-600 mt-0.5"></i>
                            <p class="text-xs text-orange-800">
                                <strong>Hardcopy-only book:</strong> no PDF file is required. Switch to <em>Both</em> if you later want to sell a softcopy too.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- DESCRIPTION                                -->
                <!-- ========================================== -->
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>
                    <textarea name="description" rows="5"
                              class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400 resize-y">{{ old('description', $book->description) }}</textarea>
                </div>

                <!-- ========================================== -->
                <!-- FORM ACTIONS                               -->
                <!-- ========================================== -->
                <div class="flex gap-3 mt-8 pt-6 border-t-2 border-slate-200/60">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-semibold flex items-center justify-center gap-2 border-2 border-orange-400/30">
                        <i class="ti ti-device-floppy"></i> Update Book
                    </button>
                    <a href="{{ route('author.books.index') }}" class="px-6 py-3 bg-white border-2 border-slate-200/80 rounded-xl hover:bg-slate-50 transition text-center font-medium text-slate-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="mt-6 bg-red-50 rounded-2xl border-2 border-red-200 overflow-hidden">
            <div class="px-6 py-3 bg-red-100 border-b border-red-200">
                <h3 class="font-semibold text-red-700 flex items-center gap-2">
                    <i class="ti ti-alert-triangle"></i> Danger Zone
                </h3>
            </div>
            <div class="p-6 flex items-center justify-between">
                <div>
                    <p class="font-medium text-slate-800">Delete this book permanently</p>
                    <p class="text-sm text-slate-500">This action cannot be undone. All data will be lost.</p>
                </div>
                <form method="POST" action="{{ route('author.books.destroy', $book) }}" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                        <i class="ti ti-trash"></i> Delete Book
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    input[type="file"] { cursor: pointer; }
    input[type="file"]::file-selector-button { cursor: pointer; }
</style>
@endsection