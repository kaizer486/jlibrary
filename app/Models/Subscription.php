<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'institution_id',
        'plan',
        'amount',
        'status', // pending, active, expired, cancelled
        'starts_at',
        'ends_at',
        'cancelled_at',
        'auto_renew',
        'payment_method',
        'payment_status', // pending, paid, failed
        'transaction_reference',
        'mpesa_request_id',
        'mpesa_checkout_request_id',
        'mpesa_response_code',
        'mpesa_response_description',
    ];
    
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];
    
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        
        return true;
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }
        
        if ($this->ends_at && $this->ends_at->isPast()) {
            return true;
        }
        
        return false;
    }
    
    public function daysRemaining(): int
    {
        if (!$this->ends_at) {
            return 0;
        }
        
        if ($this->ends_at->isPast()) {
            return 0;
        }
        
        return max(0, Carbon::now()->diffInDays($this->ends_at, false));
    }
    
    public function activate(): self
    {
        $this->status = 'active';
        $this->payment_status = 'paid';
        $this->starts_at = Carbon::now();
        $this->ends_at = Carbon::now()->addMonth();
        $this->save();
        
        $this->updateSubscribableTier($this->plan);
        
        return $this;
    }
    
    public function cancel(): self
    {
        $this->status = 'cancelled';
        $this->cancelled_at = Carbon::now();
        $this->auto_renew = false;
        $this->save();
        
        return $this;
    }
    
    public function expire(): self
    {
        $this->status = 'expired';
        $this->save();
        
        $this->updateSubscribableTier('free');
        
        return $this;
    }
    
    public function markPaymentPending(string $mpesaRequestId): self
    {
        $this->status = 'pending';
        $this->payment_status = 'pending';
        $this->mpesa_request_id = $mpesaRequestId;
        $this->save();
        
        return $this;
    }
    
    public function markPaymentFailed(string $reason): self
    {
        $this->payment_status = 'failed';
        $this->mpesa_response_description = $reason;
        $this->status = 'cancelled';
        $this->save();
        
        return $this;
    }
    
    public function markPaymentSuccess(string $transactionRef): self
    {
        $this->payment_status = 'paid';
        $this->transaction_reference = $transactionRef;
        $this->status = 'active';
        $this->starts_at = Carbon::now();
        $this->ends_at = Carbon::now()->addMonth();
        $this->save();
        
        $this->updateSubscribableTier($this->plan);
        
        return $this;
    }
    
    private function updateSubscribableTier(string $tier): void
    {
        $subscribable = $this->subscribable;
        
        if (!$subscribable) {
            return;
        }
        
        if ($subscribable instanceof Institution) {
            $subscribable->subscription_tier = $tier;
            $subscribable->subscription_expires_at = $this->ends_at;
            $subscribable->subscription_status = $this->status;
            $subscribable->save();
        }
        
        if ($subscribable instanceof User) {
            $subscribable->subscription_tier = $tier;
            $subscribable->subscription_expires_at = $this->ends_at;
            $subscribable->save();
        }
    }
}