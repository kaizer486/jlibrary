@extends('layouts.super-admin')

@section('title', $book->title)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('super-admin.books.index') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
            <i class="ti ti-arrow-left"></i> Back to Books
        </a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Left Column - Cover & Basic Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Cover Card -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-4">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-photo"></i> Book Cover
                    </h3>
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

            <!-- Stats Card -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-chart-bar text-purple-600"></i> Statistics
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
                        <span class="text-gray-500">Total Reviews</span>
                        <span class="font-semibold">{{ $book->reviews()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Bookmarks</span>
                        <span class="font-semibold">{{ $book->bookmarks()->count() }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Quizzes</span>
                        <span class="font-semibold">{{ $book->quizzes()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Book Info Card -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                    <h3 class="text-white font-semibold flex items-center gap-2">
                        <i class="ti ti-info-circle"></i> Book Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Title</p>
                            <p class="mt-1 font-semibold text-gray-800">{{ $book->title }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Author</p>
                            <p class="mt-1 text-gray-700">{{ $book->author }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Status</p>
                            <p class="mt-1">
                                <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $book->status === 'approved' ? 'bg-green-100 text-green-700' : ($book->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($book->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Price</p>
                            <p class="mt-1 font-semibold text-gray-800">
                                {{ $book->is_paid ? '$'.number_format($book->price, 2) : 'FREE' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Pages</p>
                            <p class="mt-1">{{ number_format($book->total_pages ?? 0) }} pages</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Institution</p>
                            <p class="mt-1">{{ $book->institution->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Uploaded By</p>
                            <p class="mt-1">{{ $book->uploader->full_name ?? 'Admin' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Created</p>
                            <p class="mt-1">{{ $book->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Description</p>
                        <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection