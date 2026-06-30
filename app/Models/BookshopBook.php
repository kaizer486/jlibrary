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
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'sold_count' => 'integer',
        'pages' => 'integer',
        'publication_year' => 'integer',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(BookshopOrderItem::class, 'book_id');
    }

    public function isInStock(): bool
    {
        return $this->stock_quantity > 0 && $this->status === 'active';
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= 5;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => 'bg-green-100 text-green-700',
            'inactive' => 'bg-gray-100 text-gray-700',
            'out_of_stock' => 'bg-red-100 text-red-700',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-100 text-gray-700';
        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . ucfirst(str_replace('_', ' ', $this->status)) . '</span>';
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

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-book-cover.jpg');
    }
}