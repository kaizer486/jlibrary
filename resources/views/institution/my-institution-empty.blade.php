@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen flex items-center justify-center">
    <div class="text-center max-w-md mx-auto px-4">
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-building-community text-3xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white mb-2">No Institution Yet</h2>
            <p class="text-gray-300 mb-6">You haven't joined any institution. Discover and join one to access exclusive resources!</p>
            <a href="{{ route('discover.institutions') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="ti ti-building-community"></i> Discover Institutions
            </a>
        </div>
    </div>
</div>
@endsection