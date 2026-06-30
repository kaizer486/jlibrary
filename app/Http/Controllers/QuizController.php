<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class QuizController extends BaseController
{
    public function __construct()
    {
        // Auth middleware is applied in routes/web.php
    }

    public function index()
    {
        $quizzes = Quiz::where('is_active', true)
            ->withCount('questions')
            ->get();

        $userAttempts = QuizAttempt::where('user_id', Auth::id())
            ->get()
            ->keyBy('quiz_id');

        return view('quizzes.index', compact('quizzes', 'userAttempts'));
    }

    public function show($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        $passedAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $id)
            ->where('passed', true)
            ->first();

        if ($passedAttempt) {
            return redirect()->route('quizzes.results', $passedAttempt->id)
                ->with('info', 'You have already passed this quiz!');
        }

        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, $id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        $answers = $request->except('_token');

        $score = 0;
        $totalPoints = 0;
        $userAnswers = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $answers['q_' . $question->id] ?? null;
            $isCorrect = ($userAnswer == $question->correct_answer);

            if ($isCorrect) {
                $score += $question->points;
            }

            $userAnswers[$question->id] = [
                'selected' => $userAnswer,
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct_answer,
                'question' => $question->question,
                'options' => [
                    'A' => $question->option_a,
                    'B' => $question->option_b,
                    'C' => $question->option_c,
                    'D' => $question->option_d,
                ]
            ];
        }

        $percentage = ($totalPoints > 0) ? round(($score / $totalPoints) * 100) : 0;
        $passed = $percentage >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'score' => $score,
            'total_points' => $totalPoints,
            'percentage' => $percentage,
            'passed' => $passed,
            'completed_at' => now(),
        ]);

        foreach ($answers as $key => $answer) {
            if (str_starts_with($key, 'q_')) {
                $questionId = str_replace('q_', '', $key);
                UserAnswer::create([
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'selected_answer' => $answer,
                    'is_correct' => $userAnswers[$questionId]['is_correct'],
                ]);
            }
        }

        // ✅ GENERATE CERTIFICATE IF PASSED
        if ($passed && $quiz->certificate_enabled) {
            try {
                $certificateController = new \App\Http\Controllers\CertificateController();
                $certificateController->generateFromQuiz($quiz, $attempt);
            } catch (\Exception $e) {
                \Log::error('Certificate generation failed: ' . $e->getMessage());
            }
        }

        session()->put('quiz_results_' . $attempt->id, $userAnswers);

        return redirect()->route('quizzes.results', $attempt->id);
    }

    public function results($attemptId)
    {
        $attempt = QuizAttempt::with('quiz')->findOrFail($attemptId);

        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $userAnswers = session()->get('quiz_results_' . $attemptId, []);

        return view('quizzes.results', compact('attempt', 'userAnswers'));
    }

    public function history()
    {
        $attempts = QuizAttempt::with('quiz')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('quizzes.history', compact('attempts'));
    }
}