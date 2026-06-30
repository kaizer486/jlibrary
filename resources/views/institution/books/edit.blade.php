@extends('layouts.librarian')

@section('title', 'Edit Book')
@section('page-title', '✏️ Edit Book: ' . $book->title)

@section('content')

<div class="max-w-3xl mx-auto">
    
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('institution.books.index') }}" class="text-slate-400 hover:text-slate-300 transition inline-flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="ti ti-edit text-purple-400"></i>
                Edit Book Details
            </h3>
        </div>
        
        <form method="POST" action="{{ route('institution.books.update', $book) }}" 
              enctype="multipart/form-data" 
              class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                
                <!-- Title -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Title <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                           class="search-bar" placeholder="Book title">
                    @error('title')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Author -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Author <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="author" value="{{ old('author', $book->author) }}" required
                           class="search-bar" placeholder="Author name">
                    @error('author')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Category <span class="text-red-400">*</span>
                    </label>
                    <select name="category" class="search-bar">
                        <option value="">Select Category</option>
                        <option value="fiction" {{ old('category', $book->category) == 'fiction' ? 'selected' : '' }}>📖 Fiction</option>
                        <option value="non-fiction" {{ old('category', $book->category) == 'non-fiction' ? 'selected' : '' }}>📚 Non-Fiction</option>
                        <option value="science" {{ old('category', $book->category) == 'science' ? 'selected' : '' }}>🔬 Science</option>
                        <option value="history" {{ old('category', $book->category) == 'history' ? 'selected' : '' }}>🏛️ History</option>
                        <option value="technology" {{ old('category', $book->category) == 'technology' ? 'selected' : '' }}>💻 Technology</option>
                        <option value="education" {{ old('category', $book->category) == 'education' ? 'selected' : '' }}>🎓 Education</option>
                        <option value="biography" {{ old('category', $book->category) == 'biography' ? 'selected' : '' }}>👤 Biography</option>
                        <option value="self-help" {{ old('category', $book->category) == 'self-help' ? 'selected' : '' }}>💪 Self-Help</option>
                        <option value="business" {{ old('category', $book->category) == 'business' ? 'selected' : '' }}>💼 Business</option>
                        <option value="other" {{ old('category', $book->category) == 'other' ? 'selected' : '' }}>📌 Other</option>
                    </select>
                    @error('category')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="4" class="search-bar resize-y" 
                              placeholder="Book description">{{ old('description', $book->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Cover Image
                    </label>
                    @if($book->cover_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $book->cover_image) }}" 
                                 alt="{{ $book->title }}" 
                                 class="w-32 h-40 object-cover rounded-lg border border-slate-700">
                            <p class="text-xs text-slate-500 mt-1">Current cover</p>
                        </div>
                    @endif
                    <input type="file" name="cover_image" accept="image/*"
                           class="search-bar">
                    <p class="text-xs text-slate-500 mt-1">Leave empty to keep current image</p>
                    @error('cover_image')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Shelf Location -->
                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                    <h4 class="text-sm font-semibold text-slate-300 mb-3 flex items-center gap-2">
                        <i class="ti ti-map-pin text-purple-400"></i> Shelf Location
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Shelf Number -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Shelf Number
                            </label>
                            <input type="text" name="shelf_number" 
                                   value="{{ old('shelf_number', $book->shelf_number) }}"
                                   class="search-bar" placeholder="A-01">
                        </div>
                        
                        <!-- Shelf Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Shelf Name
                            </label>
                            <input type="text" name="shelf_name" 
                                   value="{{ old('shelf_name', $book->shelf_name) }}"
                                   class="search-bar" placeholder="History">
                        </div>
                        
                        <!-- Floor -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Floor
                            </label>
                            <input type="text" name="floor" 
                                   value="{{ old('floor', $book->floor) }}"
                                   class="search-bar" placeholder="Ground">
                        </div>
                        
                        <!-- Section -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Section
                            </label>
                            <input type="text" name="section" 
                                   value="{{ old('section', $book->section) }}"
                                   class="search-bar" placeholder="Non-Fiction">
                        </div>
                        
                        <!-- Column -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Column
                            </label>
                            <input type="text" name="column_location" 
                                   value="{{ old('column_location', $book->column_location) }}"
                                   class="search-bar" placeholder="C-03">
                        </div>
                        
                        <!-- Position -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-400 mb-1">
                                Position
                            </label>
                            <input type="text" name="position" 
                                   value="{{ old('position', $book->position) }}"
                                   class="search-bar" placeholder="Row 5">
                        </div>
                    </div>
                </div>
                
                <!-- Price & Pages -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Total Pages -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Total Pages
                        </label>
                        <input type="number" name="total_pages" 
                               value="{{ old('total_pages', $book->total_pages) }}"
                               class="search-bar" placeholder="100">
                        @error('total_pages')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Price -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">
                            Price (TSh)
                        </label>
                        <input type="number" name="price" step="0.01" 
                               value="{{ old('price', $book->price) }}"
                               class="search-bar" placeholder="0.00">
                        <p class="text-xs text-slate-500 mt-1">Set to 0.00 for free</p>
                        @error('price')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Is Paid -->
                <div>
                    <label class="flex items-center gap-3 text-sm text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_paid" value="1"
                               class="rounded border-slate-600 bg-slate-800 text-purple-600 focus:ring-purple-500"
                               {{ old('is_paid', $book->is_paid) ? 'checked' : '' }}>
                        <span>This is a paid book</span>
                    </label>
                    @error('is_paid')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Status
                    </label>
                    <select name="status" class="search-bar">
                        <option value="pending" {{ old('status', $book->status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="approved" {{ old('status', $book->status) == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="rejected" {{ old('status', $book->status) == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
                    @error('status')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex flex-wrap gap-3 mt-8 pt-4 border-t border-slate-700">
                <button type="submit" class="btn-library flex-1 justify-center">
                    <i class="ti ti-device-floppy"></i> Update Book
                </button>
                <a href="{{ route('institution.books.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>

@endsection