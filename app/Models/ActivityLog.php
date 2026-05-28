<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'entity_type', 'entity_id', 
        'description', 'ip_address', 'user_agent'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper method to log activity
    public static function log($userId, $action, $description, $entityType = null, $entityId = null)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
    
    // Get icon for activity
    public function getIconAttribute()
    {
        return match($this->action) {
            'login' => 'ti-login',
            'purchase' => 'ti-wallet',
            'quiz_completed' => 'ti-brain',
            'book_added' => 'ti-book',
            'book_read' => 'ti-book-open',
            'certificate_earned' => 'ti-certificate',
            'review_written' => 'ti-star',
            'joined_group' => 'ti-users',
            default => 'ti-activity'
        };
    }
    
    // Get color for activity
    public function getColorAttribute()
    {
        return match($this->action) {
            'login' => 'blue',
            'purchase' => 'green',
            'quiz_completed' => 'purple',
            'book_added' => 'indigo',
            'book_read' => 'emerald',
            'certificate_earned' => 'yellow',
            'review_written' => 'orange',
            'joined_group' => 'pink',
            default => 'gray'
        };
    }
}