<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string|null $content
 * @property string|null $video_url
 * @property string|null $attachment
 * @property int|null $duration
 * @property int $order
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Lesson extends Model
{
    protected $table = 'lessons';
    
    protected $fillable = [
        'course_id',
        'title',
        'content',
        'video_url',
        'attachment',
        'duration',
        'order'
    ];
    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration) return 'N/A';
        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }
}