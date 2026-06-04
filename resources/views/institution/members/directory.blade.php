@extends('layouts.app')

@section('title', 'Institution Members')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="text-purple-300 hover:text-purple-200 transition mb-4 inline-block">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <i class="ti ti-building text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ $institution->name }}</h1>
                        <p class="text-purple-200">Institution Members Directory</p>
                    </div>
                    <div class="ml-auto">
                        <div class="bg-white/20 rounded-full px-4 py-2 text-white">
                            <i class="ti ti-users"></i> {{ $institution->users()->count() }} Members
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members by Role -->
        <div class="space-y-6">
            
            <!-- Institution Admins -->
            @if(isset($members['institution_admin']) || isset($members['admin']))
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-3">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-shield"></i> Institution Administrators
                    </h2>
                </div>
                <div class="p-4 divide-y divide-gray-100">
                    @foreach(($members['institution_admin'] ?? [])->merge($members['admin'] ?? []) as $member)
                    <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">Admin</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Librarians -->
            @if(isset($members['librarian']))
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-3">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-book"></i> Librarians
                    </h2>
                </div>
                <div class="p-4 divide-y divide-gray-100">
                    @foreach($members['librarian'] as $member)
                    <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Librarian</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Instructors -->
            @if(isset($members['instructor']))
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-school"></i> Instructors
                    </h2>
                </div>
                <div class="p-4 divide-y divide-gray-100">
                    @foreach($members['instructor'] as $member)
                    <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Instructor</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Members -->
            @if(isset($members['user']))
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-3">
                    <h2 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-users"></i> Members
                    </h2>
                </div>
                <div class="p-4 divide-y divide-gray-100">
                    @foreach($members['user'] as $member)
                    <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-xl transition">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-500 to-gray-600 flex items-center justify-center">
                            <span class="text-white font-bold">{{ substr($member->full_name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $member->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded-full">Member</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection