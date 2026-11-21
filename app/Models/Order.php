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
        'bulk_order_id',
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
        'platform_commission',
        'commission_status',
        'commission_notes',
        'payment_status',
        'payment_received_at',
        'payment_confirmed_by_buyer_at',
        'payment_dispute_reason',
         'platform_commission',
    'commission_status', 
    'commission_notes',
    'commission_payment_method', 
    'commission_transaction_id', 
    'commission_paid_at',
    ];
    protected $casts = [
        'institution_id' => 'integer',
        'author_id'      => 'integer',
        'user_id'        => 'integer',
        'book_id'        => 'integer',
        'quantity'       => 'integer',
        'price_per_unit' => 'decimal:2',
        'total'          => 'decimal:2',
     'platform_commission' => 'decimal:2',
        'payment_received_at' => 'datetime',
        'payment_confirmed_by_buyer_at' => 'datetime',
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

    
    // ==========================================
    // COMMISSION HELPERS
    // ==========================================

    public function hasCommission(): bool
    {
        return $this->platform_commission !== null
            && (float) $this->platform_commission > 0;
    }

    public function commissionAmount(): float
    {
        return (float) ($this->platform_commission ?? 0);
    }

    public function ownerPayout(): float
    {
        return max(0, (float) $this->total - $this->commissionAmount());
    }

    public function isCommissionPending(): bool
    {
        return empty($this->commission_status)
            || $this->commission_status === 'pending';
    }

    public function isCommissionNegotiated(): bool
    {
        return $this->commission_status === 'negotiated';
    }

    public function isCommissionCollected(): bool
    {
        return $this->commission_status === 'collected';
    }

    public function getCommissionBadgeAttribute(): string
    {
        if (!$this->hasCommission() && $this->isCommissionPending()) {
            return '<span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">Not set</span>';
        }

        return match ($this->commission_status) {
            'negotiated' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Negotiated</span>',
            'collected'  => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Collected</span>',
            default      => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>',
        };
    }
    
    // ==========================================
    // PAYMENT CONFIRMATION HELPERS
    // ==========================================

    /**
     * Is this an order where the buyer pays the seller directly
     * (hardcopy / contact payment method)?
     */
    public function isContactPayment(): bool
    {
        return $this->payment_method === 'contact';
    }

    public function isPaymentUnpaid(): bool
    {
        return empty($this->payment_status) || $this->payment_status === 'unpaid';
    }

    public function isSellerMarkedPaid(): bool
    {
        return $this->payment_status === 'seller_marked_paid';
    }

    public function isBuyerConfirmed(): bool
    {
        return $this->payment_status === 'buyer_confirmed';
    }

    public function isPaymentDisputed(): bool
    {
        return $this->payment_status === 'disputed';
    }

    /**
     * Fully settled = buyer confirmed the payment.
     */
    public function isPaymentSettled(): bool
    {
        return $this->isBuyerConfirmed();
    }

    /**
     * Can the seller mark this order as paid?
     * - Must be a contact-payment order
     * - Must be currently unpaid
     * - Order must not be cancelled
     */
    public function canSellerMarkPaid(): bool
    {
        return $this->isContactPayment()
            && $this->isPaymentUnpaid()
            && !in_array($this->status, ['cancelled'], true);
    }

    /**
     * Can the seller un-mark paid?
     * - Only if they marked it themselves
     * - Only if buyer hasn't confirmed yet
     * - Never if there's an active dispute (buyer already said no)
     */
    public function canSellerUnmarkPaid(): bool
    {
        return $this->isContactPayment()
            && $this->isSellerMarkedPaid();
    }

    /**
     * Can the buyer confirm or dispute?
     * - Only when seller has marked paid
     */
    public function canBuyerRespond(): bool
    {
        return $this->isContactPayment()
            && $this->isSellerMarkedPaid();
    }

    /**
     * HTML badge for the payment status — matches the platform style.
     */
    public function getPaymentBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'buyer_confirmed'    => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><i class="ti ti-circle-check text-xs"></i> Paid & Confirmed</span>',
            'seller_marked_paid' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700"><i class="ti ti-clock text-xs"></i> Awaiting Buyer Confirmation</span>',
            'disputed'           => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700"><i class="ti ti-alert-triangle text-xs"></i> Payment Disputed</span>',
            'unpaid', null, ''   => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600"><i class="ti ti-clock text-xs"></i> Unpaid</span>',
            default              => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">' . ucfirst((string) $this->payment_status) . '</span>',
        };
    }

    /**
     * Timeline data for super-admin / detail pages.
     * Returns an array of steps with label, timestamp, done flag.
     */
    public function getPaymentTimelineAttribute(): array
    {
        return [
            [
                'label'     => 'Order Placed',
                'timestamp' => $this->created_at,
                'done'      => true,
                'icon'      => 'ti-shopping-cart',
                'color'     => 'text-slate-500',
            ],
            [
                'label'     => 'Seller Marked Paid',
                'timestamp' => $this->payment_received_at,
                'done'      => !is_null($this->payment_received_at),
                'icon'      => 'ti-cash',
                'color'     => 'text-yellow-600',
            ],
            [
                'label'     => $this->isPaymentDisputed() ? 'Buyer Disputed' : 'Buyer Confirmed',
                'timestamp' => $this->payment_confirmed_by_buyer_at,
                'done'      => !is_null($this->payment_confirmed_by_buyer_at) || $this->isPaymentDisputed(),
                'icon'      => $this->isPaymentDisputed() ? 'ti-alert-triangle' : 'ti-circle-check',
                'color'     => $this->isPaymentDisputed() ? 'text-red-600' : 'text-emerald-600',
            ],
        ];
    }
}