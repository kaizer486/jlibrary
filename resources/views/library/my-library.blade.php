@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Library</h1>
        <p class="text-gray-600">Track your reading journey</p>
    </div>
    
    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <button onclick="showTab('reading')" id="tab-reading" class="tab-btn pb-2 px-1 font-medium text-purple-600 border-b-2 border-purple-600">Currently Reading</button>
            <button onclick="showTab('completed')" id="tab-completed" class="tab-btn pb-2 px-1 font-medium text-gray-500 hover:text-gray-700">Completed</button>
            <button onclick="showTab('want')" id="tab-want" class="tab-btn pb-2 px-1 font-medium text-gray-500 hover:text-gray-700">Want to Read</button>
            <button onclick="showTab('purchased')" id="tab-purchased" class="tab-btn pb-2 px-1 font-medium text-gray-500 hover:text-gray-700">Purchased</button>
        </nav>
    </div>
    
    <!-- Reading Tab -->
    <div id="reading-tab" class="tab-content">
        @if(isset($reading) && $reading->count() > 0)
            <div class="space-y-4">
                @foreach($reading as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                        <div class="w-16 h-20 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="ti ti-book text-2xl text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $item->book->title }}</h3>
                            <p class="text-gray-500 text-sm">{{ $item->book->author }}</p>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $item->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $item->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('library.read', $item->book) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                            Continue Reading
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl p-12 text-center">
                <i class="ti ti-book text-6xl text-gray-400 mb-4 block"></i>
                <p class="text-gray-500">No books in your reading list yet</p>
                <a href="{{ route('library.index') }}" class="inline-block mt-4 text-purple-600">Browse Library</a>
            </div>
        @endif
    </div>
    
    <!-- Completed Tab -->
    <div id="completed-tab" class="tab-content hidden">
        @if(isset($completed) && $completed->count() > 0)
            <div class="space-y-4">
                @foreach($completed as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-4">
                        <div class="w-16 h-20 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="ti ti-certificate text-2xl text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $item->book->title }}</h3>
                            <p class="text-gray-500 text-sm">{{ $item->book->author }}</p>
                            <div class="mt-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Completed</span>
                                    <span>{{ $item->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $item->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('certificates.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            View Certificate
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl p-12 text-center">
                <i class="ti ti-certificate text-6xl text-gray-400 mb-4 block"></i>
                <p class="text-gray-500">No completed books yet</p>
                <a href="{{ route('library.index') }}" class="inline-block mt-4 text-purple-600">Start Reading</a>
            </div>
        @endif
    </div>
    
    <!-- Want to Read Tab -->
    <div id="want-tab" class="tab-content hidden">
        @if(isset($wantToRead) && $wantToRead->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($wantToRead as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900">{{ $item->book->title }}</h3>
                        <p class="text-gray-500 text-sm">{{ $item->book->author }}</p>
                        <div class="mt-3">
                            <a href="{{ route('library.show', $item->book) }}" class="text-purple-600 text-sm hover:underline">View Details →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl p-12 text-center">
                <i class="ti ti-bookmark text-6xl text-gray-400 mb-4 block"></i>
                <p class="text-gray-500">No books in your wishlist</p>
                <a href="{{ route('library.index') }}" class="inline-block mt-4 text-purple-600">Browse Library</a>
            </div>
        @endif
    </div>
    
    <!-- Purchased Tab -->
    <div id="purchased-tab" class="tab-content hidden">
        @if(isset($purchased) && $purchased->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($purchased as $book)
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <h3 class="font-semibold text-gray-900">{{ $book->title }}</h3>
                        <p class="text-gray-500 text-sm">{{ $book->author }}</p>
                        <div class="mt-3">
                            <a href="{{ route('library.read', $book) }}" class="text-purple-600 text-sm hover:underline">Start Reading →</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl p-12 text-center">
                <i class="ti ti-shopping-cart text-6xl text-gray-400 mb-4 block"></i>
                <p class="text-gray-500">No purchased books yet</p>
                <a href="{{ route('marketplace.index') }}" class="inline-block mt-4 text-purple-600">Visit Marketplace</a>
            </div>
        @endif
    </div>
</div>

<script>
    function showTab(tab) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('text-purple-600', 'border-purple-600');
            el.classList.add('text-gray-500');
        });
        
        // Show selected tab
        document.getElementById(tab + '-tab').style.display = 'block';
        document.getElementById('tab-' + tab).classList.add('text-purple-600', 'border-b-2', 'border-purple-600');
        document.getElementById('tab-' + tab).classList.remove('text-gray-500');
    }
    
    // Show reading tab by default
    showTab('reading');
</script>
@endsection