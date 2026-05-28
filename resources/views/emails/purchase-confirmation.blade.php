@extends('emails.layouts.email')

@section('content')
<!-- Success Icon -->
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl flex items-center justify-center mx-auto">
        <i class="ti ti-circle-check text-4xl text-green-600"></i>
    </div>
</div>

<!-- Header -->
<h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">Thank You for Your Purchase! 🛍️</h1>
<p class="text-gray-500 text-center mb-6">Your payment has been successfully processed.</p>

<!-- Book Details Card -->
<div class="bg-white rounded-xl p-5 mb-6 border border-gray-200 shadow-sm">
    <div class="flex gap-4">
        <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
            <i class="ti ti-book text-3xl text-purple-600"></i>
        </div>
        <div class="flex-1">
            <h3 class="font-bold text-gray-800 text-lg">{{ $bookTitle }}</h3>
            <p class="text-gray-500 text-sm">by {{ $bookAuthor }}</p>
            <div class="flex items-center gap-4 mt-2">
                <span class="text-sm font-semibold text-purple-600">${{ number_format($amount, 2) }}</span>
                <span class="text-xs text-gray-400">Lifetime Access</span>
            </div>
        </div>
    </div>
    <div class="border-t border-gray-100 mt-4 pt-3">
        <div class="flex justify-between text-xs">
            <span class="text-gray-500">Transaction ID:</span>
            <span class="text-gray-700 font-mono">{{ $transactionId }}</span>
        </div>
    </div>
</div>

<!-- Features Grid -->
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="bg-gray-50 rounded-xl p-3 text-center">
        <i class="ti ti-download text-purple-500 text-lg block mb-1"></i>
        <p class="text-xs text-gray-600">PDF Download</p>
    </div>
    <div class="bg-gray-50 rounded-xl p-3 text-center">
        <i class="ti ti-device-mobile text-purple-500 text-lg block mb-1"></i>
        <p class="text-xs text-gray-600">Any Device</p>
    </div>
    <div class="bg-gray-50 rounded-xl p-3 text-center">
        <i class="ti ti-chart-line text-purple-500 text-lg block mb-1"></i>
        <p class="text-xs text-gray-600">Track Progress</p>
    </div>
    <div class="bg-gray-50 rounded-xl p-3 text-center">
        <i class="ti ti-infinity text-purple-500 text-lg block mb-1"></i>
        <p class="text-xs text-gray-600">Lifetime Access</p>
    </div>
</div>

<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="{{ route('library.read', $bookId) }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:shadow-lg transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-eye"></i>
        Read Now
    </a>
    <a href="{{ route('library.download', $bookId) }}" class="border border-purple-600 text-purple-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-purple-50 transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-download"></i>
        Download PDF
    </a>
</div>

<!-- Support -->
<p class="text-center text-xs text-gray-400 mt-6">
    Need help? Contact us at <a href="mailto:support@jlibrary.com" class="text-purple-500">support@jlibrary.com</a>
</p>
@endsection