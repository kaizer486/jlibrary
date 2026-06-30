<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $book_id
 * @property int|null $quiz_id
 * @property int|null $quiz_attempt_id
 * @property int|null $institution_id
 * @property string $certificate_number
 * @property string|null $file_path
 * @property int $quiz_score
 * @property int $total_questions
 * @property float $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\Quiz|null $quiz
 * @property-read \App\Models\QuizAttempt|null $quizAttempt
 * @property-read \App\Models\User|null $user
 */
class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'quiz_id',
        'quiz_attempt_id',
        'institution_id',
        'certificate_number',
        'file_path',
        'quiz_score',
        'total_questions',
        'percentage',
    ];

    protected $casts = [
        'quiz_score' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    /**
     * Check if certificate is for a quiz.
     */
    public function isForQuiz(): bool
    {
        return !is_null($this->quiz_id);
    }

    /**
     * Check if certificate is for a book.
     */
    public function isForBook(): bool
    {
        return !is_null($this->book_id);
    }

    /**
     * Get the display name for the certificate.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->book) {
            return $this->book->title;
        }
        if ($this->quiz) {
            return $this->quiz->title;
        }
        return 'Certificate';
    }

    /**
     * Get the type of certificate.
     */
    public function getTypeAttribute(): string
    {
        if ($this->isForQuiz()) {
            return 'Quiz Completion';
        }
        if ($this->isForBook()) {
            return 'Book Completion';
        }
        return 'Certificate';
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->percentage >= 90) {
            return 'bg-emerald-500/20 text-emerald-400';
        }
        if ($this->percentage >= 70) {
            return 'bg-yellow-500/20 text-yellow-400';
        }
        return 'bg-red-500/20 text-red-400';
    }
}