@extends('layouts.app')

@section('title', 'Create Institution Request')
@section('page-title', '🏛️ Request to Create Institution')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-white">🏛️ Request to Create Institution</h1>
            <p class="text-gray-400 mt-1">Fill in the details to request a new institution</p>
        </div>

        <!-- Form -->
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
            <form method="POST" action="{{ route('institution.store-request') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4">
                    <!-- Institution Name -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Institution Name <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="e.g., JLibrary University">
                        @error('name')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institution Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Institution Type <span class="text-red-400">*</span>
                        </label>
                        <select name="type" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Select Type</option>
                            <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                            <option value="college" {{ old('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                            <option value="university" {{ old('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                            <option value="library" {{ old('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                            <option value="bookstore" {{ old('type') == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
                            <option value="publisher" {{ old('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                            <option value="research_center" {{ old('type') == 'research_center' ? 'selected' : '' }}>🔬 Research Center</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>🏢 Other</option>
                        </select>
                        @error('type')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="4" 
                                  class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                  placeholder="Describe your institution...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Address
                        </label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="Street address">
                        @error('address')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            City
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="City">
                        @error('city')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Contact Email <span class="text-red-400">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="contact@institution.com">
                        @error('email')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Phone Number
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="+255 700 000 000">
                        @error('phone')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Website
                        </label>
                        <input type="url" name="website" value="{{ old('website') }}"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="https://www.institution.com">
                        @error('website')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">
                            Logo (Optional)
                        </label>
                        <input type="file" name="logo" accept="image/*"
                               class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white hover:file:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        @error('logo')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-4 border-t border-white/10">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:shadow-lg text-white px-6 py-3 rounded-xl font-semibold transition">
                            <i class="ti ti-send"></i> Submit Request
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-xl font-semibold transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection