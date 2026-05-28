@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Certificates</h1>
        <p class="text-gray-600">Certificates you've earned by completing book quizzes</p>
    </div>
    
    @if($certificates->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($certificates as $cert)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-4 text-white text-center">
                        <i class="ti ti-certificate text-4xl mb-2 block"></i>
                        <h3 class="font-bold text-lg">{{ $cert->book->title }}</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-2 text-sm mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Certificate No:</span>
                                <span class="font-mono text-xs">{{ $cert->certificate_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Score:</span>
                                <span class="font-semibold text-green-600">{{ $cert->quiz_score }}/{{ $cert->total_questions }} ({{ $cert->percentage }}%)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Issued:</span>
                                <span>{{ $cert->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('certificates.show', $cert) }}" 
                               class="flex-1 text-center bg-jlibrary-600 text-white px-3 py-2 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                                <i class="ti ti-eye"></i> View
                            </a>
                            <a href="{{ route('certificates.download', $cert) }}" 
                               class="flex-1 text-center border border-jlibrary-600 text-jlibrary-600 px-3 py-2 rounded-lg hover:bg-jlibrary-600 hover:text-white transition text-sm">
                                <i class="ti ti-download"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-certificate text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Certificates Yet</h3>
            <p class="text-gray-500 mb-4">Complete book quizzes with 70% or higher to earn certificates.</p>
            <a href="{{ route('library.index') }}" class="inline-block bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                Browse Books
            </a>
        </div>
    @endif
</div>
@endsection