@extends('layouts.app')

@section('title', 'My Certificates')

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">🏆 My Certificates</h1>
                <p class="text-gray-400 mt-1">Certificates earned from book completions</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        @if($certificates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($certificates as $cert)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl overflow-hidden border border-white/10 hover:border-purple-500/30 transition group">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                        <i class="ti ti-certificate text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-semibold text-sm">
                                            {{ $cert->book->title ?? 'Certificate' }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $cert->certificate_number }}</p>
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-400">
                                    {{ $cert->percentage }}%
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-400">
                                <span><i class="ti ti-calendar"></i> {{ $cert->created_at->format('M d, Y') }}</span>
                                <span><i class="ti ti-user"></i> {{ $cert->user->full_name }}</span>
                            </div>
                            
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('certificates.show', $cert) }}" 
                                   class="flex-1 text-center bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-lg text-sm transition">
                                    <i class="ti ti-eye"></i> View
                                </a>
                                <a href="{{ route('certificates.download', $cert) }}" 
                                   class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-sm transition">
                                    <i class="ti ti-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-12 text-center border border-white/10">
                <i class="ti ti-certificate text-5xl text-gray-500 mb-4 block"></i>
                <h3 class="text-xl font-semibold text-white mb-2">No Certificates Yet</h3>
                <p class="text-gray-400">Complete books to earn certificates.</p>
                <a href="{{ route('library.index') }}" class="inline-block mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg transition">
                    <i class="ti ti-books"></i> Browse Books
                </a>
            </div>
        @endif
    </div>
</div>
@endsection