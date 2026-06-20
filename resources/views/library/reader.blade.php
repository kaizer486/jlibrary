@extends('layouts.app')

@section('title', 'Reading: ' . $book->title)

@section('content')
<div class="min-h-screen bg-gray-100">
    
    <!-- Reader Header -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-10">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('library.show', $book) }}" class="text-gray-600 hover:text-purple-600 transition">
                        <i class="ti ti-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h1 class="font-semibold text-gray-800">{{ $book->title }}</h1>
                        <p class="text-sm text-gray-500">by {{ $book->author }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Progress Indicator -->
                    <div class="text-sm text-gray-600">
                        Page <span id="currentPageDisplay">{{ $currentPage ?? 0 }}</span> of {{ $book->total_pages }}
                    </div>
                    
                    <!-- Download Button -->
                    <a href="{{ route('library.download', $book) }}" class="bg-purple-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-purple-700 transition">
                        <i class="ti ti-download"></i> Download
                    </a>
                    
                    <!-- Fullscreen Toggle -->
                    <button onclick="toggleFullscreen()" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm hover:bg-gray-300 transition">
                        <i class="ti ti-arrows-maximize"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PDF Viewer -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <iframe id="pdfFrame"
                    src="{{ route('library.pdf', $book) }}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
                    class="w-full"
                    style="height: calc(100vh - 120px); min-height: 600px;">
            </iframe>
        </div>
    </div>
    
    <!-- Progress Save Overlay (shows when page changes) -->
    <div id="savedNotification" class="fixed bottom-6 right-6 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg opacity-0 transition-opacity pointer-events-none">
        <i class="ti ti-check"></i> Progress saved
    </div>
</div>

<script>
    let currentPage = {{ $currentPage ?? 0 }};
    let totalPages = {{ $book->total_pages }};
    let saveTimeout;
    
    // Get the iframe element
    const iframe = document.getElementById('pdfFrame');
    
    // Function to save progress
    function saveProgress(page) {
        if (saveTimeout) clearTimeout(saveTimeout);
        
        saveTimeout = setTimeout(() => {
            fetch('{{ route("library.progress", $book) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    page: page,
                    total_pages: totalPages
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show notification
                    const notification = document.getElementById('savedNotification');
                    notification.classList.remove('opacity-0');
                    notification.classList.add('opacity-100');
                    
                    setTimeout(() => {
                        notification.classList.remove('opacity-100');
                        notification.classList.add('opacity-0');
                    }, 2000);
                    
                    // Update current page display
                    document.getElementById('currentPageDisplay').innerText = page;
                }
            });
        }, 1000);
    }
    
    // Listen for page changes from the PDF iframe
    // Note: This requires PDF.js or a compatible viewer
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'pageChange') {
            currentPage = event.data.page;
            saveProgress(currentPage);
        }
    });
    
    // Alternative: Manual progress update
    function updatePageManually() {
        let page = prompt('Enter your current page number:', currentPage);
        if (page && page >= 1 && page <= totalPages) {
            currentPage = parseInt(page);
            saveProgress(currentPage);
            
            // Try to scroll to page in iframe
            try {
                iframe.contentWindow.postMessage({
                    type: 'goToPage',
                    page: currentPage
                }, '*');
            } catch(e) {
                console.log('Cannot control PDF viewer');
            }
        }
    }
    
    // Fullscreen toggle
    function toggleFullscreen() {
        const elem = document.documentElement;
        if (!document.fullscreenElement) {
            elem.requestFullscreen();
        } else {
            document.exitFullscreen();
        }
    }
    
    // Save progress when user leaves the page
    window.addEventListener('beforeunload', function() {
        if (currentPage > 0) {
            navigator.sendBeacon('{{ route("library.progress", $book) }}', new Blob([JSON.stringify({
                page: currentPage,
                total_pages: totalPages
            })], {type: 'application/json'}));
        }
    });
</script>

<style>
    /* Fullscreen styles */
    :-webkit-full-screen #pdfFrame {
        height: 100vh;
    }
    
    :-moz-full-screen #pdfFrame {
        height: 100vh;
    }
    
    :fullscreen #pdfFrame {
        height: 100vh;
    }
</style>
@endsection