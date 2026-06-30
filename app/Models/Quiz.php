<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string|null $image
 * @property int $time_limit
 * @property int $passing_score
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuizAttempt> $attempts
 * @property-read int|null $attempts_count
 * @property-read mixed $total_points
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz wherePassingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereTimeLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quiz whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Quiz extends Model
{
    protected $fillable = [
        'title', 'description', 'image', 'time_limit', 'passing_score', 'is_active', 'institution_id',
    ];
    
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
    
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }
}