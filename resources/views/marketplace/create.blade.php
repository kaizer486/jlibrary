@extends('layouts.app')

@section('title', 'Add New Book')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-6">
            <i class="ti ti-plus text-2xl text-purple-600"></i>
            <h1 class="text-2xl font-bold text-gray-800">Add New Book</h1>
        </div>
        
        <form action="{{ route('marketplace.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <!-- Book Title -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Book Title *</label>
                    <input type="text" name="title" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Enter book title" value="{{ old('title') }}">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Author -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Author *</label>
                    <input type="text" name="author" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Book author name" value="{{ old('author') }}">
                    @error('author')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="5" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Describe your book...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Price -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Price (TSh) *</label>
                    <input type="number" name="price" required step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="0.00" value="{{ old('price') }}">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Book Type -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Book Type *</label>
                    <select name="book_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <option value="digital" {{ old('book_type') == 'digital' ? 'selected' : '' }}>📱 Digital (E-book)</option>
                        <option value="physical" {{ old('book_type') == 'physical' ? 'selected' : '' }}>📖 Physical Book</option>
                        <option value="both" {{ old('book_type') == 'both' ? 'selected' : '' }}>📱📖 Both Digital & Physical</option>
                    </select>
                    @error('book_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- File Upload (Digital) -->
                <div id="file-upload">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-book File</label>
                    <input type="file" name="file" accept=".pdf,.epub,.mobi"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-400 mt-1">Supported: PDF, EPUB, MOBI (Max 50MB)</p>
                    @error('file')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Stock (Physical) -->
                <div id="stock-upload" class="hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Stock Quantity</label>
                    <input type="number" name="stock" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="Number of copies available" value="{{ old('stock') }}">
                    @error('stock')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cover Image</label>
                    <input type="file" name="cover_image" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-400 mt-1">Recommended: Square image (Max 5MB)</p>
                    @error('cover_image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
            
                
                <!-- Buttons -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                        <i class="ti ti-plus"></i> Publish Book
                    </button>
                    <a href="{{ route('seller.dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Calculate commission on price change
    document.querySelector('[name="price"]').addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        const commission = price * 0.20;
        const earnings = price * 0.80;
        
        document.getElementById('commission-display').textContent = 'TSh ' + commission.toFixed(2);
        document.getElementById('earnings-display').textContent = 'TSh ' + earnings.toFixed(2);
    });

    // Show/hide file or stock based on book type
    document.querySelector('[name="book_type"]').addEventListener('change', function() {
        const type = this.value;
        const fileUpload = document.getElementById('file-upload');
        const stockUpload = document.getElementById('stock-upload');
        
        if (type === 'digital' || type === 'both') {
            fileUpload.classList.remove('hidden');
        } else {
            fileUpload.classList.add('hidden');
        }
        
        if (type === 'physical' || type === 'both') {
            stockUpload.classList.remove('hidden');
        } else {
            stockUpload.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection