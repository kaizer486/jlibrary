<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $quiz_attempt_id
 * @property int $question_id
 * @property string $selected_answer
 * @property bool $is_correct
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Question|null $question
 * @property-read \App\Models\QuizAttempt|null $quizAttempt
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereIsCorrect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereQuizAttemptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereSelectedAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnswer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserAnswer extends Model
{
    protected $table = 'user_answers';
    
    protected $fillable = [
        'quiz_attempt_id',
        'question_id',
        'selected_answer',
        'is_correct'
    ];
    
    protected $casts = [
        'is_correct' => 'boolean'
    ];
    
    public function quizAttempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }
    
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}