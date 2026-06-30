@extends('layouts.admin')



@section('title', 'Edit Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-jlibrary-600">Dashboard</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <a href="{{ route('admin.books.index') }}" class="hover:text-jlibrary-600">Books</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <span class="text-gray-900">Edit: {{ $book->title }}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✏️ Edit Book</h1>
                <p class="text-gray-600 mt-1">Update book information, cover image, and details.</p>
            </div>
            <a href="{{ route('admin.books.show', $book) }}" class="text-jlibrary-600 hover:text-jlibrary-700 flex items-center gap-1">
                <i class="ti ti-eye"></i> View Book
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Cover Image Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-4 py-3">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-photo"></i> Book Cover
                    </h3>
                </div>
                <div class="p-6 flex justify-center">
                    <div class="relative group">
                        @if($book->cover_image)
                            <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-48 rounded-xl shadow-lg">
                        @else
                            <div class="w-48 h-56 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center">
                                <i class="ti ti-books text-5xl text-gray-500"></i>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="px-6 pb-6">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($book->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm overflow-hidden">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-5">
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Book Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Author <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="author" value="{{ old('author', $book->author) }}" required 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
                            @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Price
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" 
                                       class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Total Pages
                            </label>
                            <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="is_paid" value="1" {{ $book->is_paid ? 'checked' : '' }} class="w-4 h-4 text-jlibrary-600 rounded">
                            <span class="text-sm text-gray-700">This is a paid book</span>
                        </label>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Cover Image (Optional)
                        </label>
                        <input type="file" name="cover_image" accept="image/*" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-jlibrary-50 file:text-jlibrary-700">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep current cover. Max 2MB (JPG, PNG)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500">{{ old('description', $book->description) }}</textarea>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex gap-3 p-6 bg-gray-50 border-t">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-jlibrary-600 to-jlibrary-700 text-white px-6 py-2.5 rounded-lg hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i> Update Book
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-100 transition text-center">
                        Cancel
                    </a>
                </div>
            </form>
            
            <!-- Danger Zone -->
            <div class="mt-6 bg-red-50 rounded-xl border border-red-200 overflow-hidden">
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
                    <button onclick="deleteBook()" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
                        <i class="ti ti-trash"></i> Delete Book
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteBook() {
    if (confirm('Are you sure you want to delete "{{ $book->title }}" permanently?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.books.destroy", $book) }}';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection