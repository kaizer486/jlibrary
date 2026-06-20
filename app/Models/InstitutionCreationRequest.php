<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $type
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $city
 * @property string|null $region
 * @property string|null $address
 * @property string|null $description
 * @property string|null $website
 * @property string|null $motivation
 * @property string|null $document_path
 * @property string $status
 * @property string|null $rejection_reason
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * @property-read User $user
 * @property-read User|null $approver
 */
class InstitutionCreationRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'email',
        'phone',
        'city',
        'region',
        'address',
        'description',
        'website',
        'motivation',
        'document_path',
        'status',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-700';
        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . ucfirst($this->status) . '</span>';
    }

    public function getTypeLabelAttribute(): string
    {
        $types = [
            'school' => '🏫 School',
            'college' => '🎓 College',
            'university' => '🏛️ University',
            'library' => '📚 Library',
            'bookstore' => '📖 Bookstore',
            'publisher' => '📰 Publisher',
            'research_center' => '🔬 Research Center',
            'academy' => '📖 Academy',
            'institute' => '🏢 Institute',
        ];

        return $types[$this->type] ?? '🏢 Other';
    }
}