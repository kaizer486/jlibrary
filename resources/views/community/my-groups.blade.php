@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Groups</h1>
            <p class="text-gray-600">Groups you're a member of</p>
        </div>
        <a href="{{ route('community.create') }}" class="bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
            <i class="ti ti-plus"></i> New Group
        </a>
    </div>
    
    @if($myGroups->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myGroups as $group)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="h-32 bg-gradient-to-r from-jlibrary-500 to-jlibrary-700 relative">
                        @if($group->cover_image)
                            <img src="{{ Storage::url($group->cover_image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="ti ti-users text-5xl text-white/50"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900">{{ $group->name }}</h3>
                        <p class="text-gray-500 text-sm mb-3 line-clamp-2">{{ Str::limit($group->description, 80) }}</p>
                        <a href="{{ route('community.show', $group) }}" 
                           class="inline-block bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                            Open Group
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-users text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">You haven't joined any groups yet</h3>
            <p class="text-gray-500">Join a group to start discussing with others</p>
            <a href="{{ route('community.index') }}" class="inline-block mt-4 text-jlibrary-600 hover:text-jlibrary-700">
                Browse Groups →
            </a>
        </div>
    @endif
</div>
@endsection