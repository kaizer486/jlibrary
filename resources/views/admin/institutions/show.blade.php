@extends('layouts.admin')

@section('title', $institution->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.institutions.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
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
                        <p class="text-indigo-200">{{ $institution->type_label }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.institutions.edit', $institution) }}" class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-xl transition text-sm">
                        Edit Institution
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Stats Cards -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-bar text-indigo-600"></i> Statistics
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Total Users</span>
                        <span class="font-semibold text-gray-800">{{ $institution->users_count ?? $institution->users()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Total Books</span>
                        <span class="font-semibold text-gray-800">{{ $institution->books_count ?? $institution->books()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Subscription Plan</span>
                        <span class="font-semibold capitalize">{{ $institution->subscription_tier }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Wallet Balance</span>
                        <span class="font-semibold text-green-600">TSh {{ number_format($institution->wallet->balance ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-200">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-info-circle text-indigo-600"></i> Information
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Status</p>
                        <p class="mt-1">
                            @if($institution->status === 'approved')
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>
                            @elseif($institution->status === 'pending')
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>
                            @else
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold bg-red-100 text-red-700">⚠️ Suspended</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Email</p>
                        <p class="mt-1 text-gray-700">{{ $institution->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Phone</p>
                        <p class="mt-1 text-gray-700">{{ $institution->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Location</p>
                        <p class="mt-1 text-gray-700">{{ $institution->city }}, {{ $institution->region }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Address</p>
                        <p class="mt-1 text-gray-700">{{ $institution->address ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Website</p>
                        <p class="mt-1">
                            @if($institution->website)
                                <a href="{{ $institution->website }}" target="_blank" class="text-indigo-600 hover:underline">{{ $institution->website }}</a>
                            @else
                                <span class="text-gray-500">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Joined</p>
                        <p class="mt-1 text-gray-700">{{ $institution->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-users text-indigo-600"></i> Recent Users
                    </h2>
                    <a href="{{ route('admin.users.index', ['institution_id' => $institution->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="p-4">
                    @forelse($institution->users()->latest()->limit(5)->get() as $user)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-xs font-bold">{{ substr($user->full_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-1 rounded-full {{ $user->getRoleBadgeClass() }}">
                                    {{ $user->getRoleLabel() }}
                                </span>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No users in this institution</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Books -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-books text-indigo-600"></i> Recent Books
                    </h2>
                    <a href="{{ route('admin.books.index', ['institution_id' => $institution->id]) }}" class="text-sm text-indigo-600 hover:text-indigo-700">View All →</a>
                </div>
                <div class="p-4">
                    @forelse($institution->books()->latest()->limit(5)->get() as $book)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-center gap-3">
                                @if($book->cover_image)
                                    <img src="{{ url('media/' . $book->cover_image) }}" class="w-8 h-10 rounded object-cover">
                                @else
                                    <div class="w-8 h-10 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="ti ti-book text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-800">{{ Str::limit($book->title, 40) }}</p>
                                    <p class="text-xs text-gray-500">by {{ $book->author }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs px-2 py-1 rounded-full {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                                <a href="{{ route('admin.books.show', $book) }}" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="ti ti-eye"></i>
                                </a>
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