@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Community</h1>
            <p class="text-gray-600">Join groups, discuss books, and learn together</p>
        </div>
        @auth
            <a href="{{ route('community.create') }}" class="bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition flex items-center gap-2">
                <i class="ti ti-plus"></i>
                Create Group
            </a>
        @endauth
    </div>
    
    <!-- Search Bar -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-8">
        <form method="GET" action="{{ route('community.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search groups by name or description..." 
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500 focus:border-transparent">
            </div>
            <button type="submit" class="bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                <i class="ti ti-search"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('community.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Clear
                </a>
            @endif
        </form>
    </div>
    
    <!-- Groups Grid -->
    @if($groups->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($groups as $group)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <!-- Group Cover -->
                    <div class="h-32 bg-gradient-to-r from-jlibrary-500 to-jlibrary-700 relative">
                        @if($group->cover_image)
                            <img src="{{ url('media/' . $group->cover_image) }}" alt="{{ $group->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-users text-5xl text-white/50"></i>
                            </div>
                        @endif
                        
                        <!-- Member Count Badge -->
                        <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded-full">
                            <i class="ti ti-users"></i> {{ $group->member_count ?? 0 }} members
                        </div>
                    </div>
                    
                    <!-- Group Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $group->name }}</h3>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ Str::limit($group->description, 80) }}</p>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-gray-400">
                                Created by {{ $group->creator->full_name ?? 'Admin' }}
                            </div>
                            
                            @auth
                                @if(in_array($group->id, $myGroups ?? []))
                                    <a href="{{ route('community.show', $group) }}" 
                                       class="bg-green-600 text-white px-3 py-1 rounded-lg hover:bg-green-700 transition text-sm">
                                        <i class="ti ti-message-circle"></i> Open
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('community.join', $group) }}">
                                        @csrf
                                        <button type="submit" class="bg-jlibrary-600 text-white px-3 py-1 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                                            <i class="ti ti-user-plus"></i> Join
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="text-jlibrary-600 text-sm">Login to Join</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $groups->withQueryString()->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No groups found</h3>
            <p class="text-gray-500">Be the first to create a group!</p>
            @auth
                <a href="{{ route('community.create') }}" class="inline-block mt-4 text-jlibrary-600 hover:text-jlibrary-700">
                    Create a Group →
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection