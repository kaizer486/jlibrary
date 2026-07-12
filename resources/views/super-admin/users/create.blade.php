@extends('layouts.super-admin')

@section('title', 'Add New User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.users.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-user-plus"></i> Add New User
            </h1>
            <p class="text-purple-200 text-sm mt-1">Create a new user account</p>
        </div>

        <form method="POST" action="{{ route('super-admin.users.store') }}" class="p-6">
            @csrf

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        User Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Select Role --</option>
                        @foreach($availableRoles as $role => $label)
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Institution Assignment -->
                <div id="institution-fields" style="display: {{ old('role') && in_array(old('role'), ['institution_admin', 'school_admin', 'college_admin', 'university_admin', 'library_admin', 'bookstore_admin', 'publisher_admin', 'librarian', 'instructor']) ? 'block' : 'none' }};" class="p-4 bg-gray-50 rounded-xl">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="ti ti-building"></i> Institution Settings
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Institution</label>
                            <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                                <option value="">-- Select Institution --</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }} ({{ $institution->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_institution_admin" value="1" {{ old('is_institution_admin') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">This user is an Institution Administrator</span>
                            </label>
                            <p class="text-xs text-gray-400 mt-1 ml-6">Institution Admins can manage members and books for their institution</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-user-plus"></i> Create User
                </button>
                <a href="{{ route('super-admin.users.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Show/hide institution fields based on role selection
    const roleSelect = document.getElementById('role-select');
    const institutionFields = document.getElementById('institution-fields');
    
    const institutionRoles = ['institution_admin', 'school_admin', 'college_admin', 'university_admin', 'library_admin', 'bookstore_admin', 'publisher_admin', 'librarian', 'instructor'];
    
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            if (institutionRoles.includes(this.value)) {
                institutionFields.style.display = 'block';
            } else {
                institutionFields.style.display = 'none';
            }
        });
    }
</script>
@endsection