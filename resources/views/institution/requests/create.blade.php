@extends('layouts.app')

@section('title', 'Create Institution Request')

@section('content')

<!-- ========================================== -->
<!-- LIGHT BISQUE BACKGROUND                    -->
<!-- ========================================== -->
<div style="position: fixed; inset: 0; background: #e9e8e6; z-index: -10;"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <!-- ========================================== -->
        <!-- HEADER CARD - CLEARLY DEFINED              -->
        <!-- ========================================== -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl border border-white/80 shadow-sm p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-800 transition p-2 hover:bg-slate-100 rounded-lg">
                        <i class="ti ti-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-3">
                            <span class="bg-gradient-to-br from-orange-500 to-amber-500 p-2 rounded-xl shadow-lg shadow-orange-500/20">
                                <i class="ti ti-building-community text-2xl text-white"></i>
                            </span>
                            Create Institution
                        </h1>
                        <p class="text-slate-600 text-sm mt-0.5">Submit a request to create a new institution</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- FORM CARD                                  -->
        <!-- ========================================== -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/80 overflow-hidden">
            <form method="POST" action="{{ route('institution.store-request') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="p-6 md:p-8 space-y-6">
                    
                    <!-- Section 1: Basic Information -->
                    <div class="bg-white/50 rounded-xl p-6 space-y-5 border border-slate-100/60">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-5 bg-gradient-to-b from-orange-500 to-amber-500 rounded-full"></span>
                            Basic Information
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Institution Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="Enter institution name">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Institution Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type" required
                                        class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800">
                                    <option value="">Select type</option>
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

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Contact Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="contact@institution.com">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Contact & Location -->
                    <div class="bg-white/50 rounded-xl p-6 space-y-5 border border-slate-100/60">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-5 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full"></span>
                            Contact & Location
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="+255 700 000 000">
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ✅ FIXED: Website is now OPTIONAL -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Website <span class="text-slate-400 text-xs font-normal">(Optional)</span>
                                </label>
                                <input type="url" name="website" value="{{ old('website') }}"
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="https://example.com">
                                @error('website')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="city" value="{{ old('city') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="Enter city">
                                @error('city')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Region/State <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="region" value="{{ old('region') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="Enter region">
                                @error('region')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="address" value="{{ old('address') }}" required
                                       class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400"
                                       placeholder="Street address, building number">
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Description & Motivation -->
                    <div class="bg-white/50 rounded-xl p-6 space-y-5 border border-slate-100/60">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-5 bg-gradient-to-b from-amber-600 to-orange-600 rounded-full"></span>
                            Details & Motivation
                        </h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Description <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description" rows="4" required
                                          class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400 resize-y"
                                          placeholder="Describe your institution in detail...">{{ old('description') }}</textarea>
                                <p class="text-xs text-slate-400 mt-1">Minimum 20 characters</p>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Motivation <span class="text-red-500">*</span>
                                </label>
                                <textarea name="motivation" rows="3" required
                                          class="w-full px-4 py-2.5 bg-white/80 border border-slate-200/60 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition text-slate-800 placeholder-slate-400 resize-y"
                                          placeholder="Why do you want to create this institution?">{{ old('motivation') }}</textarea>
                                <p class="text-xs text-slate-400 mt-1">Minimum 20 characters</p>
                                @error('motivation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Document Upload -->
                    <div class="bg-white/50 rounded-xl p-6 space-y-5 border border-slate-100/60">
                        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-1 h-5 bg-gradient-to-b from-orange-600 to-amber-600 rounded-full"></span>
                            Supporting Document
                        </h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                Upload Document <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="relative border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:border-orange-500 transition bg-white/60 hover:bg-orange-50/50 cursor-pointer">
                                <input type="file" 
                                       name="document" 
                                       id="document" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       required
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <div class="pointer-events-none">
                                    <i class="ti ti-upload text-4xl text-slate-400 mb-3 block"></i>
                                    <p class="text-slate-600 font-medium">Click to upload or drag & drop</p>
                                    <p class="text-xs text-slate-400 mt-1">PDF, Word, JPG, PNG (max 10MB)</p>
                                    <p id="fileName" class="text-sm text-orange-600 font-medium mt-2 hidden"></p>
                                </div>
                            </div>
                            
                            @error('document')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="border-t border-slate-200/60 px-6 md:px-8 py-5 bg-white/40 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 border border-slate-200/60 rounded-lg text-slate-700 font-medium hover:bg-white/50 transition text-center">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-lg font-medium hover:shadow-lg hover:shadow-orange-600/25 transition flex items-center justify-center gap-2">
                        <i class="ti ti-send"></i>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show file name when selected
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('document');
        const fileName = document.getElementById('fileName');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = this.files[0];
                
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    fileName.textContent = '📎 ' + file.name + ' (' + fileSize + ' MB)';
                    fileName.classList.remove('hidden');
                } else {
                    fileName.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
@endsection