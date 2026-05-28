@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">File Converter</h1>
        <p class="text-gray-600">Convert your documents between different formats</p>
    </div>
    
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6">
            <i class="ti ti-info-circle"></i> {{ session('info') }}
        </div>
    @endif
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- PDF to Word -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition">
            <div class="text-center mb-4">
                <i class="ti ti-file-pdf text-5xl text-red-500"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-file-word text-5xl text-blue-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">PDF to Word</h2>
            <form method="POST" action="{{ route('converter.pdf-to-word') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".pdf" required class="hidden" id="pdf-input">
                    <label for="pdf-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500">Click to upload PDF</span>
                        <p class="text-xs text-gray-400 mt-1">Max 10MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    Convert to Word
                </button>
            </form>
        </div>
        
        <!-- Word to PDF -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition">
            <div class="text-center mb-4">
                <i class="ti ti-file-word text-5xl text-blue-500"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-file-pdf text-5xl text-red-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">Word to PDF</h2>
            <form method="POST" action="{{ route('converter.word-to-pdf') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".doc,.docx" required class="hidden" id="word-input">
                    <label for="word-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500">Click to upload Word</span>
                        <p class="text-xs text-gray-400 mt-1">Max 10MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                    Convert to PDF
                </button>
            </form>
        </div>
        
        <!-- Book to Audio -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg transition">
            <div class="text-center mb-4">
                <i class="ti ti-book text-5xl text-jlibrary-600"></i>
                <i class="ti ti-arrow-right text-2xl text-gray-400 mx-2"></i>
                <i class="ti ti-headphone text-5xl text-green-500"></i>
            </div>
            <h2 class="text-xl font-semibold text-center mb-4">Book to Audio</h2>
            <form method="POST" action="{{ route('converter.book-to-audio') }}" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-jlibrary-500 transition mb-3">
                    <input type="file" name="file" accept=".pdf,.txt" required class="hidden" id="audio-input">
                    <label for="audio-input" class="cursor-pointer block">
                        <i class="ti ti-upload text-3xl text-gray-400 mb-2 block"></i>
                        <span class="text-gray-500">Click to upload book</span>
                        <p class="text-xs text-gray-400 mt-1">PDF or TXT, Max 20MB</p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Convert to Audio
                </button>
            </form>
        </div>
    </div>
    
    <!-- Coming Soon Banner -->
    <div class="mt-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 text-center">
        <div class="flex items-center justify-center gap-6 flex-wrap">
            <div class="text-center">
                <i class="ti ti-file-text text-2xl text-gray-600"></i>
                <p class="text-sm text-gray-500 mt-1">EPUB to PDF</p>
                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>
            <div class="text-center">
                <i class="ti ti-image text-2xl text-gray-600"></i>
                <p class="text-sm text-gray-500 mt-1">Image to Text</p>
                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>
            <div class="text-center">
                <i class="ti ti-file-zip text-2xl text-gray-600"></i>
                <p class="text-sm text-gray-500 mt-1">Batch Convert</p>
                <span class="text-xs bg-gray-200 px-2 py-0.5 rounded">Soon</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Display file name when selected
    document.getElementById('pdf-input')?.addEventListener('change', function(e) {
        if (e.target.files[0]) {
            alert('Selected: ' + e.target.files[0].name);
        }
    });
    document.getElementById('word-input')?.addEventListener('change', function(e) {
        if (e.target.files[0]) {
            alert('Selected: ' + e.target.files[0].name);
        }
    });
    document.getElementById('audio-input')?.addEventListener('change', function(e) {
        if (e.target.files[0]) {
            alert('Selected: ' + e.target.files[0].name);
        }
    });
</script>
@endsection