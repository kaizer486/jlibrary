<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'institution_id',
        'duration_days',
        'reason',
        'status',
        'requested_at',
        'approved_at',
        'rejected_at',
        'admin_notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    // Scopes
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

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="bg-amber-500/20 text-amber-400 px-2 py-1 rounded-full text-xs">⏳ Pending</span>',
            'approved' => '<span class="bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded-full text-xs">✅ Approved</span>',
            'rejected' => '<span class="bg-red-500/20 text-red-400 px-2 py-1 rounded-full text-xs">❌ Rejected</span>',
            'cancelled' => '<span class="bg-gray-500/20 text-gray-400 px-2 py-1 rounded-full text-xs">🚫 Cancelled</span>',
        ];
        return $badges[$this->status] ?? $badges['pending'];
    }
}