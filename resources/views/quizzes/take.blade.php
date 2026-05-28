@extends('layouts.app')

@section('content')
<style>
    .timer-warning {
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0%, 100% { background-color: #ef4444; }
        50% { background-color: #dc2626; }
    }
    .question-card {
        transition: all 0.3s ease;
        border-left: 4px solid #8b5cf6;
    }
    .question-card:hover {
        transform: translateX(5px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .option-label {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .option-label:hover {
        background-color: #f3e8ff;
        border-color: #8b5cf6;
    }
    input[type="radio"]:checked + .option-label {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
        color: white;
    }
    input[type="radio"]:checked + .option-label .option-letter {
        background-color: white;
        color: #8b5cf6;
    }
    .option-letter {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #f3e8ff;
        color: #8b5cf6;
        font-weight: bold;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-white font-bold text-2xl">{{ $quiz->title }}</h1>
                        <p class="text-purple-200 text-sm mt-1">Test your knowledge</p>
                    </div>
                    <div class="text-center bg-white/20 backdrop-blur-sm rounded-xl px-6 py-3">
                        <div class="text-white text-3xl font-bold" id="timer">--:--</div>
                        <p class="text-purple-200 text-xs">Time Remaining</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Questions Form -->
        <form id="quizForm" action="{{ route('quizzes.submit', $quiz->id) }}" method="POST">
            @csrf
            
            <div class="space-y-4 mb-6">
                @foreach($quiz->questions as $index => $question)
                <div class="bg-white rounded-xl shadow-md overflow-hidden question-card">
                    <div class="p-6">
                        <div class="flex items-start gap-3 mb-5">
                            <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ $index + 1 }}
                            </div>
                            <h3 class="font-semibold text-gray-800 text-lg">{{ $question->question }}</h3>
                        </div>
                        
                        <div class="grid md:grid-cols-2 gap-3">
                            @php
                                $options = [
                                    'A' => $question->option_a,
                                    'B' => $question->option_b,
                                    'C' => $question->option_c,
                                    'D' => $question->option_d,
                                ];
                            @endphp
                            
                            @foreach($options as $letter => $text)
                            <label class="relative block cursor-pointer">
                                <input type="radio" name="q_{{ $question->id }}" value="{{ $letter }}" class="hidden peer" required>
                                <div class="option-label border border-gray-200 rounded-xl p-3 flex items-center gap-3 peer-checked:bg-purple-600 peer-checked:border-purple-600 peer-checked:text-white transition">
                                    <div class="option-letter flex-shrink-0 peer-checked:bg-white peer-checked:text-purple-600">
                                        {{ $letter }}
                                    </div>
                                    <span class="text-sm">{{ $text }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Submit Button -->
            <div class="bg-white rounded-xl shadow-md p-5 sticky bottom-4">
                <div class="flex justify-between items-center">
                    <button type="button" id="cancelBtn" class="px-6 py-3 text-gray-600 hover:text-gray-800 font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" class="btn-primary text-white px-8 py-3 rounded-xl font-semibold flex items-center gap-2">
                        <i class="ti ti-check"></i>
                        Submit Quiz
                    </button>
                </div>
            </div>
        </form>
        
    </div>
</div>

<script>
    let timeLeft = {{ $quiz->time_limit * 60 }};
    const timerDisplay = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 60 && timeLeft > 0) {
            timerDisplay.classList.add('text-red-200');
            document.querySelector('.bg-white\\/20').classList.add('timer-warning');
        }
        
        if (timeLeft <= 0) {
            document.getElementById('quizForm').submit();
        }
        
        timeLeft--;
    }
    
    updateTimer();
    setInterval(updateTimer, 1000);
    
    document.getElementById('cancelBtn').addEventListener('click', () => {
        if (confirm('Cancel this quiz? Your progress will be lost.')) {
            window.location.href = '{{ route("quizzes.index") }}';
        }
    });
    
    document.getElementById('submitBtn').addEventListener('click', (e) => {
        let allAnswered = true;
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            const name = radio.getAttribute('name');
            const checked = document.querySelector(`input[name="${name}"]:checked`);
            if (!checked) allAnswered = false;
        });
        
        if (!allAnswered && !confirm('You have unanswered questions. Submit anyway?')) {
            e.preventDefault();
        } else if (!confirm('Are you sure you want to submit your answers?')) {
            e.preventDefault();
        }
    });
</script>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
    }
    .btn-primary:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(102,126,234,0.4);
    }
    .timer-warning {
        animation: pulse 1s infinite;
        background-color: rgba(239, 68, 68, 0.3) !important;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>
@endsection