@extends('layouts.admin')

@section('title', 'Add Member')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.members', $institution) }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Members
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white">➕ Add Member to {{ $institution->name }}</h1>
        </div>
        
        <form method="POST" action="{{ route('admin.institutions.members.store', $institution) }}" class="p-6">
            @csrf
            
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 bg-white">
                        <option value="user">👤 Regular Member</option>
                        <option value="librarian">📚 Librarian</option>
                        <option value="institution_admin">👑 Institution Admin</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">A temporary password will be generated and shown after creation.</p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8 pt-6 border-t">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:shadow-lg transition font-semibold">
                    Add Member
                </button>
                <a href="{{ route('admin.institutions.members', $institution) }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition text-center">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection