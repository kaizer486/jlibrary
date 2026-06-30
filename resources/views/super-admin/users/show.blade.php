@extends('layouts.super-admin')

@section('title', $user->full_name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.users.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>

    <!-- User Profile Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-6">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="ti ti-user text-white text-4xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $user->full_name }}</h1>
                    <p class="text-purple-200">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if($user->isSuperAdmin())
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-500 text-white">👑 Super Admin</span>
                        @elseif($user->isAdmin())
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-500 text-white">🛡️ Admin</span>
                        @elseif($user->isAnyInstitutionAdmin())
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-500 text-white">🏢 Institution Admin</span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-500 text-white">👤 User</span>
                        @endif
                    </div>
                </div>
                <div class="ml-auto">
                    <a href="{{ route('super-admin.users.edit', $user) }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl transition text-sm flex items-center gap-2">
                        <i class="ti ti-edit"></i> Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Stats Cards -->
        <div class="bg-white rounded-xl shadow-sm p-5 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-books text-blue-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $user->books()->count() }}</p>
            <p class="text-sm text-gray-500">Books Read</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-certificate text-green-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $user->certificates()->count() }}</p>
            <p class="text-sm text-gray-500">Certificates</p>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-5 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-wallet text-yellow-600 text-xl"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600">TSh {{ number_format($user->wallet_balance ?? 0, 2) }}</p>
            <p class="text-sm text-gray-500">Wallet Balance</p>
        </div>
    </div>

    <!-- User Details -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-info-circle text-purple-600"></i> Account Information
            </h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Full Name</p>
                    <p class="mt-1 font-medium text-gray-800">{{ $user->full_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Email Address</p>
                    <p class="mt-1 text-gray-700">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Role</p>
                    <p class="mt-1 capitalize">{{ $user->role }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Institution</p>
                    <p class="mt-1">{{ $user->institution->name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Member Since</p>
                    <p class="mt-1">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Last Updated</p>
                    <p class="mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Books -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                <i class="ti ti-books text-purple-600"></i> Recently Added Books
            </h2>
        </div>
        <div class="p-6">
            @if($user->books()->count() > 0)
                <div class="space-y-3">
                    @foreach($user->books()->latest()->limit(5)->get() as $book)
                        <div class="flex items-center justify-between py-2 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ $book->title }}</p>
                                <p class="text-xs text-gray-500">by {{ $book->author }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $book->pivot->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No books added yet</p>
            @endif
        </div>
    </div>
</div>
@endsection