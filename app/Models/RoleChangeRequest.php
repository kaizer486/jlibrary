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

class RoleChangeRequest extends Model
{
    protected $fillable = [
        'requester_id', 'user_id', 'institution_id', 'requested_role',
        'current_role', 'reason', 'status', 'reviewed_by', 'review_notes', 'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
        $this->status = 'approved';
        $this->reviewed_by = $reviewerId;
        $this->review_notes = $notes;
        $this->reviewed_at = now();
        $this->save();

        // Update the user's role
        $this->user->role = $this->requested_role;
        $this->user->save();

        return $this;
    }

    public function reject($reviewerId, $notes)
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewerId;
        $this->review_notes = $notes;
        $this->reviewed_at = now();
        $this->save();

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