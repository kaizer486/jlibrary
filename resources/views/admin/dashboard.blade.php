@php
    $layout = 'layouts.app'; 
    
    if (auth()->check()) {
        if (auth()->user()->hasRole('super_admin')) {
            $layout = 'layouts.super-admin';
        } elseif (auth()->user()->hasRole('institution_admin')) {
            $layout = 'layouts.institution';
        } elseif (auth()->user()->hasRole('admin')) {
            $layout = 'layouts.admin';
        } elseif (auth()->user()->hasRole('instructor')) {
            $layout = 'layouts.instructor';
        } elseif (auth()->user()->hasRole('librarian')) {
            $layout = 'layouts.librarian';
        } else {
            $layout = 'layouts.app';
        }
    }
@endphp

@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
    <p class="text-gray-600 mt-1">Welcome back, {{ Auth::user()->full_name }}!</p>
    <p class="text-sm text-purple-600 mt-1">You have administrator privileges.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
            </div>
            <i class="ti ti-users text-4xl text-blue-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Books</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Book::count() }}</p>
            </div>
            <i class="ti ti-books text-4xl text-green-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Marketplace Listings</p>
                <p class="text-3xl font-bold text-gray-900">{{ \App\Models\MarketplaceListing::count() }}</p>
            </div>
            <i class="ti ti-shopping-cart text-4xl text-purple-500"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Approvals</p>
                <p class="text-3xl font-bold text-yellow-600">{{ \App\Models\MarketplaceListing::where('status', 'pending')->count() }}</p>
            </div>
            <i class="ti ti-clock text-4xl text-yellow-500"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid md:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('admin.books.create') }}" class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-plus text-2xl mb-1 block"></i>
        <span class="font-semibold">Add New Book</span>
    </a>
    <a href="{{ route('admin.marketplace.pending') }}" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-check text-2xl mb-1 block"></i>
        <span class="font-semibold">Review Listings</span>
    </a>
    <a href="{{ route('admin.users.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-users text-2xl mb-1 block"></i>
        <span class="font-semibold">Manage Users</span>
    </a>
    <a href="{{ route('admin.books.index') }}" class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl p-4 text-center hover:shadow-lg transition">
        <i class="ti ti-books text-2xl mb-1 block"></i>
        <span class="font-semibold">Manage Books</span>
    </a>
</div>

<!-- Recent Activity -->
<div class="grid lg:grid-cols-2 gap-6">
    <!-- Recent Users -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Recent Users</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-purple-600">View All →</a>
        </div>
        <div class="space-y-3">
            @foreach(\App\Models\User::latest()->limit(5)->get() as $user)
                <div class="flex items-center justify-between py-2 border-b">
                    <div>
                        <p class="font-medium text-gray-900">{{ $user->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                        {{ $user->getRoleLabel() }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Pending Marketplace Listings -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Pending Listings</h2>
            <a href="{{ route('admin.marketplace.pending') }}" class="text-sm text-purple-600">Review →</a>
        </div>
        <div class="space-y-3">
            @forelse(\App\Models\MarketplaceListing::where('status', 'pending')->with('seller')->latest()->limit(5)->get() as $listing)
                <div class="flex items-center justify-between py-2 border-b">
                    <div>
                        <p class="font-medium text-gray-900">{{ $listing->title }}</p>
                        <p class="text-sm text-gray-500">by {{ $listing->seller->full_name }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">Pending</span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="ti ti-check text-4xl mb-2 block"></i>
                    <p>No pending listings</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection