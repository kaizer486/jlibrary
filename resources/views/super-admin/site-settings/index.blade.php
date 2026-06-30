@extends('layouts.super-admin')

@section('title', 'Site Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Site Settings</h1>
            <p class="text-gray-500 text-sm mt-1">Manage the content displayed on the welcome page</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('super-admin.site-settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Vision, Mission, Motto -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-bulb text-yellow-500"></i>
                Vision, Mission & Motto
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Vision</label>
                    <textarea name="vision" id="vision" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('vision', $settings['vision'] ?? '') }}</textarea>
                    @error('vision')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Mission</label>
                    <textarea name="mission" id="mission" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('mission', $settings['mission'] ?? '') }}</textarea>
                    @error('mission')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="motto" class="block text-sm font-medium text-gray-700 mb-1">Motto</label>
                    <input type="text" name="motto" id="motto" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('motto', $settings['motto'] ?? 'Learn. Share. Grow.') }}">
                    @error('motto')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Platform Message -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-message text-blue-500"></i>
                Platform Message
            </h2>
            <div>
                <label for="platform_message" class="block text-sm font-medium text-gray-700 mb-1">Welcome Message</label>
                <textarea name="platform_message" id="platform_message" rows="6"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">{{ old('platform_message', $settings['platform_message'] ?? '') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">This message appears below the Vision, Mission, Motto section (like VC's message)</p>
                @error('platform_message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Announcements -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-bullhorn text-orange-500"></i>
                Announcements
            </h2>
            <p class="text-sm text-gray-500 mb-4">These announcements appear below the platform message (like CICT Digital Hub)</p>
            <div class="space-y-3">
                <div>
                    <label for="announcement_1" class="block text-sm font-medium text-gray-700 mb-1">Announcement 1</label>
                    <input type="text" name="announcement_1" id="announcement_1" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('announcement_1', $settings['announcement_1'] ?? '') }}" placeholder="🎉 New AI-Powered Book Recommendations Now Available!">
                    @error('announcement_1')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="announcement_2" class="block text-sm font-medium text-gray-700 mb-1">Announcement 2</label>
                    <input type="text" name="announcement_2" id="announcement_2" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('announcement_2', $settings['announcement_2'] ?? '') }}" placeholder="📚 50 New Books Added This Week">
                    @error('announcement_2')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="announcement_3" class="block text-sm font-medium text-gray-700 mb-1">Announcement 3</label>
                    <input type="text" name="announcement_3" id="announcement_3" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('announcement_3', $settings['announcement_3'] ?? '') }}" placeholder="🌍 Community Book Club Launching June 1st">
                    @error('announcement_3')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="ti ti-phone text-green-500"></i>
                Contact Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('contact_email', $settings['contact_email'] ?? 'info@jlibrary.co.tz') }}">
                    @error('contact_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="support_email" class="block text-sm font-medium text-gray-700 mb-1">Support Email</label>
                    <input type="email" name="support_email" id="support_email" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('support_email', $settings['support_email'] ?? 'support@jlibrary.co.tz') }}">
                    @error('support_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="contact_phone" id="contact_phone" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('contact_phone', $settings['contact_phone'] ?? '0766408259') }}">
                    @error('contact_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" id="address" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                           value="{{ old('address', $settings['address'] ?? 'Dar es Salaam, Tanzania') }}">
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-lg font-medium transition">
                <i class="ti ti-device-floppy"></i> Save All Settings
            </button>
            <a href="{{ route('super-admin.dashboard') }}" class="text-gray-600 hover:text-gray-800 transition">
                Cancel
            </a>
        </div>
    </form>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex items-start gap-3">
            <i class="ti ti-info-circle text-blue-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-sm text-blue-700 font-medium">How it works:</p>
                <ul class="text-sm text-blue-600 list-disc list-inside mt-1 space-y-1">
                    <li>All fields are optional - leave blank to hide that section</li>
                    <li>Changes appear immediately on the welcome page</li>
                    <li>Use emojis to make announcements more engaging</li>
                    <li>Contact information appears in the footer</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection