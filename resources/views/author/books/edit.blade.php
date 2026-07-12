@extends('layouts.app')

@section('title', 'Edit Book')

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
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-20 h-28 object-cover rounded-lg shadow">
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

            <form method="POST" action="{{ route('author.books.update', $book) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

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
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Price
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-500 font-medium">TSh</span>
                                <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" 
                                       class="w-full pl-16 pr-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center gap-3 cursor-pointer p-3 bg-orange-50/60 rounded-xl border-2 border-orange-100/60">
                                <input type="checkbox" name="is_paid" value="1" {{ $book->is_paid ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded focus:ring-orange-500">
                                <span class="text-sm text-slate-700 font-medium">This is a paid book</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Total Pages
                            </label>
                            <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}" 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Book File (PDF)
                            </label>
                            <input type="file" name="book_file" accept=".pdf" 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                            <p class="text-xs text-slate-400 mt-1">Max 20MB. Leave empty to keep current file.</p>
                            @if($book->file_path)
                                <p class="text-xs text-emerald-600 mt-1">✅ Current file: {{ basename($book->file_path) }}</p>
                            @endif
                            @error('book_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Cover Image (Optional)
                            </label>
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                                   class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-orange-500 file:text-white hover:file:bg-orange-600 transition text-slate-700">
                            <p class="text-xs text-slate-400 mt-1">Leave empty to keep current cover. Max 2MB</p>
                            @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Status
                            </label>
                            <select name="status" class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                                <option value="pending" {{ $book->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="approved" {{ $book->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                                <option value="rejected" {{ $book->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                            </select>
                            <p class="text-xs text-slate-400 mt-1">Note: Changing to "Approved" will make your book visible to everyone</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-5">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="5" 
                              class="w-full px-4 py-2.5 bg-white border-2 border-slate-200/80 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400 resize-y">{{ old('description', $book->description) }}</textarea>
                </div>

                <!-- Form Actions -->
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
@endsection