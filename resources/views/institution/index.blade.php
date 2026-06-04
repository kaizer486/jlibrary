@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-7xl">
        
        <!-- Header with Yellow Line (like your photo) -->
        <div class="mb-8">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="ti ti-building-community text-purple-400 text-3xl"></i>
                        <h1 class="text-3xl md:text-4xl font-bold text-white">All Institutions</h1>
                    </div>
                    <div class="w-20 h-1 bg-yellow-400 rounded-full mb-3"></div>
                    <p class="text-gray-300">Browse and connect with learning communities worldwide</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full transition">
                    <i class="ti ti-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
        
        <!-- Institutions Grid -->
        @if($institutions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($institutions as $institution)
            <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <!-- Colored top accent -->
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                
                <div class="p-5">
                    <!-- Icon & Name -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center">
                                <i class="ti ti-building text-indigo-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">{{ Str::limit($institution->name, 25) }}</h3>
                                <span class="text-xs text-gray-500">{{ $institution->type_label ?? 'Institution' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats -->
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-3">
                        <div class="flex items-center gap-1">
                            <i class="ti ti-users"></i>
                            <span>{{ $institution->users_count ?? 0 }} members</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="ti ti-books"></i>
                            <span>{{ $institution->books_count ?? 0 }} books</span>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                        {{ $institution->description ?? ($institution->city ? 'Located in ' . $institution->city : 'Join this institution to access exclusive learning resources.') }}
                    </p>
                    
                    <!-- View Details Button -->
                    <a href="{{ route('institution.show', $institution->id) }}" 
                       class="w-full block text-center bg-gray-100 hover:bg-indigo-50 text-indigo-700 font-medium px-4 py-2.5 rounded-xl transition text-sm border border-gray-200 hover:border-indigo-200">
                        <i class="ti ti-eye"></i> View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $institutions->links() }}
        </div>
        
        @else
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-12 text-center border border-white/20">
            <i class="ti ti-building text-5xl text-gray-400 mb-3 block"></i>
            <h3 class="text-xl font-semibold text-white mb-2">No Institutions Available</h3>
            <p class="text-gray-300">Check back later for institutions to join</p>
        </div>
        @endif
        
    </div>
</div>
@endsection