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
            <h1 class="text-2xl font-bold text-gray-800">Add Founder</h1>
            <p class="text-gray-500 text-sm mt-1">Add a new founder or leadership team member</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl">
        <form action="{{ route('super-admin.founders.store') }}" method="POST" enctype="multipart/form-data" id="founderForm">
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

            <!-- Photo with Cropper -->
            <div class="mb-4">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                
                <!-- Hidden file input (replaced by cropper) -->
                <input type="file" name="photo" id="photo" accept="image/*" class="hidden">
                
                <!-- File picker button -->
                <button type="button" id="pick-photo-btn" 
                        class="w-full px-4 py-3 border-2 border-dashed border-purple-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition text-purple-600 font-medium flex items-center justify-center gap-2">
                    <i class="ti ti-upload"></i> Choose Photo
                </button>
                
                <p class="text-xs text-gray-500 mt-2">
                    <i class="ti ti-info-circle"></i> 
                    <strong>Required:</strong> Crop to <strong>4:5 portrait ratio</strong> (e.g., 400×500px). Max 2MB. JPG, PNG, WebP.
                </p>
                @error('photo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                
                <!-- Cropper Container -->
                <div id="cropper-container" class="mt-4 hidden">
                    <div class="border-2 border-dashed border-purple-200 rounded-xl p-4 bg-purple-50/50">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-purple-700">
                                <i class="ti ti-crop"></i> Adjust crop area
                            </p>
                            <button type="button" id="remove-photo" class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1">
                                <i class="ti ti-trash"></i> Remove
                            </button>
                        </div>
                        
                        <div class="max-h-[400px] overflow-hidden rounded-lg bg-gray-100">
                            <img id="cropper-image" src="#" alt="Crop preview" class="w-full block">
                        </div>
                        
                        <div class="flex flex-wrap justify-center gap-2 mt-3">
                            <button type="button" id="rotate-left" class="px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="ti ti-rotate-2"></i> Rotate Left
                            </button>
                            <button type="button" id="rotate-right" class="px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="ti ti-rotate-clockwise-2"></i> Rotate Right
                            </button>
                            <button type="button" id="zoom-in" class="px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="ti ti-zoom-in"></i> Zoom In
                            </button>
                            <button type="button" id="zoom-out" class="px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="ti ti-zoom-out"></i> Zoom Out
                            </button>
                            <button type="button" id="reset-crop" class="px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                <i class="ti ti-restore"></i> Reset
                            </button>
                        </div>
                        
                        <p class="text-xs text-gray-400 mt-2 text-center">
                            Drag to move • Corner handles to resize • All photos will be 4:5 ratio
                        </p>
                    </div>
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
    const pickBtn = document.getElementById('pick-photo-btn');
    const photoInput = document.getElementById('photo');
    const cropperContainer = document.getElementById('cropper-container');
    const cropperImage = document.getElementById('cropper-image');
    const removeBtn = document.getElementById('remove-photo');
    const founderForm = document.getElementById('founderForm');
    
    let cropper = null;
    let croppedFile = null;

    // FIXED ASPECT RATIO: 4/5 (portrait) — change this to match your frontend
    const ASPECT_RATIO = 4 / 5;

    // Open file picker
    pickBtn.addEventListener('click', function() {
        photoInput.click();
    });

    // Handle file selection
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file (JPG, PNG, WebP).');
            photoInput.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            alert('Image must be less than 2MB.');
            photoInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            cropperImage.src = event.target.result;
            pickBtn.classList.add('hidden');
            cropperContainer.classList.remove('hidden');

            if (cropper) cropper.destroy();

            cropper = new Cropper(cropperImage, {
                aspectRatio: ASPECT_RATIO,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.85,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                minContainerWidth: 250,
                minContainerHeight: 250,
                ready: function() {
                    // Set initial crop to portrait 4:5
                    const canvasData = this.cropper.getCanvasData();
                    const cropWidth = Math.min(canvasData.width, canvasData.height * ASPECT_RATIO) * 0.85;
                    const cropHeight = cropWidth / ASPECT_RATIO;
                    
                    this.cropper.setCropBoxData({
                        width: cropWidth,
                        height: cropHeight,
                        left: (canvasData.width - cropWidth) / 2,
                        top: (canvasData.height - cropHeight) / 2
                    });
                }
            });
        };
        reader.readAsDataURL(file);
    });

    // Remove photo
    removeBtn.addEventListener('click', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        photoInput.value = '';
        croppedFile = null;
        cropperContainer.classList.add('hidden');
        pickBtn.classList.remove('hidden');
        cropperImage.src = '#';
    });

    // Toolbar buttons
    document.getElementById('rotate-left').addEventListener('click', () => { if (cropper) cropper.rotate(-90); });
    document.getElementById('rotate-right').addEventListener('click', () => { if (cropper) cropper.rotate(90); });
    document.getElementById('zoom-in').addEventListener('click', () => { if (cropper) cropper.zoom(0.1); });
    document.getElementById('zoom-out').addEventListener('click', () => { if (cropper) cropper.zoom(-0.1); });
    document.getElementById('reset-crop').addEventListener('click', () => { if (cropper) cropper.reset(); });

    // Before submit: crop and replace file input
    founderForm.addEventListener('submit', function(e) {
        if (!cropper || !photoInput.files.length) return; // No photo or no cropper = submit as-is

        e.preventDefault();

        // Get cropped canvas at 400x500 (4:5 ratio)
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 500,
            fillColor: '#ffffff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        // Convert canvas to Blob → File
        canvas.toBlob(function(blob) {
            // Create new File from blob
            const originalName = photoInput.files[0].name;
            const fileName = originalName.replace(/\.[^/.]+$/, '') + '_cropped.jpg';
            croppedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

            // Create DataTransfer to replace the file input's files
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            photoInput.files = dataTransfer.files;

            // Now submit with the cropped file
            founderForm.submit();
        }, 'image/jpeg', 0.92);
    });
});
</script>
@endpush
@endsection