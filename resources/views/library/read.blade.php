@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    <!-- Book Header -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $book->title }}</h1>
                <p class="text-gray-600">by {{ $book->author }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('library.download', $book) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    <i class="ti ti-download"></i> Download PDF
                </a>
                <a href="{{ route('library.show', $book) }}" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                    <i class="ti ti-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>
    
    <!-- Reading Progress -->
    @php
        $userBook = $book->users()->where('user_id', auth()->id())->first();
        $progressPercent = $userBook ? $userBook->pivot->progress_percent : 0;
        $currentPage = $userBook ? $userBook->pivot->current_page : 0;
    @endphp
    
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-gray-800">Reading Progress</h3>
            <span class="text-sm text-gray-500">{{ $progressPercent }}% Complete</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-3 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
        </div>
        <div class="flex justify-between mt-3 text-sm text-gray-500">
            <span>Page {{ $currentPage }} of {{ $book->total_pages }}</span>
            <span>{{ $book->total_pages - $currentPage }} pages remaining</span>
        </div>
    </div>
    
    <!-- PDF Viewer / Book Content -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        @if($book->file_path && file_exists(storage_path('app/public/' . $book->file_path)))
            <div class="p-4 bg-gray-50 border-b">
                <p class="text-sm text-gray-600 text-center">
                    <i class="ti ti-file-pdf text-red-500"></i> 
                    PDF Document - {{ $book->total_pages }} pages
                </p>
            </div>
            
            <!-- Embedded PDF Viewer -->
            <iframe src="{{ asset('storage/' . $book->file_path) }}#toolbar=1&navpanes=1&scrollbar=1"
                    class="w-full h-[80vh]"
                    style="min-height: 600px;">
            </iframe>
        @else
            <!-- Sample text content when PDF is not available -->
            <div class="p-8 prose max-w-none">
                <div class="text-center py-12">
                    <i class="ti ti-file-text text-6xl text-gray-300 mb-4 block"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Preview Mode</h3>
                    <p class="text-gray-500 mb-6">The full PDF will be available after purchase.</p>
                    
                    @if(!$book->is_paid || ($book->is_paid && $book->isPurchasedByUser(auth()->id())))
                        <a href="{{ route('library.download', $book) }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-flex items-center gap-2">
                            <i class="ti ti-download"></i> Download PDF
                        </a>
                    @else
                        <a href="{{ route('library.show', $book) }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-flex items-center gap-2">
                            <i class="ti ti-shopping-cart"></i> Purchase to Read
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <!-- Page Navigation (for manual progress update) -->
    <div class="fixed bottom-6 right-6">
        <button onclick="updateProgress()" class="bg-purple-600 text-white p-3 rounded-full shadow-lg hover:bg-purple-700 transition">
            <i class="ti ti-device-floppy text-xl"></i>
        </button>
    </div>
</div>

<script>
let currentPage = {{ $currentPage }};
let totalPages = {{ $book->total_pages }};

function updateProgress() {
    let page = prompt('Enter your current page number:', currentPage);
    if (page && !isNaN(page) && page >= 1 && page <= totalPages) {
        fetch('{{ route("library.progress", $book) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                page: page,
                total_pages: totalPages
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Progress saved!');
                location.reload();
            }
        });
    }
}
</script>
@endsection