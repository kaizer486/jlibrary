@extends('layouts.admin')

@section('title', 'Edit Institution')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Institutions
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-edit"></i> Edit Institution
            </h1>
            <p class="text-amber-100 text-sm mt-1">Update institution information</p>
        </div>

        <form method="POST" action="{{ route('admin.institutions.update', $institution) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Institution Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $institution->name) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Institution Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $institution->email) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $institution->phone) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        City
                    </label>
                    <input type="text" name="city" value="{{ old('city', $institution->city) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Region
                    </label>
                    <input type="text" name="region" value="{{ old('region', $institution->region) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Address
                    </label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $institution->address) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Website
                    </label>
                    <input type="url" name="website" value="{{ old('website', $institution->website) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="pending" {{ $institution->status == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="approved" {{ $institution->status == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="suspended" {{ $institution->status == 'suspended' ? 'selected' : '' }}>⚠️ Suspended</option>
                        <option value="inactive" {{ $institution->status == 'inactive' ? 'selected' : '' }}>❌ Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Subscription Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="subscription_tier" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="basic" {{ $institution->subscription_tier == 'basic' ? 'selected' : '' }}>📘 Basic</option>
                        <option value="premium" {{ $institution->subscription_tier == 'premium' ? 'selected' : '' }}>📚 Premium</option>
                        <option value="enterprise" {{ $institution->subscription_tier == 'enterprise' ? 'selected' : '' }}>🏢 Enterprise</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Max Users
                    </label>
                    <input type="number" name="max_users" value="{{ old('max_users', $institution->max_users) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Max Books
                    </label>
                    <input type="number" name="max_books" value="{{ old('max_books', $institution->max_books) }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Institution Stats -->
            <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <i class="ti ti-chart-bar text-indigo-600"></i> Institution Statistics
                </h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-2xl font-bold text-indigo-600">{{ $institution->users()->count() }}</p>
                        <p class="text-xs text-gray-500">Total Users</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-2xl font-bold text-purple-600">{{ $institution->books()->count() }}</p>
                        <p class="text-xs text-gray-500">Total Books</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg shadow-sm">
                        <p class="text-2xl font-bold text-green-600">TSh {{ number_format($institution->wallet->balance ?? 0, 2) }}</p>
                        <p class="text-xs text-gray-500">Wallet Balance</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t border-gray-200">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <a href="{{ route('admin.institutions.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center text-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if($institution->users()->count() == 0)
    <div class="mt-6 bg-red-50 rounded-2xl border border-red-200 overflow-hidden">
        <div class="px-6 py-3 bg-red-100 border-b border-red-200">
            <h3 class="font-semibold text-red-700 flex items-center gap-2">
                <i class="ti ti-alert-triangle"></i> Danger Zone
            </h3>
        </div>
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-800">Delete this institution permanently</p>
                <p class="text-sm text-gray-500">This action cannot be undone. All institution data will be lost.</p>
            </div>
            <form method="POST" action="{{ route('admin.institutions.destroy', $institution) }}" onsubmit="return confirm('Delete {{ $institution->name }} permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-trash"></i> Delete Institution
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection