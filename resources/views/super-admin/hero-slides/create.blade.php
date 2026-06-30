@extends('layouts.super-admin')

@section('title', 'Create Hero Slide')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('super-admin.hero-slides.index') }}" class="text-gray-600 hover:text-gray-800 transition">
            <i class="ti ti-arrow-left text-2xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create Hero Slide</h1>
            <p class="text-gray-500 text-sm mt-1">Add a new slide with glassmorphism design</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 max-w-4xl">
        <form action="{{ route('super-admin.hero-slides.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div>
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition" required>
                        @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Subtitle -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <textarea name="subtitle" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('subtitle') }}</textarea>
                        @error('subtitle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Badge Text -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Badge Text</label>
                        <input type="text" name="badge_text" value="{{ old('badge_text', 'Welcome to JLIBRARY') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        <p class="text-xs text-gray-400 mt-1">Small badge above the title</p>
                    </div>

                    <!-- Image -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" accept="image/*" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition" required>
                        <p class="text-xs text-gray-400 mt-1">Recommended: 1920x1080px. Max 2MB</p>
                        @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        <div id="image-preview" class="mt-3 hidden">
                            <img src="#" alt="Preview" class="max-h-48 rounded-lg border">
                        </div>
                    </div>

                    <!-- Slide Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slide Type <span class="text-red-500">*</span></label>
                        <select name="slide_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                            @foreach($slideTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('slide_type', 'dashboard') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('slide_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- CTA -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA Text</label>
                            <input type="text" name="cta_text" value="{{ old('cta_text', 'Get Started Free') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CTA URL</label>
                            <input type="text" name="cta_url" value="{{ old('cta_url', '/register') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                   
                    <!-- Settings -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (seconds)</label>
                            <input type="number" name="slide_duration" value="{{ old('slide_duration', 5) }}" min="2" max="30"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                            <input type="number" name="order" value="{{ old('order', $slideCount + 1) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-200">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    Create Slide
                </button>
                <a href="{{ route('super-admin.hero-slides.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image preview
        const imageInput = document.querySelector('input[name="image"]');
        const preview = document.getElementById('image-preview');
        const previewImg = preview.querySelector('img');

        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Add stat
        document.getElementById('add-stat').addEventListener('click', function() {
            const container = document.getElementById('stats-container');
            const index = container.children.length;
            const html = `
                <div class="stat-item border border-gray-200 rounded-lg p-3 mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-xs text-gray-500">Icon</label>
                                <input type="text" name="stats[${index}][icon]" value="books" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 outline-none transition" placeholder="books">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Number</label>
                                <input type="text" name="stats[${index}][number]" value="" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 outline-none transition" placeholder="12K+">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500">Label</label>
                                <input type="text" name="stats[${index}][label]" value="" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-purple-500 outline-none transition" placeholder="Books Available">
                            </div>
                        </div>
                        <button type="button" class="remove-stat text-red-400 hover:text-red-600 text-xl">&times;</button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });

        // Remove stat
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-stat')) {
                const container = document.getElementById('stats-container');
                if (container.children.length > 1) {
                    e.target.closest('.stat-item').remove();
                } else {
                    alert('You need at least one stat card');
                }
            }
        });
    });
</script>
@endpush
@endsection