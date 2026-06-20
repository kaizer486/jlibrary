<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;  

class SubscriptionPayment extends Model
{
    protected $table = 'subscription_payments';
    
    protected $fillable = [
        'subscription_id', 'invoice_number', 'amount', 'currency',
        'status', 'billing_period_start', 'billing_period_end',
        'paid_at', 'payment_method', 'transaction_reference', 'notes'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'paid_at' => 'datetime',
    ];
    
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
    
    /**
     * Mark payment as paid
     * ✅ Fixed: explicit nullable type for $reference parameter
     */
    public function markAsPaid(?string $reference = null): void  
    {
        $this->status = 'paid';
        $this->paid_at = Carbon::now();  
        $this->transaction_reference = $reference ?? $this->transaction_reference;
        $this->save();
    }
}