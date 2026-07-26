<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'shipping_address',
        'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who placed the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for this order
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">⏳ Pending</span>',
            'processing' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">🔄 Processing</span>',
            'completed' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ Completed</span>',
            'cancelled' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">❌ Cancelled</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">' . ucfirst($this->status) . '</span>'
        };
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}