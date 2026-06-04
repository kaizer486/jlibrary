@extends('layouts.master')



@section('title', $book->title)

@section('page-content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-jlibrary-600">Dashboard</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <a href="{{ route('admin.books.index') }}" class="hover:text-jlibrary-600">Books</a>
            <i class="ti ti-chevron-right text-xs"></i>
            <span class="text-gray-900">{{ $book->title }}</span>
        </div>
        
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $book->title }}</h1>
                <p class="text-gray-500 mt-1">by {{ $book->author }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.books.edit', $book) }}" class="bg-jlibrary-600 hover:bg-jlibrary-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-edit"></i> Edit Book
                </a>
                <button onclick="toggleStatus()" class="border border-gray-300 hover:bg-gray-50 px-5 py-2.5 rounded-xl transition flex items-center gap-2">
                    <i class="ti ti-refresh"></i> {{ $book->status === 'approved' ? 'Reject' : 'Approve' }}
                </button>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 px-4 py-3">
                    <h3 class="text-white font-semibold">Book Cover</h3>
                </div>
                <div class="p-6 flex justify-center">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full max-w-[250px] rounded-xl shadow-lg">
                    @else
                        <div class="w-full max-w-[250px] h-64 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center">
                            <i class="ti ti-books text-6xl text-gray-500"></i>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-bar text-jlibrary-600"></i> Statistics
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Downloads</span>
                        <span class="font-semibold">{{ number_format($book->downloads ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Average Rating</span>
                        <span class="font-semibold text-yellow-600">⭐ {{ $book->averageRating() ?? 0 }}/5</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Total Ratings</span>
                        <span class="font-semibold">{{ $book->ratings()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Reviews</span>
                        <span class="font-semibold">{{ $book->reviews()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Bookmarks</span>
                        <span class="font-semibold">{{ $book->bookmarks()->count() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-info-circle text-jlibrary-600"></i> Information
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Status</p>
                        <p class="mt-1">
                            <span class="inline-block px-2 py-1 rounded-lg text-xs font-semibold {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Price</p>
                        <p class="mt-1 font-semibold">{{ $book->is_paid ? '$'.number_format($book->price, 2) : 'FREE' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Total Pages</p>
                        <p class="mt-1">{{ number_format($book->total_pages ?? 0) }} pages</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Uploaded By</p>
                        <p class="mt-1">{{ $book->uploader->full_name ?? 'Admin' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase">Created</p>
                        <p class="mt-1">{{ $book->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-jlibrary-50 to-purple-50 px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-file-description"></i> Description
                    </h3>
                </div>
                <div class="p-5">
                    <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-star text-yellow-500"></i> Rating Distribution
                    </h3>
                </div>
                <div class="p-5">
                    @php
                        $distribution = $book->rating_distribution;
                        $totalRatings = $book->ratings()->count();
                    @endphp
                    
                    @foreach([5,4,3,2,1] as $star)
                        @php
                            $count = $distribution[$star] ?? 0;
                            $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 text-sm font-medium text-gray-600">{{ $star }} ★</div>
                            <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-12 text-sm text-gray-500">{{ $count }}</div>
                        </div>
                    @endforeach
                    
                    <div class="mt-4 pt-3 border-t text-center">
                        <p class="text-sm text-gray-500">Based on {{ $totalRatings }} ratings</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ti ti-message-circle text-blue-500"></i> Recent Reviews
                    </h3>
                </div>
                <div class="p-5">
                    @php
                        $recentReviews = $book->reviews()->with('user')->latest()->limit(5)->get();
                    @endphp
                    
                    @if($recentReviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentReviews as $review)
                                <div class="pb-4 border-b last:border-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                                                <span class="text-white text-xs font-bold">{{ substr($review->user->full_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm">{{ $review->user->full_name }}</p>
                                                <p class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="ti ti-star-filled text-yellow-400 text-sm"></i>
                                                @else
                                                    <i class="ti ti-star text-gray-300 text-sm"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-600 text-sm">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <i class="ti ti-message-circle-off text-4xl mb-2 block"></i>
                            <p>No reviews yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStatus() {
    if (confirm(`Are you sure you want to ${'{{ $book->status }}' === 'approved' ? 'reject' : 'approve'} this book?`)) {
        fetch('{{ route("admin.books.toggle-status", $book) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection