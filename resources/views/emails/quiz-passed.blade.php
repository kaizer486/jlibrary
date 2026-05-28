@extends('emails.layouts.email')

@section('content')
<!-- Brain Icon -->
<div class="text-center mb-6">
    <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center mx-auto">
        <i class="ti ti-brain text-4xl text-purple-600"></i>
    </div>
</div>

<!-- Header -->
<h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">Excellent Work, {{ $userName }}! 🧠✨</h1>
<p class="text-gray-500 text-center mb-6">You've successfully passed the quiz with flying colors!</p>

<!-- Score Card -->
<div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 mb-6 text-center">
    <div class="inline-block bg-white rounded-full px-6 py-3 shadow-md mb-4">
        <span class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ $percentage }}%</span>
    </div>
    <div class="grid grid-cols-2 gap-4 mt-4">
        <div>
            <p class="text-xs text-gray-500">Correct Answers</p>
            <p class="text-xl font-bold text-green-600">{{ $correctAnswers }}/{{ $totalQuestions }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Time Spent</p>
            <p class="text-xl font-bold text-purple-600">{{ $timeSpent }}</p>
        </div>
    </div>
</div>

<!-- Rewards Card -->
<div class="bg-gradient-to-r from-yellow-50 to-amber-50 rounded-xl p-5 mb-6 border border-yellow-200">
    <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
        <i class="ti ti-gift text-yellow-600"></i>
        Rewards Unlocked
    </h3>
    <div class="space-y-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="ti ti-point text-green-500"></i>
                <span>Points Earned</span>
            </div>
            <span class="font-semibold text-green-600">+{{ $pointsEarned }} points</span>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="ti ti-certificate text-purple-500"></i>
                <span>Certificate</span>
            </div>
            <span class="font-semibold text-purple-600">Unlocked 🎓</span>
        </div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <i class="ti ti-badge text-amber-500"></i>
                <span>Badge</span>
            </div>
            <span class="font-semibold text-amber-600">Added to Profile</span>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-gray-50 rounded-xl p-5 mb-6">
    <div class="flex justify-between text-sm text-gray-600 mb-2">
        <span>Progress to Next Level</span>
        <span>{{ $progressToNextLevel }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" style="width: {{ $progressToNextLevel }}%"></div>
    </div>
</div>

<!-- CTA Buttons -->
<div class="flex flex-col sm:flex-row gap-3 justify-center">
    <a href="{{ route('quizzes.results', $attemptId) }}" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:shadow-lg transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-list-details"></i>
        Review Answers
    </a>
    <a href="{{ route('quizzes.index') }}" class="border border-purple-600 text-purple-600 px-6 py-3 rounded-xl font-semibold text-center hover:bg-purple-50 transition inline-flex items-center justify-center gap-2">
        <i class="ti ti-arrow-right"></i>
        Next Quiz
    </a>
</div>

<!-- Motivation -->
<div class="mt-6 p-4 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl">
    <div class="flex items-start gap-3">
        <i class="ti ti-flame text-white text-xl"></i>
        <div>
            <p class="text-sm font-medium text-white">🔥 You're on fire!</p>
            <p class="text-xs text-purple-200">Complete 5 quizzes to unlock the "Quiz Master" badge and earn 1000 bonus points!</p>
        </div>
    </div>
</div>
@endsection