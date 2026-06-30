<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookshopOrderItem extends Model
{
    protected $table = 'bookshop_order_items';

    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price',
        'total',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(BookshopOrder::class, 'order_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(BookshopBook::class, 'book_id');
    }
}