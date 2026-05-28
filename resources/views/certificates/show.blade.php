@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('certificates.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700">
                <i class="ti ti-arrow-left"></i> Back to Certificates
            </a>
        </div>
        
        <!-- Certificate Preview -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-8 text-white text-center">
                <i class="ti ti-certificate text-6xl mb-3 block"></i>
                <h1 class="text-3xl font-bold">Certificate of Completion</h1>
                <p class="text-yellow-100 mt-2">This certificate is proudly presented to</p>
            </div>
            
            <div class="p-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $certificate->user->full_name }}</h2>
                <p class="text-gray-600 mb-6">For successfully completing</p>
                
                <div class="border-t border-b border-gray-200 py-6 my-4">
                    <h3 class="text-2xl font-semibold text-jlibrary-600">{{ $certificate->book->title }}</h3>
                    <p class="text-gray-500">by {{ $certificate->book->author }}</p>
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-gray-500">Score</p>
                        <p class="font-semibold text-lg">{{ $certificate->quiz_score }}/{{ $certificate->total_questions }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Percentage</p>
                        <p class="font-semibold text-lg text-green-600">{{ $certificate->percentage }}%</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Date</p>
                        <p class="font-semibold text-sm">{{ $certificate->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
                
                <div class="text-center text-gray-400 text-xs mt-6 pt-4 border-t">
                    <p>Certificate Number: {{ $certificate->certificate_number }}</p>
                    <p class="mt-1">JLIBRARY - Learn. Share. Grow Together.</p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="{{ route('certificates.download', $certificate) }}" 
               class="inline-flex items-center gap-2 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                <i class="ti ti-download"></i>
                Download PDF Certificate
            </a>
        </div>
    </div>
</div>
@endsection