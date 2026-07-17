@extends('layouts.app')

@section('title', 'Certificate - ' . $certificate->certificate_number)

@section('content')
<div class="fixed inset-0 bg-gradient-to-br from-slate-800 via-slate-900 to-indigo-900 -z-10"></div>

<div class="relative z-10 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('certificates.index') }}" class="text-gray-400 hover:text-white transition">
                <i class="ti ti-arrow-left"></i> Back to Certificates
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Certificate Preview ONLY -->
            <div class="p-4 bg-gray-100">
                <div class="relative" style="background: #f8f9fa;">
                    @php
                        $filePath = public_path('media/' . $certificate->file_path);
                    @endphp
                    
                    @if(file_exists($filePath))
                        <embed src="{{ url('media/' . $certificate->file_path) }}" 
                               type="application/pdf"
                               class="w-full rounded-lg shadow-lg"
                               style="min-height: 500px; height: 70vh;">
                    @else
                        <div class="text-center py-12">
                            <i class="ti ti-file-pdf text-6xl text-red-500 mb-4 block"></i>
                            <p class="text-gray-600">PDF file not found.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Download Button ONLY -->
            <div class="p-6 text-center">
                <a href="{{ route('certificates.download', $certificate) }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-lg transition flex items-center gap-2 justify-center">
                    <i class="ti ti-download"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection