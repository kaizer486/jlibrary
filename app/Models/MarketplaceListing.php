<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $seller_id
 * @property string $title
 * @property string $description
 * @property string|null $book_file
 * @property string|null $cover_image
 * @property numeric $price
 * @property string|null $book_type
 * @property numeric|null $softcopy_price
 * @property numeric|null $hardcopy_price
 * @property bool|null $hardcopy_available
 * @property int|null $stock
 * @property string $status
 * @property string|null $admin_notes
 * @property int $views
 * @property int $downloads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing query()
 * @mixin \Eloquent
 */
class MarketplaceListing extends Model
{
    protected $table = 'marketplace_listings';

    protected $fillable = [
        'seller_id',
        'institution_id',
         'category_id',
        'title',
        'description',
        'book_file',
        'cover_image',
        'price',

        // Format & pricing (new)
        'book_type',
        'softcopy_price',
        'hardcopy_price',
        'hardcopy_available',
        'stock',

        'status',
        'admin_notes',
        'views',
        'downloads',
    ];

    protected $casts = [
        'price'              => 'decimal:2',
        'softcopy_price'     => 'decimal:2',
        'hardcopy_price'     => 'decimal:2',
        'hardcopy_available' => 'boolean',
        'stock'              => 'integer',
        'views'              => 'integer',
        'downloads'          => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function orders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'listing_id');
    }

    public function completedOrders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'listing_id')->where('status', 'completed');
    }

    // ==========================================
    // SALES / EARNINGS HELPERS
    // ==========================================

    public function getTotalSalesAttribute()
    {
        return $this->completedOrders()->count();
    }

    public function getTotalEarningsAttribute()
    {
        return $this->completedOrders()->sum('seller_earnings');
    }

    // ==========================================
    // STATUS HELPERS
    // ==========================================

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ==========================================
    // FORMAT HELPERS (mirrors Book model)
    // ==========================================

    public function effectiveBookType(): string
    {
        $hasSoft = (float) ($this->softcopy_price ?? 0) > 0;
        $hasHard = (bool) $this->hardcopy_available
            && (float) ($this->hardcopy_price ?? 0) > 0;

        if (in_array($this->book_type, ['softcopy', 'hardcopy', 'both'], true)) {
            if ($this->book_type === 'hardcopy') {
                return $hasHard ? 'hardcopy' : 'softcopy';
            }
            if ($this->book_type === 'softcopy') {
                return 'softcopy';
            }
            if ($hasSoft && $hasHard) return 'both';
            if ($hasHard) return 'hardcopy';
            return 'softcopy';
        }

        if ($hasSoft && $hasHard) return 'both';
        if ($hasHard) return 'hardcopy';
        return 'softcopy';
    }

    public function offersSoftcopy(): bool
    {
        $type = $this->effectiveBookType();
        if (!in_array($type, ['softcopy', 'both'], true)) {
            return false;
        }
        return !empty($this->book_file) || $this->effectiveSoftcopyPrice() > 0;
    }

    public function offersHardcopy(): bool
    {
        $type = $this->effectiveBookType();
        return in_array($type, ['hardcopy', 'both'], true)
            && (bool) $this->hardcopy_available
            && $this->effectiveHardcopyPrice() > 0;
    }

    public function effectiveSoftcopyPrice(): float
    {
        if ((float) ($this->softcopy_price ?? 0) > 0) {
            return (float) $this->softcopy_price;
        }
        return (float) ($this->price ?? 0);
    }

    public function effectiveHardcopyPrice(): float
    {
        return (float) ($this->hardcopy_price ?? 0);
    }

    public function hasStockFor(int $quantity): bool
    {
        return $this->offersHardcopy()
            && (int) ($this->stock ?? 0) >= $quantity;
    }

    public function getFormatLabelAttribute(): string
    {
        return match ($this->effectiveBookType()) {
            'softcopy' => ' Softcopy',
            'hardcopy' => ' Hardcopy',
            'both'     => ' Both',
            default    => 'Unknown',
        };
    }

    public function getFormatBadgeAttribute(): string
    {
        return match ($this->effectiveBookType()) {
            'softcopy' => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700"> Softcopy</span>',
            'hardcopy' => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700"> Hardcopy</span>',
            'both'     => '<span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">📱 Both</span>',
            default    => '',
        };
    }
}