@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
            <h1 class="text-xl font-bold text-white">✏️ Edit User</h1>
            <p class="text-amber-100 text-sm">Update user information and role</p>
        </div>
        
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-5">
                <!-- User Avatar Preview -->
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <span class="text-white text-xl font-bold">{{ substr($user->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">{{ $user->full_name }}</p>
                        <p class="text-sm text-gray-500">User ID: #{{ $user->id }}</p>
                    </div>
                </div>
                
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('full_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Role Selection (Only Super Admin can change role) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        User Role
                    </label>
                    
                    @if(auth()->user()->isSuperAdmin())
                        <select name="role" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>👤 Regular User</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                            <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>👑 Super Admin</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Changing role will update user permissions immediately.</p>
                    @else
                        <div class="px-4 py-2.5 bg-gray-100 rounded-lg">
                            @if($user->isSuperAdmin())
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">👑 Super Admin</span>
                            @elseif($user->isAdmin())
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">🛡️ Admin</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">👤 User</span>
                            @endif
                        </div>
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-xs text-amber-600 mt-1">Role changes can only be made by Super Admin.</p>
                    @endif
                    
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
    <!-- Danger Zone (Only for Super Admin) -->
    @if(auth()->user()->isSuperAdmin() && auth()->id() !== $user->id)
    <div class="mt-6 bg-red-50 rounded-xl border border-red-200 overflow-hidden">
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
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete {{ $user->full_name }} permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg transition flex items-center gap-2">
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
</script>
@endsection