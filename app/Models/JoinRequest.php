<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $institution_id
 * @property string $status
 * @property string|null $message
 * @property string|null $rejection_reason
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Institution $institution
 * @property-read \App\Models\User|null $approver
 */
class JoinRequest extends Model
{
    protected $fillable = [
        'user_id',
        'institution_id',
        'status',
        'message',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-700',
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
        ];

        $icons = [
            'pending' => '⏳',
            'approved' => '✅',
            'rejected' => '❌',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-700';
        $icon = $icons[$this->status] ?? '';

        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . $icon . ' ' . ucfirst($this->status) . '</span>';
    }
}