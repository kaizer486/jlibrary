<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrowing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_id',
        'user_id',
        'institution_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'notes',
        'borrowed_by',
        'returned_to',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function borrowedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrowed_by');
    }

    public function returnedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'borrowed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'borrowed')
            ->where('due_date', '<', now());
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeByInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isOverdue(): bool
    {
        return $this->status === 'borrowed' && $this->due_date < now();
    }

    public function isActive(): bool
    {
        return $this->status === 'borrowed';
    }

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function getDaysLeft(): int
    {
        if ($this->isReturned()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->due_date, false));
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'borrowed' => 'bg-blue-500/20 text-blue-400 border border-blue-500/20',
            'returned' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            'overdue' => 'bg-red-500/20 text-red-400 border border-red-500/20',
            'lost' => 'bg-gray-500/20 text-gray-400 border border-gray-500/20',
        ];

        $labels = [
            'borrowed' => '📖 Borrowed',
            'returned' => '✅ Returned',
            'overdue' => '⚠️ Overdue',
            'lost' => '❌ Lost',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-500/20 text-gray-400';
        $label = $labels[$this->status] ?? ucfirst($this->status);

        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
    }
}