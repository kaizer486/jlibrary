@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <i class="ti ti-building-community text-purple-400 text-3xl"></i>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Discover Institutions</h1>
            </div>
            <div class="w-16 h-1 bg-yellow-400 rounded-full mb-2"></div>
            <p class="text-gray-300">Browse institutions you can join</p>
        </div>
        
        <!-- Institutions Grid -->
        @if($institutions->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($institutions as $institution)
            <div class="group bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
                <div class="p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center">
                            <i class="ti ti-building text-indigo-600 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">{{ Str::limit($institution->name, 25) }}</h3>
                            <span class="text-xs text-gray-500">{{ $institution->type_label ?? 'Institution' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 text-xs text-gray-500 mb-2">
                        <span class="flex items-center gap-1"><i class="ti ti-users"></i> {{ $institution->users_count ?? 0 }} members</span>
                        <span class="flex items-center gap-1"><i class="ti ti-books"></i> {{ $institution->books_count ?? 0 }} books</span>
                    </div>
                    
                    <p class="text-gray-500 text-xs line-clamp-2 mb-3">
                        {{ $institution->city ?? 'Location not specified' }}
                    </p>
                    
                    <a href="{{ route('institution.show', $institution->id) }}" 
                       class="w-full block text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-medium px-3 py-2 rounded-lg transition text-sm">
                        <i class="ti ti-eye"></i> View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $institutions->links() }}
        </div>
        
        @else
        <div class="bg-white/10 rounded-2xl p-12 text-center">
            <i class="ti ti-building-community text-5xl text-gray-400 mb-3 block"></i>
            <h3 class="text-xl font-semibold text-white mb-2">No Institutions Available</h3>
            <p class="text-gray-300">Check back later for new institutions</p>
        </div>
        @endif
        
              <!-- Bottom Buttons - Aligned to the LEFT (start) -->
        <div class="flex justify-start gap-4 py-6">
            <!-- Back to Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-dashboard"></i> Back to Dashboard
            </a>
            
            <!-- Back to Top -->
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-6 py-3 rounded-full transition">
                <i class="ti ti-arrow-up"></i> Back to Top
            </button>
        </div>
        
    </div>
</div>
@endsection