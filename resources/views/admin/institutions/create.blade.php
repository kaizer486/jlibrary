@extends('layouts.master')



@section('title', 'Add Institution')

@section('page-content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Institutions
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">🏢 Add New Institution</h1>
            <p class="text-purple-200 text-sm">Register a school, library, or other institution</p>
        </div>
        
        <form method="POST" action="{{ route('admin.institutions.store') }}" class="p-6">
            @csrf
            
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Institution Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Institution Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
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
                    @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Region</label>
                    <input type="text" name="region" value="{{ old('region') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('address') }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="https://example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-building"></i> Create Institution
                </button>
                <a href="{{ route('admin.institutions.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection