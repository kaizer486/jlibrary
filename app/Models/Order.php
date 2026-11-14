<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * NOTE: This now matches the ACTUAL `orders` table schema:
     *   id, institution_id, author_id, user_id, book_id, book_type,
     *   quantity, price_per_unit, total, status, payment_method,
     *   transaction_id, shipping_address, customer_phone, notes
     *
     * Previously this list was out of sync and silently dropped
     * institution_id / book_id / quantity on create().
     */
    protected $fillable = [
        'institution_id',
        'author_id',
        'user_id',
        'book_id',
        'book_type',
        'quantity',
        'price_per_unit',
        'total',
        'status',
        'payment_method',
        'transaction_id',
        'shipping_address',
        'customer_phone',
        'notes',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'author_id'      => 'integer',
        'user_id'        => 'integer',
        'book_id'        => 'integer',
        'quantity'       => 'integer',
        'price_per_unit' => 'decimal:2',
        'total'          => 'decimal:2',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Buyer who placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The book being ordered.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * The author who owns the book (only set for author-owned books).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * The institution that owns the book (only set for institution-owned books).
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }

    /**
     * Legacy relation kept for compatibility — order_items table exists
     * but is not used by the current flow. Kept so nothing that already
     * references it breaks.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Orders for books owned by a given author.
     */
    public function scopeForAuthor($query, int $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Orders for books owned by a given institution.
     */
    public function scopeForInstitution($query, int $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    /**
     * Hardcopy orders only.
     */
    public function scopeHardcopy($query)
    {
        return $query->whereIn('book_type', ['hardcopy', 'both']);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isHardcopy(): bool
    {
        return in_array($this->book_type, ['hardcopy', 'both'], true);
    }

    public function isSoftcopy(): bool
    {
        return in_array($this->book_type, ['softcopy', 'both'], true);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Human-readable owner name (author OR institution).
     */
    public function getOwnerNameAttribute(): string
    {
        if ($this->author_id && $this->author) {
            return $this->author->full_name ?? $this->author->name ?? 'Author';
        }

        if ($this->institution_id && $this->institution) {
            return $this->institution->name ?? 'Institution';
        }

        return 'JLIBRARY';
    }

    /**
     * Status badge HTML — kept identical to previous output so any
     * existing view rendering {!! $order->status_badge !!} keeps working.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">⏳ Pending</span>',
            'processing' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">🔄 Processing</span>',
            'completed'  => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ Completed</span>',
            'cancelled'  => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">❌ Cancelled</span>',
            default      => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">' . ucfirst((string) $this->status) . '</span>',
        };
    }
}