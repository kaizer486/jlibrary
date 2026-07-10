<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'subscription_id',
        'institution_id',
        'amount',
        'payment_method',
        'status',
        'reference',
        'metadata',
        'paid_at',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];
    
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->paid_at = now();
        $this->save();
    }
    
    public function markAsFailed(string $reason = null): void
    {
        $this->status = 'failed';
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['failure_reason'] = $reason;
            $this->metadata = $metadata;
        }
        $this->save();
    }
}