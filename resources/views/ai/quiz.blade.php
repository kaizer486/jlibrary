@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Quiz: {{ $book->title }}</h1>
        <p class="text-gray-600">Test your knowledge and earn a certificate (70% to pass)</p>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('ai.submit-quiz', $book) }}" id="quiz-form">
            @csrf
            <input type="hidden" name="quiz_data" id="quiz-data" value='{{ json_encode($quizData) }}'>
            
            @foreach($quizData as $index => $question)
                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                    <h3 class="font-semibold text-gray-900 mb-3">Question {{ $index + 1 }}: {{ $question['question'] }}</h3>
                    <div class="space-y-2">
                        @foreach($question['options'] as $option)
                            <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                                <input type="radio" name="answers[{{ $index }}]" value="{{ substr($option, 0, 1) }}" class="text-jlibrary-600" required>
                                <span class="text-gray-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
            
            <button type="submit" class="w-full bg-jlibrary-600 text-white px-6 py-3 rounded-lg hover:bg-jlibrary-700 transition font-semibold">
                Submit Quiz
            </button>
        </form>
    </div>
</div>
@endsection