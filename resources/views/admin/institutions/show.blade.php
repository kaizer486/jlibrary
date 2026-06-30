@extends('layouts.master')

@section('title', $institution->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700 transition inline-flex items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Institutions
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="ti ti-building text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $institution->name }}</h1>
                        <p class="text-indigo-200">{{ $institution->type_label ?? 'Institution' }}</p>
                    </div>
                </div>
                <div>
                    @php
                        $colors = ['approved' => 'bg-green-500/30 text-green-200', 'pending' => 'bg-yellow-500/30 text-yellow-200', 'suspended' => 'bg-red-500/30 text-red-200'];
                        $color = $colors[$institution->status] ?? 'bg-gray-500/30 text-gray-200';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $color }}">
                        {{ ucfirst($institution->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Stats Cards -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-bar text-purple-600"></i> Statistics
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Total Members</span>
                        <span class="font-semibold text-gray-800">{{ number_format($institution->users_count ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Total Books</span>
                        <span class="font-semibold text-gray-800">{{ number_format($institution->books_count ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Subscription Plan</span>
                        <span class="font-semibold capitalize">{{ $institution->subscription_tier ?? 'Basic' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Wallet Balance</span>
                        <span class="font-semibold text-green-600">TSh {{ number_format($institution->wallet?->balance ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-info-circle text-purple-600"></i> Information
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Email</p>
                        <p class="mt-1">{{ $institution->email ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Phone</p>
                        <p class="mt-1">{{ $institution->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Location</p>
                        <p class="mt-1">{{ $institution->city }}{{ $institution->city && $institution->region ? ', ' : '' }}{{ $institution->region ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Address</p>
                        <p class="mt-1">{{ $institution->address ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Website</p>
                        <p class="mt-1">
                            @if($institution->website)
                                <a href="{{ $institution->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $institution->website }}</a>
                            @else
                                Not provided
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Joined</p>
                        <p class="mt-1">{{ $institution->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-users text-purple-600"></i> Recent Members
                    </h2>
                    <p class="text-xs text-gray-400">Showing only member names (privacy protected)</p>
                </div>
                <div class="p-4">
                    @forelse($recentUsers ?? [] as $user)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ substr($user->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                    {{ ucfirst($user->role ?? 'member') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No members in this institution</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Books -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-books text-purple-600"></i> Recent Books
                    </h2>
                </div>
                <div class="p-4">
                    @forelse($recentBooks ?? [] as $book)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-800">{{ Str::limit($book->title, 40) }}</p>
                                <p class="text-xs text-gray-500">by {{ $book->author ?? 'Unknown' }}</p>
                            </div>
                            <div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($book->status ?? 'pending') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No books in this institution</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection