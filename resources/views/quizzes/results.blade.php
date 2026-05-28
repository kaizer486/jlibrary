@extends('layouts.app')

@section('content')
<style>
    .confetti {
        position: fixed;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 999;
    }
    .result-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
</style>

@if($attempt->passed)
<div class="confetti" id="confetti"></div>
@endif

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Result Card -->
        <div class="result-card rounded-2xl shadow-xl overflow-hidden">
            
            @if($attempt->passed)
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-10 text-center text-white">
                <div class="inline-block animate-bounce mb-4">
                    <i class="ti ti-trophy text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Congratulations!</h1>
                <p class="text-green-100">You passed the quiz!</p>
            </div>
            @else
            <div class="bg-gradient-to-r from-red-500 to-orange-600 px-6 py-10 text-center text-white">
                <div class="inline-block mb-4">
                    <i class="ti ti-mood-sad text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Keep Learning!</h1>
                <p class="text-red-100">You didn't pass this time. Try again!</p>
            </div>
            @endif
            
            <!-- Score Summary -->
            <div class="p-8 border-b">
                <h2 class="font-bold text-gray-800 text-xl mb-6 text-center">Your Performance</h2>
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ti ti-scoreboard text-purple-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Your Score</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $attempt->score }}/{{ $attempt->total_points }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ti ti-chart-bar text-blue-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Percentage</p>
                        <p class="text-2xl font-bold {{ $attempt->percentage >= 70 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $attempt->percentage }}%
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ti ti-target text-yellow-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Passing Score</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $attempt->quiz->passing_score }}%</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-{{ $attempt->passed ? 'green' : 'red' }}-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="ti ti-{{ $attempt->passed ? 'check' : 'x' }} text-{{ $attempt->passed ? 'green' : 'red' }}-600 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Status</p>
                        <p class="text-2xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                            {{ $attempt->passed ? 'Passed' : 'Failed' }}
                        </p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-3 rounded-full transition-all duration-1000" style="width: {{ $attempt->percentage }}%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Review -->
            <div class="p-8">
                <h3 class="font-bold text-gray-800 text-xl mb-6 flex items-center gap-2">
                    <i class="ti ti-list-details"></i>
                    Detailed Review
                </h3>
                <div class="space-y-4">
                    @foreach($userAnswers as $questionId => $answer)
                    <div class="border rounded-xl p-5 {{ $answer['is_correct'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} transition hover:shadow-md">
                        <div class="flex items-start gap-3 mb-4">
                            @if($answer['is_correct'])
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-circle-check text-green-600"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="ti ti-circle-x text-red-600"></i>
                                </div>
                            @endif
                            <p class="font-medium text-gray-800 flex-1">{{ $answer['question'] }}</p>
                        </div>
                        
                        <div class="ml-11 space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24">Your answer:</span>
                                <span class="{{ $answer['is_correct'] ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                    {{ $answer['selected'] }}. {{ $answer['options'][$answer['selected']] ?? 'Not answered' }}
                                </span>
                            </div>
                            @if(!$answer['is_correct'])
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 w-24">Correct answer:</span>
                                <span class="text-green-600 font-medium">
                                    {{ $answer['correct_answer'] }}. {{ $answer['options'][$answer['correct_answer']] }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="border-t bg-gray-50 p-6 flex justify-between">
                <a href="{{ route('quizzes.index') }}" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2">
                    <i class="ti ti-arrow-left"></i>
                    Back to Quizzes
                </a>
                @if(!$attempt->passed)
                <a href="{{ route('quizzes.show', $attempt->quiz_id) }}" class="btn-primary text-white px-8 py-3 rounded-xl font-semibold flex items-center gap-2">
                    <i class="ti ti-repeat"></i>
                    Try Again
                </a>
                @endif
            </div>
            
        </div>
    </div>
</div>

@if($attempt->passed)
<script>
    // Simple confetti effect
    for(let i = 0; i < 100; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
        confetti.style.left = Math.random() * window.innerWidth + 'px';
        confetti.style.top = '-10px';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '9999';
        confetti.style.animation = `fall ${Math.random() * 3 + 2}s linear forwards`;
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 5000);
    }
    
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fall {
            to {
                transform: translateY(${window.innerHeight + 10}px) rotate(360deg);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endif

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(102,126,234,0.4);
    }
</style>
@endsection