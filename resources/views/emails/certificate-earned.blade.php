@extends('emails.layouts.email')

@section('content')
<!-- Celebration Icon -->
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-2xl flex items-center justify-center mx-auto animate-bounce">
        <i class="ti ti-trophy text-4xl text-yellow-600"></i>
    </div>
</div>

<!-- Header -->
<h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">Congratulations, {{ $userName }}! 🎉</h1>
<p class="text-gray-500 text-center mb-6">You've successfully earned a certificate!</p>

<!-- Certificate Card -->
<div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-2xl p-6 mb-6 text-center border-2 border-yellow-300">
    <i class="ti ti-certificate text-5xl text-yellow-600 mb-3 block"></i>
    <h2 class="text-xl font-bold text-amber-800 mb-1">Certificate of Achievement</h2>
    <p class="text-sm text-amber-600 mb-3">Presented to</p>
    <p class="text-lg font-bold text-gray-800">{{ $userName }}</p>
    <div class="border-t border-yellow-200 my-4"></div>
    <p class="text-sm text-gray-600">for completing</p>
    <p class="font-semibold text-gray-800 mb-3">{{ $quizTitle }}</p>
    <div class="inline-block bg-gradient-to-r from-yellow-500 to-amber-500 text-white px-6 py-2 rounded-full">
        <span class="text-2xl font-bold">{{ $percentage }}%</span>
    </div>
    <p class="text-xs text-amber-600 mt-2">Passing Score: {{ $passingScore }}%</p>
</div>

<!-- Next Steps -->
<div class="bg-gray-50 rounded-xl p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <i class="ti ti-stars text-purple-600"></i>
        What's Next?
    </h3>
    <div class="space-y-2">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-download text-purple-500"></i>
            <span>Download your certificate as PDF</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-brand-linkedin text-purple-500"></i>
            <span>Share it on LinkedIn</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-chart-line text-purple-500"></i>
            <span>Take advanced quizzes to level up</span>
        </div>
    </div>
</div>

<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="{{ route('certificates.index') }}" class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white px-6 py-3 rounded-xl font-semibold text-center hover:shadow-lg transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-certificate"></i>
        View Certificate
    </a>
    <a href="{{ route('quizzes.index') }}" class="border border-purple-600 text-purple-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-purple-50 transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-brain"></i>
        Take Next Quiz
    </a>
</div>

<!-- Motivation -->
<div class="mt-6 p-4 bg-purple-50 rounded-xl border border-purple-200">
    <div class="flex items-start gap-3">
        <i class="ti ti-flame text-purple-500 text-xl"></i>
        <div>
            <p class="text-sm font-medium text-purple-800">Keep the momentum going!</p>
            <p class="text-xs text-purple-700">Complete 5 quizzes to unlock the "Quiz Master" badge and earn 500 bonus points! 🔥</p>
        </div>
    </div>
</div>
@endsection