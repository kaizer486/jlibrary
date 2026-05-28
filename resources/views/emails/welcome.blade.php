@extends('emails.layouts.email')

@section('content')
<!-- Hero Icon -->
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl flex items-center justify-center mx-auto">
        <i class="ti ti-hand-wave text-4xl text-purple-600"></i>
    </div>
</div>

<!-- Greeting -->
<h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome to JLIBRARY, {{ $userName }}! 👋</h1>
<p class="text-gray-500 mb-6">We're thrilled to have you join our community of lifelong learners.</p>

<!-- Stats Cards -->
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-3 text-center">
        <i class="ti ti-books text-xl text-blue-600 mb-1 block"></i>
        <p class="text-xl font-bold text-gray-800">500+</p>
        <p class="text-xs text-gray-500">Books</p>
    </div>
    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-3 text-center">
        <i class="ti ti-brain text-xl text-green-600 mb-1 block"></i>
        <p class="text-xl font-bold text-gray-800">50+</p>
        <p class="text-xs text-gray-500">Quizzes</p>
    </div>
    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-3 text-center">
        <i class="ti ti-users text-xl text-purple-600 mb-1 block"></i>
        <p class="text-xl font-bold text-gray-800">10k+</p>
        <p class="text-xs text-gray-500">Learners</p>
    </div>
</div>

<!-- Features List -->
<div class="bg-gray-50 rounded-xl p-5 mb-6">
    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <i class="ti ti-rocket text-purple-600"></i>
        What You Get
    </h3>
    <div class="space-y-2">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-check text-green-500"></i>
            <span>Access to 500+ premium books</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-check text-green-500"></i>
            <span>Interactive quizzes with certificates</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-check text-green-500"></i>
            <span>AI-powered learning assistant</span>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="ti ti-check text-green-500"></i>
            <span>Active community discussions</span>
        </div>
    </div>
</div>

<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="{{ route('library.index') }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:shadow-lg transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-books"></i>
        Start Reading
    </a>
    <a href="{{ route('quizzes.index') }}" class="border border-purple-600 text-purple-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-purple-50 transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-brain"></i>
        Take a Quiz
    </a>
</div>

<!-- Tip Box -->
<div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-200">
    <div class="flex items-start gap-3">
        <i class="ti ti-lightbulb text-amber-500 text-xl"></i>
        <div>
            <p class="text-sm font-medium text-amber-800">Pro Tip</p>
            <p class="text-xs text-amber-700">Complete your first book within 7 days to earn the "Quick Learner" badge! 🎯</p>
        </div>
    </div>
</div>
@endsection