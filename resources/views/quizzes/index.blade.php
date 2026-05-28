@extends('layouts.app')

@section('content')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .bg-pattern {
        background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
    }
    .stat-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(102,126,234,0.3);
    }
    .quiz-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid rgba(102,126,234,0.1);
    }
</style>

<div class="min-h-screen bg-pattern" style="background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);">
    
    <!-- Hero Section -->
    <div class="hero-gradient text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <div class="inline-block animate-bounce mb-4">
                <i class="ti ti-brain text-5xl"></i>
            </div>
            <h1 class="text-5xl font-bold mb-4">Test Your Knowledge</h1>
            <p class="text-xl opacity-90 max-w-2xl mx-auto">Challenge yourself with our interactive quizzes and earn certificates</p>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="container mx-auto px-4 -mt-8 mb-12">
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-notes text-purple-600 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $quizzes->count() }}</p>
                <p class="text-gray-500">Available Quizzes</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-checkbox text-green-600 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $userAttempts->where('passed', true)->count() }}</p>
                <p class="text-gray-500">Completed Quizzes</p>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 text-center transform hover:scale-105 transition duration-300">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-certificate text-yellow-600 text-2xl"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800">{{ $userAttempts->where('passed', true)->count() }}</p>
                <p class="text-gray-500">Certificates Earned</p>
            </div>
        </div>
    </div>
    
    <!-- Quizzes Grid -->
    <div class="container mx-auto px-4 pb-16">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Available Quizzes</h2>
                <p class="text-gray-500 mt-1">Choose a quiz to test your skills</p>
            </div>
            <a href="{{ route('quizzes.history') }}" class="text-purple-600 hover:text-purple-700 flex items-center gap-2">
                <i class="ti ti-history"></i>
                View History
            </a>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($quizzes as $quiz)
            <div class="quiz-card rounded-2xl shadow-lg overflow-hidden card-hover">
                <div class="h-40 bg-gradient-to-r from-purple-500 to-indigo-500 relative">
                    <div class="absolute inset-0 bg-black/20"></div>
                    <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center">
                        <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                            <i class="ti ti-clock"></i> {{ $quiz->time_limit }} min
                        </span>
                        <span class="bg-white/20 backdrop-blur-sm text-white text-xs px-3 py-1 rounded-full">
                            <i class="ti ti-questions"></i> {{ $quiz->questions_count ?? 0 }} Questions
                        </span>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="font-bold text-xl text-gray-800 mb-2">{{ $quiz->title }}</h3>
                    <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ Str::limit($quiz->description, 80) }}</p>
                    
                    <div class="flex items-center justify-between text-sm mb-4">
                        <div class="flex items-center gap-1 text-gray-500">
                            <i class="ti ti-chart-bar"></i>
                            <span>Pass: {{ $quiz->passing_score }}%</span>
                        </div>
                        <div class="flex items-center gap-1 text-gray-500">
                            <i class="ti ti-star"></i>
                            <span>{{ $quiz->total_points ?? 0 }} Points</span>
                        </div>
                    </div>
                    
                    @php
                        $attempt = $userAttempts[$quiz->id] ?? null;
                    @endphp
                    
                    @if($attempt && $attempt->passed)
                        <div class="mb-3 p-2 bg-green-50 rounded-xl text-center">
                            <span class="text-green-600 text-sm">✓ Completed • {{ $attempt->percentage }}%</span>
                        </div>
                        <a href="{{ route('quizzes.results', $attempt->id) }}" 
                           class="block text-center bg-gray-100 text-gray-600 rounded-xl py-2.5 text-sm font-medium hover:bg-gray-200 transition">
                            View Results
                        </a>
                    @elseif($attempt && !$attempt->passed)
                        <div class="mb-3 p-2 bg-yellow-50 rounded-xl text-center">
                            <span class="text-yellow-600 text-sm">Attempted • {{ $attempt->percentage }}%</span>
                        </div>
                        <a href="{{ route('quizzes.show', $quiz->id) }}" 
                           class="block text-center btn-primary text-white rounded-xl py-2.5 text-sm font-medium">
                            Try Again
                        </a>
                    @else
                        <a href="{{ route('quizzes.show', $quiz->id) }}" 
                           class="block text-center btn-primary text-white rounded-xl py-2.5 text-sm font-medium">
                            Start Quiz
                        </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <i class="ti ti-notes text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-400 text-lg">No quizzes available yet.</p>
                    <p class="text-gray-400 text-sm mt-1">Check back later for new challenges!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
    
</div>
@endsection