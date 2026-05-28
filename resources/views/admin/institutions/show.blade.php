@extends('layouts.admin')

@section('title', $institution->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700">
            <i class="ti ti-arrow-left"></i> Back to Institutions
        </a>
    </div>
    
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if($institution->logo)
                    <img src="{{ $institution->logo_url }}" class="w-12 h-12 rounded-lg object-cover">
                @else
                    <div class="w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
                        <i class="ti ti-building text-white text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $institution->name }}</h1>
                    <p class="text-purple-200 text-sm">{{ $institution->type_label }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.institutions.edit', $institution) }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition">Edit</a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $institution->users_count ?? $institution->users()->count() }}</p>
                    <p class="text-sm text-gray-500">Total Users</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $institution->books_count ?? $institution->books()->count() }}</p>
                    <p class="text-sm text-gray-500">Total Books</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $institution->subscription_label }}</p>
                    <p class="text-sm text-gray-500">Subscription</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Institution Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-3 bg-gray-50 border-b">
                <h2 class="font-semibold text-gray-800">📋 Institution Details</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Status</span>
                    <span class="font-medium">{{ $institution->status_label }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Email</span>
                    <span class="font-medium">{{ $institution->email }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Phone</span>
                    <span class="font-medium">{{ $institution->phone ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Address</span>
                    <span class="font-medium">{{ $institution->address ?? 'Not provided' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">City/Region</span>
                    <span class="font-medium">{{ $institution->city }}, {{ $institution->region }}</span>
                </div>
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-500">Website</span>
                    @if($institution->website)
                        <a href="{{ $institution->website }}" target="_blank" class="text-purple-600 hover:underline">{{ $institution->website }}</a>
                    @else
                        <span class="text-gray-400">Not provided</span>
                    @endif
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500">Joined</span>
                    <span class="font-medium">{{ $institution->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Admin Users -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-3 bg-gray-50 border-b">
                <h2 class="font-semibold text-gray-800">👥 Institution Admins</h2>
            </div>
            <div class="p-6">
                @if($institution->admins()->count() > 0)
                    <div class="space-y-3">
                        @foreach($institution->admins()->get() as $admin)
                            <div class="flex items-center justify-between py-2 border-b last:border-0">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $admin->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $admin->email }}</p>
                                </div>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">Admin</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No admins assigned yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection