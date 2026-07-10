@extends('layouts.app')

@section('title', 'Upload New Book')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('author.books.index') }}" class="text-slate-600 hover:text-slate-800 flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> Back to My Books
            </a>
        </div>

        <!-- ========================================== -->
        <!-- MAIN FORM CARD                             -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-md border-2 border-slate-200/80 overflow-hidden">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-600 to-amber-600 px-6 py-4 border-b-2 border-orange-400/30">
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ti ti-book-plus"></i> Upload New Book
                </h1>
                <p class="text-orange-100 text-sm mt-1">Share your knowledge with the world</p>
            </div>

            <form method="POST" action="{{ route('author.books.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Book Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" required 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Author Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="author" value="{{ old('author', auth()->user()->full_name) }}" required 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400">
                            @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Price
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">TSh</span>
                                <input type="number" name="price" step="0.01" value="{{ old('price', 0) }}" 
                                       class="w-full pl-16 pr-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-orange-50/60 rounded-xl border-2 border-orange-100/60">
                                <input type="checkbox" name="is_paid" value="1" {{ old('is_paid') ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                                <span class="text-sm text-slate-700 font-medium">This is a paid book</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Total Pages
                            </label>
                            <input type="number" name="total_pages" value="{{ old('total_pages', 0) }}" 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Book File (PDF) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="book_file" accept=".pdf" required 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                            <p class="text-xs text-slate-400 mt-1">Max 20MB. Only PDF files allowed.</p>
                            @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Cover Image (Optional)
                            </label>
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                            <p class="text-xs text-slate-400 mt-1">Max 2MB. JPG, PNG only. Recommended: 500x700px</p>
                            @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="5" 
                              class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400 resize-y">{{ old('description') }}</textarea>
                </div>

                <!-- Notice -->
                <div class="mt-4 p-4 bg-amber-50 rounded-xl border-2 border-amber-200/80 shadow-sm">
                    <div class="flex items-start gap-3">
                        <i class="ti ti-info-circle text-amber-600 text-lg mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-amber-800">
                                📚 Important Information
                            </p>
                            <p class="text-xs text-amber-700 mt-1">
                                Books will be reviewed by admin before appearing in the marketplace. You will earn <strong>10% royalty</strong> on each sale.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-3 mt-8 pt-6 border-t-2 border-slate-200/60">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-xl hover:shadow-lg hover:shadow-orange-600/25 transition font-semibold flex items-center justify-center gap-2 border-2 border-orange-400/30">
                        <i class="ti ti-device-floppy"></i> Upload Book
                    </button>
                    <a href="{{ route('author.books.index') }}" class="px-6 py-3 bg-white border-2 border-slate-200/80 rounded-xl hover:bg-slate-50 transition text-center font-medium text-slate-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection