<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    
    protected $fillable = [
        'subscribable_type', 'subscribable_id',
        'subscription_plan_id', 'status', 'payment_method',
        'gateway_subscription_id', 'start_date', 'end_date',
        'cancelled_at', 'trial_ends_at', 'auto_renew'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];
    
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
    
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }
        
        return true;
    }
    
    public function isTrialing(): bool
    {
        return $this->status === 'trialing' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }
    
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }
    
    public function daysRemaining(): int
    {
        if (!$this->end_date) {
            return 0;
        }
        
        return Carbon::now()->diffInDays($this->end_date, false);
    }
    
    public function activate(): self
    {
        $this->status = 'active';
        $this->start_date = Carbon::now();
        $this->end_date = Carbon::now()->addMonth();
        $this->save();
        
        // Update the subscribable's tier field
        $this->updateSubscribableTier($this->plan->slug);
        
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
        
        // Reset subscribable's tier to free
        $this->updateSubscribableTier('free');
        
        return $this;
    }
    
    public function renew(): self
    {
        $this->status = 'active';
        $this->start_date = Carbon::now();
        $this->end_date = Carbon::now()->addMonth();
        $this->cancelled_at = null;
        $this->auto_renew = true;
        $this->save();
        
        return $this;
    }
    
    private function updateSubscribableTier(string $tier): void
    {
        $subscribable = $this->subscribable;
        
        if ($subscribable instanceof User) {
            $subscribable->subscription_tier = $tier;
            $subscribable->subscription_expires_at = $this->end_date;
            $subscribable->save();
        } elseif ($subscribable instanceof Institution) {
            $subscribable->subscription_tier = $tier;
            $subscribable->subscription_expires_at = $this->end_date;
            $subscribable->save();
        }
    }
}