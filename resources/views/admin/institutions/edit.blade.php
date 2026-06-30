@extends('layouts.master')



@section('title', 'Edit Institution')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Institutions
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white">✏️ Edit Institution</h1>
            <p class="text-amber-100 text-sm">Update institution information</p>
        </div>
        
        <form method="POST" action="{{ route('admin.institutions.update', $institution) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Institution Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $institution->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Institution Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                        <option value="school" {{ $institution->type == 'school' ? 'selected' : '' }}>🏫 School</option>
                        <option value="college" {{ $institution->type == 'college' ? 'selected' : '' }}>🎓 College</option>
                        <option value="university" {{ $institution->type == 'university' ? 'selected' : '' }}>🏛️ University</option>
                        <option value="library" {{ $institution->type == 'library' ? 'selected' : '' }}>📚 Library</option>
                        <option value="bookstore" {{ $institution->type == 'bookstore' ? 'selected' : '' }}>📖 Bookstore</option>
                        <option value="publisher" {{ $institution->type == 'publisher' ? 'selected' : '' }}>📰 Publisher</option>
                        <option value="research_center" {{ $institution->type == 'research_center' ? 'selected' : '' }}>🔬 Research Center</option>
                        <option value="other" {{ $institution->type == 'other' ? 'selected' : '' }}>🏢 Other</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $institution->email) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $institution->phone) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $institution->city) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Region</label>
                    <input type="text" name="region" value="{{ old('region', $institution->region) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">{{ old('address', $institution->address) }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website', $institution->website) }}" placeholder="https://example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
            </div>
            
            <!-- Super Admin Only Fields -->
            @if(auth()->user()->isSuperAdmin())
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">⚙️ Admin Settings</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-4 py-2 border rounded-lg bg-white">
                            <option value="pending" {{ $institution->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="approved" {{ $institution->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                            <option value="suspended" {{ $institution->status == 'suspended' ? 'selected' : '' }}>⚠️ Suspended</option>
                            <option value="inactive" {{ $institution->status == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Tier</label>
                        <select name="subscription_tier" class="w-full px-4 py-2 border rounded-lg bg-white">
                            <option value="basic" {{ $institution->subscription_tier == 'basic' ? 'selected' : '' }}>📘 Basic</option>
                            <option value="premium" {{ $institution->subscription_tier == 'premium' ? 'selected' : '' }}>📚 Premium</option>
                            <option value="enterprise" {{ $institution->subscription_tier == 'enterprise' ? 'selected' : '' }}>🏢 Enterprise</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Users</label>
                        <input type="number" name="max_users" value="{{ old('max_users', $institution->max_users) }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Books</label>
                        <input type="number" name="max_books" value="{{ old('max_books', $institution->max_books) }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
            </div>
            @endif
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <a href="{{ route('admin.institutions.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection