@extends('layouts.librarian')

@section('title', 'Add New Member')
@section('page-title', ' Add New Member')

@section('content')

<div class="max-w-2xl mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('institution.members.index') }}" class="text-slate-400 hover:text-slate-300 transition inline-flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Members
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-700 rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-900/30 to-pink-900/30 px-6 py-4 border-b border-slate-700">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                <i class="ti ti-user-plus text-purple-400"></i>
                Add New Member to {{ $institution->name }}
            </h3>
        </div>
        
        <form method="POST" action="{{ route('institution.members.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Full Name 
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="search-bar">
                    @error('full_name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Email Address 
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="search-bar">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Role -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Role 
                    </label>
                    <select name="role" class="search-bar">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>👤 Member</option>
                        <option value="librarian" {{ old('role') == 'librarian' ? 'selected' : '' }}>📚 Librarian</option>
                        <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>👨‍🏫 Instructor</option>
                        <option value="institution_admin" {{ old('role') == 'institution_admin' ? 'selected' : '' }}>🏢 Institution Admin</option>
                    </select>
                    @error('role')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Info -->
                <div class="bg-slate-800/50 rounded-lg p-4 border border-slate-700">
                    <p class="text-sm text-slate-400">
                        <i class="ti ti-info-circle text-purple-400"></i>
                        A temporary password will be generated and sent to the member's email address.
                    </p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-4 border-t border-slate-700">
                <button type="submit" class="btn-library flex-1 justify-center">
                    <i class="ti ti-device-floppy"></i> Add Member
                </button>
                <a href="{{ route('institution.members.index') }}" class="bg-slate-800 text-slate-400 px-6 py-2.5 rounded-lg hover:bg-slate-700 transition border border-slate-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>

@endsection