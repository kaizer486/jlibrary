@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Documents</h1>
            <p class="text-gray-600">Upload PDFs, Word docs, or text files to analyze with AI</p>
        </div>
        <a href="{{ route('documents.create') }}" class="bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
            <i class="ti ti-upload"></i> Upload Document
        </a>
    </div>
    
    @if($documents->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($documents as $doc)
                <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                        <div class="flex items-center justify-between">
                            <i class="ti ti-file-text text-3xl"></i>
                            <span class="text-xs bg-white/20 px-2 py-1 rounded">
                                {{ strtoupper(pathinfo($doc->file_path, PATHINFO_EXTENSION)) }}
                            </span>
                        </div>
                        <h3 class="font-bold text-lg mt-2 line-clamp-1">{{ $doc->title }}</h3>
                    </div>
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-3">
                            <div class="flex justify-between mb-1">
                                <span>Size:</span>
                                <span>{{ number_format($doc->file_size / 1024, 2) }} KB</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Uploaded:</span>
                                <span>{{ $doc->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('documents.chat', $doc) }}" 
                               class="flex-1 text-center bg-jlibrary-600 text-white px-3 py-2 rounded-lg hover:bg-jlibrary-700 transition text-sm">
                                <i class="ti ti-message-circle"></i> Chat with Doc
                            </a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center border border-red-500 text-red-500 px-3 py-2 rounded-lg hover:bg-red-500 hover:text-white transition text-sm"
                                        onclick="return confirm('Delete this document?')">
                                    <i class="ti ti-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <i class="ti ti-file-text text-6xl text-gray-400 mb-4 block"></i>
            <h3 class="text-xl font-semibold mb-2">No Documents Yet</h3>
            <p class="text-gray-500 mb-4">Upload PDFs, Word documents, or text files to analyze with AI</p>
            <a href="{{ route('documents.create') }}" class="inline-block bg-jlibrary-600 text-white px-6 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                Upload Your First Document
            </a>
        </div>
    @endif
</div>
@endsection