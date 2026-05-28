@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-8">
        <a href="{{ route('documents.index') }}" class="text-jlibrary-600 hover:text-jlibrary-700 mb-4 inline-block">
            <i class="ti ti-arrow-left"></i> Back to Documents
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Upload Document</h1>
        <p class="text-gray-600">Upload a PDF, Word document, or text file to analyze with AI</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('documents.upload') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Document Title *</label>
                <input type="text" name="title" id="title" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-jlibrary-500"
                       placeholder="e.g., My Research Paper, Course Notes, Book Chapter">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="document" class="block text-sm font-medium text-gray-700 mb-1">Document File *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-jlibrary-500 transition">
                    <input type="file" name="document" id="document" required
                           accept=".pdf,.txt,.docx"
                           class="hidden">
                    <i class="ti ti-upload text-4xl text-gray-400 mb-2 block"></i>
                    <p class="text-gray-500">Click or drag to upload</p>
                    <p class="text-xs text-gray-400 mt-1">PDF, DOCX, TXT (Max 10MB)</p>
                    <button type="button" onclick="document.getElementById('document').click()"
                            class="mt-3 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                        Select File
                    </button>
                </div>
                <p id="file-name" class="text-sm text-gray-500 mt-2 hidden"></p>
                @error('document')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-blue-800 mb-2">What happens after upload?</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>📄 The document will be processed and analyzed</li>
                    <li>🤖 You can ask questions about the document content</li>
                    <li>💡 The AI will answer based ONLY on document information</li>
                    <li>🔒 Your documents are private to your account</li>
                </ul>
            </div>
            
            <button type="submit" class="w-full bg-jlibrary-600 text-white px-6 py-3 rounded-lg hover:bg-jlibrary-700 transition font-semibold">
                <i class="ti ti-cloud-upload"></i> Upload and Process Document
            </button>
        </form>
    </div>
</div>

<script>
    document.getElementById('document').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileNameDisplay = document.getElementById('file-name');
        if (fileName) {
            fileNameDisplay.textContent = 'Selected: ' + fileName;
            fileNameDisplay.classList.remove('hidden');
        }
    });
</script>
@endsection