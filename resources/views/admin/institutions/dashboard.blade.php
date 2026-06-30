@extends('layouts.master')



@section('title', 'Institution Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">🏢 {{ $institution->name }}</h1>
    <p class="text-gray-500 text-sm mt-1">Manage your institution's library and members</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border-l-4 border-purple-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Members</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_members'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
        <p class="text-gray-500 text-sm">Total Books</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_books'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-green-500 shadow-sm">
        <p class="text-gray-500 text-sm">Institution Admins</p>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_admins'] ?? 0 }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border-l-4 border-yellow-500 shadow-sm">
        <p class="text-gray-500 text-sm">Subscription</p>
        <p class="text-2xl font-bold text-gray-900">{{ $institution->subscription_label }}</p>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">
    <!-- Recent Members -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">👥 Recent Members</h2>
            <a href="{{ route('admin.users.index') }}?institution={{ $institution->id }}" class="text-sm text-purple-600">View All</a>
        </div>
        <div class="p-4">
            @forelse($recentMembers as $member)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div>
                        <p class="font-medium text-gray-900">{{ $member->full_name }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($member->role) }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $member->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No members yet</p>
            @endforelse
        </div>
    </div>
    
    <!-- Recent Books -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">📚 Recent Books</h2>
            <a href="{{ route('admin.books.index') }}?institution={{ $institution->id }}" class="text-sm text-purple-600">View All</a>
        </div>
        <div class="p-4">
            @forelse($recentBooks as $book)
                <div class="flex items-center justify-between py-2 border-b last:border-0">
                    <div>
                        <p class="font-medium text-gray-900">{{ $book->title }}</p>
                        <p class="text-xs text-gray-500">{{ $book->author }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ $book->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No books added yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection