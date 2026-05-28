@extends('emails.layouts.email')

@section('content')
<!-- Book Icon -->
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-2xl flex items-center justify-center mx-auto">
        <i class="ti ti-book-open text-4xl text-blue-600"></i>
    </div>
</div>

<!-- Header -->
<h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">New Book Available! 📚✨</h1>
<p class="text-gray-500 text-center mb-6">{{ $bookTitle }} has been added to your library.</p>

<!-- Book Details Card -->
<div class="bg-white rounded-xl p-5 mb-6 border border-gray-200 shadow-sm">
    <div class="flex gap-4">
        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg">
            <i class="ti ti-book text-3xl text-white"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-800 text-lg">{{ $bookTitle }}</h3>
            <p class="text-gray-500 text-sm">by {{ $bookAuthor }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="ti ti-file-text"></i> {{ $bookPages }} pages
                </span>
                <span class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="ti ti-download"></i> {{ number_format($bookDownloads) }} downloads
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Book Description -->
<div class="bg-gray-50 rounded-xl p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
        <i class="ti ti-info-circle text-blue-500"></i>
        About This Book
    </h3>
    <p class="text-sm text-gray-600 leading-relaxed">{{ Str::limit($bookDescription, 250) }}</p>
</div>

<!-- What You Can Do -->
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-3 text-center">
        <i class="ti ti-eye text-blue-500 text-lg block mb-1"></i>
        <p class="text-xs font-medium text-gray-700">Read Online</p>
        <p class="text-xs text-gray-500">Any device</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 text-center">
        <i class="ti ti-download text-green-500 text-lg block mb-1"></i>
        <p class="text-xs font-medium text-gray-700">Download PDF</p>
        <p class="text-xs text-gray-500">Offline access</p>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-3 text-center">
        <i class="ti ti-chart-line text-purple-500 text-lg block mb-1"></i>
        <p class="text-xs font-medium text-gray-700">Track Progress</p>
        <p class="text-xs text-gray-500">Save your place</p>
    </div>
    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl p-3 text-center">
        <i class="ti ti-quiz text-yellow-500 text-lg block mb-1"></i>
        <p class="text-xs font-medium text-gray-700">Take Quiz</p>
        <p class="text-xs text-gray-500">Test knowledge</p>
    </div>
</div>

<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="{{ route('library.read', $bookId) }}" class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:shadow-lg transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-eye"></i>
        Start Reading
    </a>
    <a href="{{ route('library.download', $bookId) }}" class="border border-blue-600 text-blue-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-blue-50 transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-download"></i>
        Download PDF
    </a>
</div>

<!-- Reading Tip -->
<div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
    <div class="flex items-start gap-3">
        <i class="ti ti-lightbulb text-amber-500 text-xl"></i>
        <div>
            <p class="text-sm font-medium text-amber-800">Reading Tip</p>
            <p class="text-xs text-amber-700">Set a daily reading goal of 20 pages to build a consistent reading habit! 📖</p>
        </div>
    </div>
</div>
@endsection