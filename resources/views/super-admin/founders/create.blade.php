@extends('layouts.super-admin')

@section('title', 'Create Founder')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('super-admin.founders.index') }}" class="text-gray-600 hover:text-gray-800 transition">
            <i class="ti ti-arrow-left text-2xl"></i>
        </a>
        <div>
            <!-- DEPLOYMENT TEST 2026 -->
            <h1 class="text-2xl font-bold text-gray-800">Add Founder</h1>
            <p class="text-gray-500 text-sm mt-1">Add a new founder or leadership team member</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
        <form action="{{ route('super-admin.founders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Name & Title -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" name="title" id="title" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('title', 'Founder & Super Admin') }}">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Bio -->
            <div class="mb-4">
                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                <textarea name="bio" id="bio" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('bio') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Detailed biography or message from the founder</p>
                @error('bio')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Photo -->
            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                <input type="file" name="photo" id="photo" accept="image/*"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                <p class="text-xs text-gray-500 mt-1">Recommended: Square image, 400x400px. Max 2MB. Supported: JPG, PNG, WebP</p>
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <div id="photo-preview" class="mt-3 hidden">
                    <img src="#" alt="Preview" class="w-24 h-24 rounded-full object-cover border-4 border-purple-100">
                </div>
            </div>

            <!-- Email & Phone -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('phone') }}">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Social Links -->
            <div class="mb-4">
                <p class="block text-sm font-medium text-gray-700 mb-2">Social Links</p>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="twitter" class="text-xs text-gray-500">Twitter (X)</label>
                        <input type="text" name="twitter" id="twitter" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('twitter') }}" placeholder="https://x.com/username">
                    </div>
                    <div>
                        <label for="instagram" class="text-xs text-gray-500">Instagram</label>
                        <input type="text" name="instagram" id="instagram" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('instagram') }}" placeholder="https://instagram.com/username">
                    </div>
                    <div>
                        <label for="facebook" class="text-xs text-gray-500">Facebook</label>
                        <input type="text" name="facebook" id="facebook" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('facebook') }}" placeholder="https://facebook.com/username">
                    </div>
                    <div>
                        <label for="tiktok" class="text-xs text-gray-500">TikTok</label>
                        <input type="text" name="tiktok" id="tiktok" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('tiktok') }}" placeholder="https://tiktok.com/@username">
                    </div>
                    <div>
                        <label for="whatsapp" class="text-xs text-gray-500">WhatsApp Channel</label>
                        <input type="text" name="whatsapp" id="whatsapp" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('whatsapp') }}" placeholder="https://whatsapp.com/channel/...">
                    </div>
                    <div>
                        <label for="youtube" class="text-xs text-gray-500">YouTube</label>
                        <input type="text" name="youtube" id="youtube" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                               value="{{ old('youtube') }}" placeholder="https://youtube.com/@channel">
                    </div>
                </div>
            </div>

            <!-- Order & Status -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <input type="number" name="order" id="order" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('order', $founders->count() + 1) }}">
                    @error('order')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                    Create Founder
                </button>
                <a href="{{ route('super-admin.founders.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.getElementById('photo');
        const preview = document.getElementById('photo-preview');
        const previewImg = preview.querySelector('img');

        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        });
    });
</script>
@endpush
@endsection