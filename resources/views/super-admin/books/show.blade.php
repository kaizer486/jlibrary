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
                <!-- Flags -->
                <div class="px-6 pb-4 flex flex-wrap gap-2 justify-center">
                    @if($book->is_featured)
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">⭐ Featured</span>
                    @endif
                    @if($book->is_trending)
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-semibold">🔥 Trending</span>
                    @endif
                    @if($book->is_paid)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">💰 Paid</span>
                    @else
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">🆓 Free</span>
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
                        <span class="font-semibold text-yellow-600">⭐ {{ number_format($book->average_rating ?? 0, 1) }}/5</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Total Ratings</span>
                        <span class="font-semibold">{{ $book->ratings_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Total Reviews</span>
                        <span class="font-semibold">{{ $book->reviews_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">Bookmarks</span>
                        <span class="font-semibold">{{ $book->bookmarks_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Quizzes</span>
                        <span class="font-semibold">{{ $book->quizzes()->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- File Info -->
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="ti ti-file text-purple-600"></i> File Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-500">PDF File</span>
                        @if($book->file_path)
                            <span class="text-green-600 font-semibold">✅ Available</span>
                        @else
                            <span class="text-red-600 font-semibold">❌ Missing</span>
                        @endif
                    </div>
                    @if($book->file_path)
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-500">File Name</span>
                            <span class="text-xs text-gray-600 truncate max-w-[120px]">{{ basename($book->file_path) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500">File Size</span>
                            <span class="text-sm text-gray-600">
                                @if(Storage::disk('public')->exists($book->file_path))
                                    {{ number_format(Storage::disk('public')->size($book->file_path) / 1024, 2) }} KB
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    @endif
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
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Category</p>
                            <p class="mt-1 text-gray-700">{{ $book->category_label ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Sub-Category</p>
                            <p class="mt-1 text-gray-700">{{ $book->sub_category ?? 'N/A' }}</p>
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
                                {{ $book->formatted_price ?? 'FREE' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Pages</p>
                            <p class="mt-1">{{ number_format($book->total_pages ?? 0) }} pages</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Published Date</p>
                            <p class="mt-1">{{ $book->published_date ? $book->published_date->format('F d, Y') : 'N/A' }}</p>
                        </div>
                       <div>
    <p class="text-xs text-gray-400 uppercase tracking-wide">Institution</p>
    <p class="mt-1">
        @if($book->institution_id && $book->institution)
            {{ $book->institution->name }}
        @else
            <span class="text-gray-400">Global Library</span>
        @endif
    </p>
</div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Uploaded By</p>
                            <p class="mt-1">{{ $book->uploader->full_name ?? 'Admin' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Created</p>
                            <p class="mt-1">{{ $book->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Last Updated</p>
                            <p class="mt-1">{{ $book->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Description</p>
                        <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'No description available.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid md:grid-cols-3 gap-4">
                <a href="{{ route('super-admin.books.edit', $book) }}" class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                    <i class="ti ti-edit"></i> Edit Book
                </a>
                @if($book->file_path)
                    <a href="{{ $book->pdf_url }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                        <i class="ti ti-file-pdf"></i> View PDF
                    </a>
                @endif
                <form method="POST" action="{{ route('super-admin.books.destroy', $book) }}" onsubmit="return confirm('Delete {{ addslashes($book->title) }} permanently?')" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white p-3 rounded-xl text-center transition flex items-center justify-center gap-2">
                        <i class="ti ti-trash"></i> Delete Book
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection