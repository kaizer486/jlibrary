@extends('layouts.app')

@section('title', 'Certificate - ' . $certificate->certificate_number)

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('certificates.index') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-arrow-left"></i> Back to Certificates
            </a>
        </div>

        <!-- Certificate Display -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden p-8 md:p-12">
            <!-- Certificate Content -->
            <div class="text-center border-4 border-double border-purple-300 p-8 md:p-12 relative">
                <!-- Decorative Border -->
                <div class="absolute inset-2 border border-purple-200 pointer-events-none"></div>
                
                <!-- Logo -->
                <div class="flex justify-center items-center gap-3 mb-6">
                    <img src="{{ asset('images/jlibrary.jpeg') }}" alt="JLIBRARY" class="h-12 w-auto">
                    <span class="text-2xl font-bold text-purple-700">JLIBRARY</span>
                </div>
                
                <p class="text-sm text-gray-500 italic">Learn. Share. Grow Together.</p>
                <p class="text-xs text-gray-400 mb-8">Education Empowers All.</p>
                
                <hr class="border-purple-200 my-4">
                
                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-serif font-bold text-purple-800 my-6">
                    JLIBRARY LEARNING CERTIFICATE
                </h1>
                
                <!-- Body -->
                <p class="text-gray-600 text-lg">This is to certify that</p>
                <h2 class="text-2xl md:text-3xl font-bold text-purple-700 my-3">
                    {{ $certificate->user->full_name }}
                </h2>
                
                <p class="text-gray-600 text-lg mt-4">
                    has successfully completed reading
                </p>
                <h3 class="text-xl md:text-2xl font-bold text-purple-600 my-2">
                    "{{ $certificate->book->title ?? 'Book' }}"
                </h3>
                <p class="text-gray-600 text-lg">
                    written by <span class="font-semibold">{{ $certificate->book->author ?? 'Unknown' }}</span>
                </p>
                
                <p class="text-gray-600 text-lg mt-4">
                    through JLIBRARY and has met all requirements for certification.
                </p>
                
                <!-- Score -->
                <div class="my-6 inline-block bg-purple-50 px-6 py-3 rounded-lg border border-purple-200">
                    <p class="text-sm text-gray-600">Quiz Score</p>
                    <p class="text-2xl font-bold text-purple-700">
                        {{ $certificate->quiz_score }}/{{ $certificate->total_questions }}
                        <span class="text-lg font-normal text-gray-500">({{ $certificate->percentage }}%)</span>
                    </p>
                </div>
                
                <hr class="border-purple-200 my-6">
                
                <!-- Footer -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center text-sm text-gray-500 mt-6">
                    <div>
                        <p class="font-semibold">Date</p>
                        <p>{{ $certificate->created_at->format('d / m / Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">Certificate ID</p>
                        <p>{{ $certificate->certificate_number }}</p>
                    </div>
                </div>
                
                <!-- Signature -->
                <div class="mt-8 pt-4 border-t border-gray-200 flex justify-between items-end">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">JLIBRARY Administration</p>
                        <p class="text-xs text-gray-400">Authorized Signature</p>
                    </div>
                    <div class="text-right">
                        <div class="w-32 h-12 border-b-2 border-purple-400 mx-auto"></div>
                        <p class="text-xs text-gray-400 mt-1">Signature</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('certificates.download', $certificate) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
                <i class="ti ti-download"></i> Download PDF
            </a>
            <a href="{{ route('certificates.index') }}" 
               class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition flex items-center gap-2">
                <i class="ti ti-arrow-left"></i> All Certificates
            </a>
        </div>
        
    </div>
</div>
@endsection