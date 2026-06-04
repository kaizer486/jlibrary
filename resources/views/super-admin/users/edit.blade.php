@extends('layouts.super-admin')

@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.users.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="ti ti-edit"></i> Edit User
            </h1>
            <p class="text-amber-100 text-sm mt-1">Update user information and role</p>
        </div>

        <form method="POST" action="{{ route('super-admin.users.update', $user) }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- User Avatar Preview -->
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl mb-6">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                    <span class="text-white text-xl font-bold">{{ substr($user->full_name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">{{ $user->full_name }}</p>
                    <p class="text-sm text-gray-500">User ID: #{{ $user->id }}</p>
                    <p class="text-xs text-gray-400">Member since {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        User Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role-select" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white focus:ring-2 focus:ring-purple-500">
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>👤 Regular User</option>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                        <option value="institution_admin" {{ $user->role == 'institution_admin' ? 'selected' : '' }}>🏢 Institution Admin</option>
                        <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Institution Assignment -->
                <div id="institution-fields" style="display: {{ $user->role == 'institution_admin' ? 'block' : 'none' }};" class="p-4 bg-gray-50 rounded-xl">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="ti ti-building"></i> Institution Settings
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Institution</label>
                            <select name="institution_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white">
                                <option value="">-- Select Institution --</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id', $user->institution_id) == $institution->id ? 'selected' : '' }}>
                                        {{ $institution->name }} ({{ $institution->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_institution_admin" value="1" {{ old('is_institution_admin', $user->is_institution_admin) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">This user is an Institution Administrator</span>
                            </label>
                            <p class="text-xs text-gray-400 mt-1 ml-6">Institution Admins can manage members and books for their institution</p>
                        </div>
                    </div>
                </div>

                <!-- Password Change Option -->
                <div class="border-t pt-4 mt-2">
                    <div class="flex items-center justify-between mb-4">
                        <label class="text-sm font-semibold text-gray-700">Change Password</label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" id="change-password-checkbox" class="w-4 h-4 text-purple-600 rounded">
                            <span class="text-sm text-gray-500">Check to change password</span>
                        </label>
                    </div>
                    
                    <div id="password-fields" style="display: none;" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" name="password" id="password" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <a href="{{ route('super-admin.users.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    @if(auth()->id() !== $user->id)
    <div class="mt-6 bg-red-50 rounded-2xl border border-red-200 overflow-hidden">
        <div class="px-6 py-3 bg-red-100 border-b border-red-200">
            <h3 class="font-semibold text-red-700 flex items-center gap-2">
                <i class="ti ti-alert-triangle"></i> Danger Zone
            </h3>
        </div>
        <div class="p-6 flex items-center justify-between">
            <div>
                <p class="font-medium text-gray-800">Delete this user permanently</p>
                <p class="text-sm text-gray-500">This action cannot be undone. All user data will be lost.</p>
            </div>
            <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->full_name }} permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-trash"></i> Delete User
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
    // Toggle password fields visibility
    const checkbox = document.getElementById('change-password-checkbox');
    const passwordFields = document.getElementById('password-fields');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                passwordFields.style.display = 'block';
                passwordInput.required = true;
                passwordConfirmInput.required = true;
            } else {
                passwordFields.style.display = 'none';
                passwordInput.required = false;
                passwordConfirmInput.required = false;
                passwordInput.value = '';
                passwordConfirmInput.value = '';
            }
        });
    }
    
    // Toggle institution fields based on role selection
    const roleSelect = document.getElementById('role-select');
    const institutionFieldsDiv = document.getElementById('institution-fields');
    
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            if (this.value === 'institution_admin') {
                institutionFieldsDiv.style.display = 'block';
            } else {
                institutionFieldsDiv.style.display = 'none';
            }
        });
    }
</script>
@endsection