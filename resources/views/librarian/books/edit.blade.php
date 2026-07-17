@extends('layouts.librarian')

@section('title', 'Edit Book')
@section('page-title', '📝 Edit Book')

@section('content')

<div class="max-w-4xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('librarian.books.index') }}" class="text-purple-400 hover:text-purple-300 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-edit"></i> Edit Book
            </h1>
            <p class="text-slate-400 text-sm">Update book details</p>
        </div>

        <form method="POST" action="{{ route('librarian.books.update', $book) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                               class="search-bar">
                        @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Author</label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}"
                               class="search-bar">
                        @error('author') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Category</label>
                        <input type="text" name="category" value="{{ old('category', $book->category) }}"
                               placeholder="e.g., Fiction, Science, History"
                               class="search-bar">
                        @error('category') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Status <span class="text-red-400">*</span>
                        </label>
                        <select name="status" class="search-bar">
                            <option value="pending" {{ old('status', $book->status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="approved" {{ old('status', $book->status) == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                            <option value="rejected" {{ old('status', $book->status) == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                        </select>
                        @error('status') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Description</label>
                    <textarea name="description" rows="4" 
                              class="search-bar">{{ old('description', $book->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Shelf Location -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-map-pin text-purple-400"></i> Shelf Location
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Shelf</label>
                            <select name="shelf_number" class="search-bar">
                                <option value="">Select Shelf</option>
                                @foreach($shelves ?? [] as $shelf)
                                    <option value="{{ $shelf->code }}" {{ old('shelf_number', $book->shelf_number) == $shelf->code ? 'selected' : '' }}>
                                        {{ $shelf->code }} - {{ $shelf->name }} ({{ $shelf->current_count }}/{{ $shelf->capacity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('shelf_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Shelf Name</label>
                            <input type="text" name="shelf_name" value="{{ old('shelf_name', $book->shelf_name) }}"
                                   class="search-bar">
                            @error('shelf_name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Column</label>
                            <input type="text" name="column_location" value="{{ old('column_location', $book->column_location) }}"
                                   class="search-bar">
                            @error('column_location') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Position</label>
                            <input type="text" name="position" value="{{ old('position', $book->position) }}"
                                   class="search-bar">
                            @error('position') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Floor</label>
                            <input type="text" name="floor" value="{{ old('floor', $book->floor) }}"
                                   placeholder="e.g., Ground, 1st, 2nd"
                                   class="search-bar">
                            @error('floor') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Section</label>
                            <input type="text" name="section" value="{{ old('section', $book->section) }}"
                                   placeholder="e.g., Fiction, Non-Fiction"
                                   class="search-bar">
                            @error('section') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-coin text-purple-400"></i> Pricing
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Is Paid?</label>
                            <select name="is_paid" class="search-bar">
                                <option value="0" {{ old('is_paid', $book->is_paid) == '0' ? 'selected' : '' }}>🆓 Free</option>
                                <option value="1" {{ old('is_paid', $book->is_paid) == '1' ? 'selected' : '' }}>💰 Paid</option>
                            </select>
                            @error('is_paid') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Price (if paid)</label>
                            <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}"
                                   placeholder="0.00"
                                   class="search-bar">
                            @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Files -->
                <div class="border-t border-slate-700 pt-6">
                    <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="ti ti-file text-purple-400"></i> Files
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Cover Image</label>
                            @if($book->cover_image)
                                <div class="mb-2">
                                   <img src="{{ url('media/' . $book->cover_image) }}" alt="Current cover" class="w-20 h-28 object-cover rounded-lg">
<p class="text-xs text-slate-500 mt-1">Current cover</p>
                                </div>
                            @endif
                            <input type="file" name="cover_image" accept="image/*"
                                   class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-500/20 file:text-purple-300 hover:file:bg-purple-500/30 text-slate-300">
                            <p class="text-xs text-slate-500 mt-1">Max 2MB. Leave empty to keep current</p>
                            @error('cover_image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">PDF File</label>
                            @if($book->file_path)
                                <p class="text-xs text-emerald-400 mb-1">✅ Current PDF uploaded</p>
                            @endif
                            <input type="file" name="file" accept=".pdf"
                                   class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-500/20 file:text-purple-300 hover:file:bg-purple-500/30 text-slate-300">
                            <p class="text-xs text-slate-500 mt-1">Max 10MB. Leave empty to keep current</p>
                            @error('file') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Total Pages</label>
                            <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}"
                                   class="search-bar">
                            @error('total_pages') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex gap-3 pt-6 border-t border-slate-700">
                    <button type="submit" class="btn-library flex-1 justify-center">
                        <i class="ti ti-device-floppy"></i> Update Book
                    </button>
                    <a href="{{ route('librarian.books.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition text-center border border-slate-700">
                        Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>

</div>

@endsection