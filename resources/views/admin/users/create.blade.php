@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">➕ Add New User</h1>
            <p class="text-purple-200 text-sm">Create a new user account manually</p>
        </div>
        
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-5">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        User Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>👤 Regular User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                        <option value="institution_admin" {{ old('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Institution Admin</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Super Admin role cannot be assigned manually. Contact existing Super Admin.</p>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <!-- Institution Assignment (shows only when Institution Admin is selected) -->
                <div id="institution-fields" style="display: none;" class="space-y-4 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-semibold text-gray-800">🏢 Institution Settings</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Institution</label>
                        <select name="institution_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="">Select Institution</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                                    {{ $institution->name }} ({{ $institution->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_institution_admin" value="1" {{ old('is_institution_admin') ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">This user is an Institution Administrator</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Institution Admins can manage members and books for their institution</p>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    <i class="ti ti-user-plus"></i> Create User
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
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
    
    function toggleInstitutionFields() {
        if (roleSelect.value === 'institution_admin') {
            institutionFields.style.display = 'block';
        } else {
            institutionFields.style.display = 'none';
        }
    }
    
    roleSelect.addEventListener('change', toggleInstitutionFields);
    toggleInstitutionFields();
</script>
@endsection