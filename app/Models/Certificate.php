<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property string $certificate_number
 * @property string|null $file_path
 * @property int $quiz_score
 * @property int $total_questions
 * @property numeric $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCertificateNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereQuizScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereTotalQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUserId($value)
 * @mixin \Eloquent
 */
class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'certificate_number',
        'file_path',
        'quiz_score',
        'total_questions',     // Add this
        'percentage'           // Add this
    ];
    
    protected $casts = [
        'quiz_score' => 'integer',
        'total_questions' => 'integer',  // Add this
        'percentage' => 'decimal:2'       // Add this
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}