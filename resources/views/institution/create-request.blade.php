@extends('layouts.app')

@section('title', 'Create Institution Request')
@section('page-title', '🏛️ Request to Create Institution')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-800 transition">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                    <i class="ti ti-building-community text-2xl text-white"></i>
                </span>
                Request to Create Institution
            </h1>
            <p class="text-slate-600 mt-1">Fill in the details to request a new institution</p>
        </div>

        <!-- Form -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 border border-white/80 shadow-sm">
            <form method="POST" action="{{ route('institution.store-request') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4">
                    <!-- Institution Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Institution Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="e.g., JLibrary University">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institution Type -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Institution Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type" class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
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
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="4" 
                                  class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                                  placeholder="Describe your institution (min 20 characters)...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address" value="{{ old('address') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="Street address">
                        @error('address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="city" value="{{ old('city') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="City">
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Region -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Region <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="region" value="{{ old('region') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="Region or State">
                        @error('region')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Contact Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="contact@institution.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="+255 700 000 000">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Website <span class="text-slate-400 text-xs font-normal">(Optional)</span>
                        </label>
                        <input type="url" name="website" value="{{ old('website') }}"
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                               placeholder="https://www.institution.com">
                        @error('website')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ✅ MOTIVATION - ADD THIS FIELD -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Motivation <span class="text-red-500">*</span>
                        </label>
                        <textarea name="motivation" rows="4" required
                                  class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 transition"
                                  placeholder="Why do you want to create this institution? (min 20 characters)...">{{ old('motivation') }}</textarea>
                        @error('motivation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ✅ SUPPORTING DOCUMENT - ADD THIS FIELD -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Supporting Document <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white hover:file:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        <p class="text-xs text-slate-400 mt-1">Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG (Max 10MB)</p>
                        @error('document')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo Upload (Optional) -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Logo (Optional)
                        </label>
                        <input type="file" name="logo" accept="image/*"
                               class="w-full bg-white/80 border border-slate-200/60 rounded-lg px-4 py-3 text-slate-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-600 file:text-white hover:file:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 transition">
                        @error('logo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex gap-3 pt-4 border-t border-slate-200/60">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-orange-600 to-amber-600 hover:shadow-lg hover:shadow-orange-600/25 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                            <i class="ti ti-send"></i> Submit Request
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-white/60 hover:bg-white/80 text-slate-700 px-6 py-3 rounded-xl font-semibold transition border border-slate-200/60">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection