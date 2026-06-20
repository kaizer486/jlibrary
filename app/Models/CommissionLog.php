<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionLog extends Model
{
    protected $table = 'commission_logs';
    
    protected $fillable = [
        'author_id',
        'buyer_id',
        'saleable_type',
        'saleable_id',
        'total_amount',
        'author_earnings',
        'platform_fee',
        'currency',
        'exchange_rate',
        'status',
        'payout_date',
        'payout_method',
        'payout_reference',
        'notes',
        'processed_by',
    ];
    
    protected $casts = [
        'total_amount' => 'decimal:2',
        'author_earnings' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'payout_date' => 'datetime',
    ];
    
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    
    public function saleable()
    {
        return $this->morphTo();
    }
    
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}