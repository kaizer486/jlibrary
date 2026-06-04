<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int|null $institution_id
 * @property int|null $user_id
 * @property int $requested_by
 * @property float $amount
 * @property string $status
 * @property string $payment_method
 * @property string $account_details
 * @property string|null $notes
 * @property int|null $processed_by
 * @property Carbon|null $processed_at
 * @property string|null $rejection_reason
 * @property string|null $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Enrollment extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'status', 'progress', 'completed_at'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    public function markComplete()
    {
        $this->status = 'completed';
        $this->progress = 100;
        $this->completed_at = now();
        $this->save();
    }
}