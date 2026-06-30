@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left"></i> Back to Users
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-jlibrary-100 rounded-full flex items-center justify-center">
                <i class="ti ti-user text-3xl text-jlibrary-600"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->full_name }}</h1>
                <p class="text-gray-500">{{ $user->email }}</p>
                <span class="px-2 py-1 rounded-full text-xs {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $user->getRoleLabel() }}
                </span>
            </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-jlibrary-600">{{ $user->books->count() }}</p>
                <p class="text-sm text-gray-500">Books Read</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-jlibrary-600">{{ $user->certificates->count() }}</p>
                <p class="text-sm text-gray-500">Certificates</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-jlibrary-600">${{ number_format($user->wallet_balance ?? 0, 2) }}</p>
                <p class="text-sm text-gray-500">Wallet Balance</p>
            </div>
        </div>
        
        <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-900 mb-2">Account Info</h3>
            <p class="text-sm text-gray-600">Joined: {{ $user->created_at->format('F d, Y') }}</p>
        </div>
    </div>
</div>
@endsection