<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuizPassedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $quizTitle;
    public $percentage;
    public $correctAnswers;
    public $totalQuestions;
    public $timeSpent;
    public $pointsEarned;
    public $progressToNextLevel;
    public $attemptId;

    public function __construct($userName, $quizTitle, $percentage, $correctAnswers, $totalQuestions, $timeSpent, $pointsEarned, $progressToNextLevel, $attemptId)
    {
        $this->userName = $userName;
        $this->quizTitle = $quizTitle;
        $this->percentage = $percentage;
        $this->correctAnswers = $correctAnswers;
        $this->totalQuestions = $totalQuestions;
        $this->timeSpent = $timeSpent;
        $this->pointsEarned = $pointsEarned;
        $this->progressToNextLevel = $progressToNextLevel;
        $this->attemptId = $attemptId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🧠 Quiz Mastered! Check Your Results on JLIBRARY',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quiz-passed',
            with: [
                'userName' => $this->userName,
                'quizTitle' => $this->quizTitle,
                'percentage' => $this->percentage,
                'correctAnswers' => $this->correctAnswers,
                'totalQuestions' => $this->totalQuestions,
                'timeSpent' => $this->timeSpent,
                'pointsEarned' => $this->pointsEarned,
                'progressToNextLevel' => $this->progressToNextLevel,
                'attemptId' => $this->attemptId,
                'headerTitle' => 'Quiz Completed! 🧠'
            ]
        );
    }
}