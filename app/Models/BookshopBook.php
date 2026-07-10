<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookshopBook extends Model
{
    use SoftDeletes;

    protected $table = 'bookshop_books';

    protected $fillable = [
        'institution_id',
        'title',
        'author',
        'description',
        'cover_image',
        'price',
        'stock_quantity',
        'sold_count',
        'status',
        'category',
        'isbn',
        'pages',
        'publisher',
        'publication_year',
        'shelf_number', // ✅ ADD THIS
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'sold_count' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(BookshopOrderItem::class, 'book_id');
    }

    /**
     * Get the shelf this book belongs to.
     */
    public function shelf(): BelongsTo
    {
        return $this->belongsTo(Shelf::class, 'shelf_number', 'code');
    }

    // ==========================================
    // STOCK HELPERS
    // ==========================================

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0 && $this->status === 'active';
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= 5;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0 || $this->status === 'out_of_stock';
    }

    // ==========================================
    // STATUS BADGE
    // ==========================================

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => 'bg-emerald-100 text-emerald-700',
            'inactive' => 'bg-gray-100 text-gray-700',
            'out_of_stock' => 'bg-red-100 text-red-700',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-700';
        $label = ucfirst(str_replace('_', ' ', $this->status));
        
        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Out of Stock';
        } elseif ($this->stock_quantity <= 5) {
            return 'Low Stock (' . $this->stock_quantity . ' left)';
        }
        return 'In Stock (' . $this->stock_quantity . ' available)';
    }

    // ==========================================
    // COVER IMAGE
    // ==========================================

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-book-cover.jpg');
    }

    // ==========================================
    // STATUS MAPPING (For library to bookstore status mapping)
    // ==========================================

    public function getValidStatuses()
    {
        return ['active', 'inactive', 'out_of_stock'];
    }

    public function setStatusAttribute($value)
    {
        // Map library statuses to bookstore statuses
        $map = [
            'approved' => 'active',
            'pending' => 'inactive',
            'rejected' => 'inactive',
        ];
        
        $this->attributes['status'] = $map[$value] ?? $value;
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0)->where('status', 'active');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0)->orWhere('status', 'out_of_stock');
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 5)
            ->where('status', 'active');
    }

    public function scopeOnShelf($query, $shelfCode)
    {
        return $query->where('shelf_number', $shelfCode);
    }

    public function scopeInInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('author', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%")
              ->orWhere('isbn', 'LIKE', "%{$search}%");
        });
    }

    // ==========================================
    // PRICE HELPERS
    // ==========================================

    public function getFormattedPriceAttribute()
    {
        return 'TSh ' . number_format($this->price, 2);
    }

    public function getDiscountedPrice($discountPercentage = 0)
    {
        if ($discountPercentage <= 0) {
            return $this->price;
        }
        return $this->price * (1 - ($discountPercentage / 100));
    }

    // ==========================================
    // STATS HELPERS
    // ==========================================

    public function incrementSoldCount($quantity = 1)
    {
        $this->increment('sold_count', $quantity);
        $this->decrement('stock_quantity', $quantity);
        
        if ($this->stock_quantity <= 0) {
            $this->update(['status' => 'out_of_stock']);
        }
        
        return $this;
    }

    public function restock($quantity)
    {
        $this->increment('stock_quantity', $quantity);
        
        if ($this->stock_quantity > 0 && $this->status === 'out_of_stock') {
            $this->update(['status' => 'active']);
        }
        
        return $this;
    }
}