<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'institution_id',
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
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}