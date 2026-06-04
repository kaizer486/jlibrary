@extends('layouts.super-admin')

@section('title', 'Edit Book')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.books.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
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

        <form method="POST" action="{{ route('super-admin.books.update', $book) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <!-- Current Cover Preview -->
            <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                <p class="text-sm font-semibold text-gray-700 mb-3">Current Cover</p>
                <div class="flex items-center gap-4">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-24 h-32 object-cover rounded-lg shadow">
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

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Book Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $book->title) }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Author <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="author" value="{{ old('author', $book->author) }}" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                        @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Select Institution
                        </label>
                        <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                            <option value="">-- Select Institution --</option>
                            @foreach($institutions as $inst)
                                <option value="{{ $inst->id }}" {{ old('institution_id', $book->institution_id) == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->name }} ({{ $inst->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Price
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                            <input type="number" name="price" step="0.01" value="{{ old('price', $book->price) }}" 
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer p-3 bg-gray-50 rounded-xl">
                            <input type="checkbox" name="is_paid" value="1" {{ $book->is_paid ? 'checked' : '' }} class="w-4 h-4 text-purple-600 rounded">
                            <span class="text-sm text-gray-700">This is a paid book</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Total Pages
                        </label>
                        <input type="number" name="total_pages" value="{{ old('total_pages', $book->total_pages) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                            <option value="approved" {{ $book->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                            <option value="pending" {{ $book->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="rejected" {{ $book->status == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            New Cover Image (Optional)
                        </label>
                        <input type="file" name="cover_image" accept="image/jpeg,image/png,image/jpg" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to keep current cover. Max 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Description - Full Width -->
            <div class="mt-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" rows="5" 
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-xl">{{ old('description', $book->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Update Book
                </button>
                <a href="{{ route('super-admin.books.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
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
            <form method="POST" action="{{ route('super-admin.books.destroy', $book) }}" onsubmit="return confirm('Delete " . addslashes($book->title) . " permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-trash"></i> Delete Book
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
