@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <i class="ti ti-user-edit text-purple-600 text-3xl"></i>
                <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
            </div>
            <p class="text-gray-600">Customize your public profile and personal information</p>
        </div>
      <!-- Success Message -->
@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3">
        <i class="ti ti-circle-check text-green-600 text-xl"></i>
        <div>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-500 hover:text-green-700">
            <i class="ti ti-x text-sm"></i>
        </button>
    </div>
@endif

<!-- INSTITUTION INFO CARD - Only shows if user belongs to an institution -->
@if(Auth::user()->institution_id && Auth::user()->institution)
<div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-4 mb-6 border border-indigo-100">
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="ti ti-building text-white text-lg"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Your Institution</p>
            <p class="font-semibold text-gray-800 institution-name-multiline" title="{{ Auth::user()->institution->name }}">
                {{ Auth::user()->institution->name }}
            </p>
            <p class="text-xs text-gray-500">You are a member of this institution</p>
        </div>
        <a href="{{ route('institution.members.directory') }}" class="text-indigo-600 hover:text-indigo-700 text-sm flex items-center gap-1 whitespace-nowrap flex-shrink-0">
            View Members <i class="ti ti-arrow-right"></i>
        </a>
    </div>
</div>
@endif

<!-- Error Messages -->
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-alert-circle text-red-600 text-lg"></i>
            <p class="text-red-700 font-medium">Please fix the following errors:</p>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li class="text-red-600 text-sm">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <i class="ti ti-alert-circle text-red-600 text-lg"></i>
                    <p class="text-red-700 font-medium">Please fix the following errors:</p>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-red-600 text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Cover Photo & Avatar Section -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
            <!-- Cover Photo -->
            <div class="relative h-48 bg-gradient-to-r from-purple-500 to-pink-500">
                @if($user->cover_photo)
                    <img src="{{ Storage::url($user->cover_photo) }}" class="w-full h-full object-cover">
                @endif
                <div class="absolute bottom-4 right-4 flex gap-2">
                    <button onclick="document.getElementById('cover-input').click()" 
                            class="bg-black/50 hover:bg-black/70 text-white px-3 py-1.5 rounded-lg text-sm flex items-center gap-2 transition">
                        <i class="ti ti-camera"></i>
                        Change Cover
                    </button>
                    @if($user->cover_photo)
                        <form method="POST" action="{{ route('profile.cover.delete') }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500/80 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm flex items-center gap-2 transition" onclick="return confirm('Remove cover photo?')">
                                <i class="ti ti-trash"></i>
                                Remove
                            </button>
                        </form>
                    @endif
                </div>
                <form id="cover-form" method="POST" action="{{ route('profile.cover') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="cover-input" name="cover_photo" accept="image/*" class="hidden" onchange="this.form.submit()">
                </form>
            </div>
            
            <!-- Avatar -->
            <div class="relative px-6 pb-6">
                <div class="flex justify-between items-end -mt-16">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full border-4 border-white bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center overflow-hidden shadow-lg">
                            @if($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}" class="w-full h-full object-cover">
                            @else
                                <i class="ti ti-user text-white text-5xl"></i>
                            @endif
                        </div>
                        <button onclick="document.getElementById('avatar-input').click()" 
                                class="absolute bottom-1 right-1 bg-purple-600 text-white p-1.5 rounded-full hover:bg-purple-700 transition shadow-md">
                            <i class="ti ti-camera text-sm"></i>
                        </button>
                        @if($user->avatar)
                            <form method="POST" action="{{ route('profile.avatar.delete') }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="absolute -top-2 -right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600 transition shadow-md" onclick="return confirm('Remove avatar?')">
                                    <i class="ti ti-trash text-xs"></i>
                                </button>
                            </form>
                        @endif
                        <form id="avatar-form" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </form>
                    </div>
                    <a href="{{ route('profile.show', $user->id) }}" class="text-purple-600 text-sm hover:underline">
                        View Public Profile →
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Edit Form -->
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-info-circle text-purple-600"></i>
                    Basic Information
                </h2>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-user text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-mail text-gray-400 text-sm"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-map-pin text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="location" value="{{ old('location', $user->location) }}" 
                                   placeholder="e.g., Dar es Salaam, Tanzania"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-briefcase text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="occupation" value="{{ old('occupation', $user->occupation) }}" 
                                   placeholder="e.g., Software Engineer"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-calendar text-gray-400 text-sm"></i>
                            </div>
                            <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio / About Me</label>
                    <textarea name="bio" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Max 500 characters</p>
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-share text-purple-600"></i>
                    Social Links
                </h2>
                
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <i class="ti ti-brand-facebook text-blue-600 w-6"></i>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $user->facebook_url) }}" 
                               placeholder="https://facebook.com/username"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="ti ti-brand-twitter text-blue-400 w-6"></i>
                        <input type="url" name="twitter_url" value="{{ old('twitter_url', $user->twitter_url) }}" 
                               placeholder="https://twitter.com/username"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="ti ti-brand-linkedin text-blue-700 w-6"></i>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}" 
                               placeholder="https://linkedin.com/in/username"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="ti ti-brand-github text-gray-800 w-6"></i>
                        <input type="url" name="github_url" value="{{ old('github_url', $user->github_url) }}" 
                               placeholder="https://github.com/username"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="ti ti-brand-instagram text-pink-600 w-6"></i>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $user->instagram_url) }}" 
                               placeholder="https://instagram.com/username"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="ti ti-world text-green-600 w-6"></i>
                        <input type="url" name="website_url" value="{{ old('website_url', $user->website_url) }}" 
                               placeholder="https://your-website.com"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-lock text-purple-600"></i>
                    Change Password
                </h2>
                <p class="text-sm text-gray-500 mb-4">Leave blank to keep your current password</p>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="ti ti-lock text-gray-400 text-sm"></i>
                            </div>
                            <input type="password" name="password_confirmation" 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button type="submit" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg hover:bg-purple-700 transition font-medium flex items-center gap-2">
                    <i class="ti ti-device-floppy"></i> Save Changes
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition font-medium flex items-center gap-2">
                    <i class="ti ti-arrow-left"></i> Cancel
                </a>
            </div>
        </form>
        
        <!-- Account Info Card -->
        <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-100">
            <div class="flex items-start gap-3">
                <i class="ti ti-info-circle text-blue-500 text-lg mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-blue-800">Account Information</p>
                    <p class="text-xs text-blue-600 mt-1">Member since: {{ $user->created_at->format('F j, Y') }}</p>
                    <p class="text-xs text-blue-600">Role: {{ $user->getRoleLabel() }}</p>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection