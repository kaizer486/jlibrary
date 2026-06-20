@extends('layouts.app')

@section('title', 'Request to Create Institution')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-3xl">
        
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition inline-flex items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <i class="ti ti-file-plus text-white text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold text-white">Request to Create Institution</h1>
                        <p class="text-blue-100 text-sm">Submit a request to create your own institution</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Info Banner -->
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <i class="ti ti-info-circle text-blue-500 text-xl"></i>
                        <div>
                            <p class="text-sm text-blue-800">
                                <strong>Note:</strong> Your request will be reviewed by the Super Admin.
                                Once approved, you will become the Institution Admin.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('institution.store-request') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5">
                        <!-- Institution Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Institution Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="e.g., ABC University">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Institution Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Institution Type <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="">Select Type</option>
                                <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>🏫 School</option>
                                <option value="college" {{ old('type') == 'college' ? 'selected' : '' }}>🎓 College</option>
                                <option value="university" {{ old('type') == 'university' ? 'selected' : '' }}>🏛️ University</option>
                                <option value="library" {{ old('type') == 'library' ? 'selected' : '' }}>📚 Library</option>
                                <option value="bookstore" {{ old('type') == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
                                <option value="publisher" {{ old('type') == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                                <option value="research_center" {{ old('type') == 'research_center' ? 'selected' : '' }}>🔬 Research Center</option>
                                <option value="academy" {{ old('type') == 'academy' ? 'selected' : '' }}>📖 Academy</option>
                                <option value="institute" {{ old('type') == 'institute' ? 'selected' : '' }}>🏢 Institute</option>
                            </select>
                            @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="institution@email.com">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="+255 712 345 678">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- City -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., Dar es Salaam">
                                @error('city') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Region -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Region</label>
                                <input type="text" name="region" value="{{ old('region') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="e.g., Dar es Salaam">
                                @error('region') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Full address">
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                            <input type="url" name="website" value="{{ old('website') }}"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://www.example.com">
                            @error('website') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Tell us about your institution...">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Motivation -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Why do you want to create this institution? <span class="text-red-500">*</span>
                            </label>
                            <textarea name="motivation" rows="3" required
                                      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Explain why you want to create this institution...">{{ old('motivation') }}</textarea>
                            @error('motivation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Document Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Supporting Document (Optional)</label>
                            <input type="file" name="document" accept=".pdf,.doc,.docx"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-400 mt-1">Max 5MB. Allowed: PDF, DOC, DOCX</p>
                            @error('document') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                            <i class="ti ti-send"></i> Submit Request
                        </button>
                        <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection