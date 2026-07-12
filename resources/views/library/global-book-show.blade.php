@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('library.index') }}" class="inline-flex items-center text-jlibrary-600 hover:text-jlibrary-700">
            <i class="ti ti-arrow-left mr-1"></i> Back to Library
        </a>
        
        <!-- Bookmark Button -->
        <x-bookmark-button :item="$book" type="book" size="md" />
    </div>
    
    <div class="grid md:grid-cols-3 gap-8">
        <!-- Left Column - Book Cover -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm overflow-hidden sticky top-24">
                <div class="h-64 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center relative">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <i class="ti ti-book text-8xl text-white/50"></i>
                    @endif
                    
                    <!-- Global Badge -->
                    <div class="absolute bottom-2 left-2 bg-purple-600/90 text-white px-3 py-1 rounded-lg text-xs">
                        🌐 Global Library
                    </div>
                </div>
                
                <div class="p-6">
                    @if($book->is_paid)
                        <div class="mb-4">
                            <div class="text-2xl font-bold text-jlibrary-600">${{ number_format($book->price, 2) }}</div>
                            <p class="text-gray-500 text-sm">One-time purchase. Lifetime access.</p>
                        </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        @auth
                            @if($hasAccess)
                                <a href="{{ route('library.read', $book->id) }}" 
                                   class="block text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                    <i class="ti ti-eye"></i> Read Now
                                </a>
                                <a href="{{ route('library.download', $book->id) }}" 
                                   class="block text-center border border-jlibrary-600 text-jlibrary-600 px-4 py-2 rounded-lg hover:bg-jlibrary-600 hover:text-white transition">
                                    <i class="ti ti-download"></i> Download PDF
                                </a>
                            @else
                                <button onclick="showPurchaseModal({{ $book->id }}, {{ $book->price }}, '{{ addslashes($book->title) }}')" 
                                        class="block text-center bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-2 rounded-lg hover:shadow-lg transition w-full">
                                    <i class="ti ti-shopping-cart"></i> Purchase for ${{ number_format($book->price, 2) }}
                                </button>
                            @endif
                            
                            @if($progress && $progress->pivot->status != 'completed')
                                <form method="POST" action="{{ route('library.add-to-library', $book->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="reading">
                                    <button type="submit" class="block text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition w-full">
                                        <i class="ti ti-bookmark"></i> Add to My Library
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block text-center bg-jlibrary-600 text-white px-4 py-2 rounded-lg hover:bg-jlibrary-700 transition">
                                Login to Read
                            </a>
                            <a href="{{ route('register') }}" class="block text-center border border-jlibrary-600 text-jlibrary-600 px-4 py-2 rounded-lg hover:bg-jlibrary-600 hover:text-white transition">
                                Create Free Account
                            </a>
                        @endauth
                    </div>
                    
                    <!-- Reading Progress -->
                    @if($progress && $progress->pivot->progress_percent > 0)
                        <div class="mt-6 pt-4 border-t">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Reading Progress</span>
                                <span>{{ $progress->pivot->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress->pivot->progress_percent }}%"></div>
                            </div>
                            @if($progress->pivot->status == 'completed')
                                <div class="mt-2 text-green-600 text-sm">
                                    <i class="ti ti-certificate"></i> Completed! 
                                    <a href="#" class="underline">Get Certificate</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right Column - Book Details -->
        <div class="md:col-span-2">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                    <p class="text-gray-600 text-lg mb-4">by {{ $book->author }}</p>
                </div>
            </div>
            
            <!-- ⭐ RATINGS SECTION -->
<div class="flex items-center flex-wrap gap-4 mb-6">
    <div class="flex items-center gap-2">
        @php
            $avgRating = method_exists($book, 'averageRating') ? $book->averageRating() : 0;
            $ratingCount = method_exists($book, 'ratingCount') ? $book->ratingCount() : ($book->ratings_count ?? 0);
        @endphp
        <x-star-rating :rating="$avgRating" readonly="true" size="lg" />
        <span class="text-2xl font-bold text-gray-800">
            {{ number_format($avgRating, 1) }}
        </span>
        <span class="text-gray-500 text-sm">
            ({{ $ratingCount }} ratings)
        </span>
    </div>
    
    @auth
        @php
            $hasUserRated = method_exists($book, 'hasUserRated') ? $book->hasUserRated() : false;
            $userRating = method_exists($book, 'userRating') ? $book->userRating() : null;
        @endphp
        @if(!$hasUserRated)
            <div class="text-sm text-gray-500 flex items-center gap-2">
                <span>Rate this book:</span>
                <x-star-rating :bookId="$book->id" size="md" />
            </div>
        @else
            <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full">
                <span class="text-sm text-green-600">You rated: {{ $userRating ?? 0 }} ★</span>
                <form action="{{ route('books.rating.delete', $book) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700" onclick="return confirm('Remove your rating?')">
                        Remove
                    </button>
                </form>
            </div>
        @endif
    @endauth
</div>



            
            <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                <span><i class="ti ti-file-text"></i> {{ $book->total_pages ?? 0 }} pages</span>
                <span><i class="ti ti-download"></i> {{ number_format($book->downloads ?? 0) }} downloads</span>
                <span><i class="ti ti-eye"></i> {{ number_format($book->views_count ?? 0) }} views</span>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold mb-3">Description</h2>
                <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'No description available for this book.' }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-xl p-6 mb-6">
                <h2 class="text-xl font-semibold mb-3">About the Author</h2>
                <p class="text-gray-700">{{ $book->author }} is a renowned author in this field.</p>
            </div>
            
            <!-- ⭐ REVIEWS SECTION -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-message-circle-2"></i>
                    Reviews & Comments
                    <span class="text-sm font-normal text-gray-500">({{ $book->reviews_count ?? 0 }} reviews)</span>
                </h3>
                
                @auth
                    @if(!$book->hasUserReviewed())
                        <div class="bg-gray-50 rounded-xl p-5 mb-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Write a Review</h4>
                            <form action="{{ route('books.review', $book) }}" method="POST">
                                @csrf
                                <textarea name="review" rows="4" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                          placeholder="Share your thoughts about this book..." required></textarea>
                                <button type="submit" class="mt-3 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="bg-green-50 rounded-xl p-4 mb-6 flex justify-between items-center">
                            <div>
                                <i class="ti ti-check-circle text-green-500"></i>
                                <span class="text-sm text-gray-600">You've reviewed this book</span>
                            </div>
                            <form action="{{ route('books.review.delete', $book) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700" onclick="return confirm('Delete your review?')">
                                    Delete Review
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
                
                <!-- Reviews List -->
                <div class="space-y-4">
                    @forelse($book->reviews as $review)
                        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-gray-800">{{ $review->user->full_name ?? 'Anonymous' }}</span>
                                        <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @php
                                        $userRating = $review->user->ratingForBook($book->id);
                                    @endphp
                                    @if($userRating)
                                        <x-star-rating :rating="$userRating" readonly="true" size="sm" />
                                    @endif
                                </div>
                                <button onclick="markHelpful({{ $review->id }}, this)" 
                                        class="text-sm text-gray-400 hover:text-purple-600 transition flex items-center gap-1">
                                    <i class="ti ti-thumb-up"></i>
                                    <span class="helpful-count-{{ $review->id }}">{{ $review->helpful_count }}</span>
                                </button>
                            </div>
                            <p class="text-gray-600 leading-relaxed">{{ $review->review }}</p>
                        </div>
                    @empty
                        <div class="bg-gray-50 rounded-xl p-8 text-center">
                            <i class="ti ti-message-circle-2 text-4xl text-gray-300 mb-2 block"></i>
                            <p class="text-gray-500">No reviews yet. Be the first to review this book!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection