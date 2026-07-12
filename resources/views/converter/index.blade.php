@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">File Converter</h1>
        <p class="text-gray-600">Convert your documents between different formats</p>
    </div>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <i class="ti ti-alert-circle"></i> {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <i class="ti ti-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- PDF to Word -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition" x-data="{ loading: false, fileName: '' }">
            <div class="text-center mb-4">
                <i class="ti ti-file-pdf text-5xl text-red-500"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-file-word text-5xl text-blue-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">PDF to Word</h2>
            <form method="POST" action="{{ route('converter.pdf-to-word') }}" enctype="multipart/form-data" @submit="loading = true">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".pdf" required class="hidden" id="pdf-input" @change="fileName = $event.target.files[0]?.name || ''">
                    <label for="pdf-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500" x-text="fileName || 'Click to upload PDF'"></span>
                        <p class="text-xs text-gray-400 mt-1">Max 10MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition flex items-center justify-center gap-2" :disabled="loading">
                    <i class="ti ti-upload" x-show="!loading"></i>
                    <i class="ti ti-loader animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Converting...' : 'Convert to Word'"></span>
                </button>
            </form>
        </div>
        
        <!-- Word to PDF -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition" x-data="{ loading: false, fileName: '' }">
            <div class="text-center mb-4">
                <i class="ti ti-file-word text-5xl text-blue-500"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-file-pdf text-5xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">Word to PDF</h2>
            <form method="POST" action="{{ route('converter.word-to-pdf') }}" enctype="multipart/form-data" @submit="loading = true">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".doc,.docx" required class="hidden" id="word-input" @change="fileName = $event.target.files[0]?.name || ''">
                    <label for="word-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500" x-text="fileName || 'Click to upload Word'"></span>
                        <p class="text-xs text-gray-400 mt-1">Max 10MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition flex items-center justify-center gap-2" :disabled="loading">
                    <i class="ti ti-upload" x-show="!loading"></i>
                    <i class="ti ti-loader animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Converting...' : 'Convert to PDF'"></span>
                </button>
            </form>
        </div>
        
        <!-- Book to Audio -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition" x-data="{ loading: false, fileName: '' }">
            <div class="text-center mb-4">
                <i class="ti ti-book text-5xl text-jlibrary-600"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-headphone text-5xl text-green-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">Book to Audio</h2>
            <form method="POST" action="{{ route('converter.book-to-audio') }}" enctype="multipart/form-data" @submit="loading = true">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".pdf,.txt" required class="hidden" id="audio-input" @change="fileName = $event.target.files[0]?.name || ''">
                    <label for="audio-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500" x-text="fileName || 'Click to upload book'"></span>
                        <p class="text-xs text-gray-400 mt-1">PDF or TXT, Max 20MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2" :disabled="loading">
                    <i class="ti ti-upload" x-show="!loading"></i>
                    <i class="ti ti-loader animate-spin" x-show="loading"></i>
                    <span x-text="loading ? 'Converting...' : 'Convert to Audio'"></span>
                </button>
            </form>
        </div>
    </div>
    
   
</div>

@push('scripts')
<script>
    // Alpine.js is already included in your layout
    // The x-data attributes handle the loading states
</script>
@endpush

@endsection