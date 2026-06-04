<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinRequest extends Model
{
    protected $fillable = [
        'user_id', 'institution_id', 'message', 'status', 
        'reviewed_by', 'reviewed_at', 'admin_notes'
    ];
    
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    public function approve($reviewerId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
        
        // Update user's institution
        $this->user->update([
            'institution_id' => $this->institution_id,
        ]);
        
        return $this;
    }
    
    public function reject($reviewerId, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
            'admin_notes' => $notes,
        ]);
        
        return $this;
    }
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>',
            'approved' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>',
            'rejected' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Unknown</span>'
        };
    }
}