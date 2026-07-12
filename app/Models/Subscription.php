<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'auto_renew',
        'payment_method',
        'payment_status',
        'transaction_reference',
        'mpesa_request_id',
        'mpesa_checkout_request_id',
        'mpesa_response_code',
        'mpesa_response_description',
        'billing_period',
        'payment_proof',
    ];
    
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'auto_renew' => 'boolean',
    ];
    
    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
    
    // ==========================================
    // STATUS CHECKS
    // ==========================================
    
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
    
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
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
    
    public function shouldWarnExpiration(): bool
    {
        if (!$this->ends_at) {
            return false;
        }
        
        $daysLeft = $this->daysRemaining();
        return $daysLeft <= 7 && $daysLeft > 0;
    }
    
    public function getExpirationWarningLevel(): string
    {
        $daysLeft = $this->daysRemaining();
        if ($daysLeft <= 1) return 'critical';
        if ($daysLeft <= 3) return 'urgent';
        if ($daysLeft <= 7) return 'warning';
        return 'none';
    }
    
    public function getPlanLabel(): string
    {
        return match($this->plan) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            'free' => '🆓 Free',
            default => '📘 ' . ucfirst($this->plan),
        };
    }
    
    // ==========================================
    // ACTIONS
    // ==========================================
    
    public function activate(): self
    {
        $this->status = 'active';
        $this->payment_status = 'paid';
        $this->starts_at = Carbon::now();
        $this->ends_at = $this->calculateExpiry($this->billing_period ?? 'monthly');
        $this->save();
        
        $this->updateSubscribableTier($this->plan);
        
        return $this;
    }
    
    /**
 * Cancel this subscription
 */
public function cancel(): self
{
    $this->status = 'cancelled';
    $this->cancelled_at = Carbon::now();
    $this->auto_renew = false;
    $this->save();
    
    // ✅ Update the institution
    if ($this->institution) {
        $this->institution->update([
            'subscription_status' => 'cancelled',
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
        
        // ✅ Update all users in this institution
        $this->institution->users()->update([
            'subscription_tier' => 'free',
            'subscription_expires_at' => null,
        ]);
    }
    
    // ✅ Update the subscribable (if it's an Institution or User)
    if ($this->subscribable) {
        if ($this->subscribable instanceof Institution) {
            $this->subscribable->update([
                'subscription_status' => 'cancelled',
                'subscription_tier' => 'free',
                'subscription_expires_at' => null,
            ]);
        } elseif ($this->subscribable instanceof User) {
            $this->subscribable->update([
                'subscription_tier' => 'free',
                'subscription_expires_at' => null,
            ]);
        }
    }
    
    return $this;
}
    
    public function expire(): self
    {
        $this->status = 'expired';
        $this->save();
        
        $this->updateSubscribableTier('free');
        
        return $this;
    }
    
    public function markPaymentPending(string $requestId, string $method): self
    {
        $this->status = 'pending';
        $this->payment_status = 'pending';
        $this->payment_method = $method;
        
        if ($method === 'mpesa') {
            $this->mpesa_checkout_request_id = $requestId;
        }
        
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
        $this->ends_at = $this->calculateExpiry($this->billing_period ?? 'monthly');
        $this->save();
        
        $this->updateSubscribableTier($this->plan);
        
        return $this;
    }
    
    public function uploadPaymentProof(string $path): self
    {
        $this->payment_proof = $path;
        $this->payment_status = 'pending';
        $this->status = 'pending';
        $this->save();
        
        return $this;
    }
    
    // ==========================================
    // HELPERS
    // ==========================================
    
    private function calculateExpiry(string $period): Carbon
    {
        $map = [
            'monthly' => ['method' => 'addMonths', 'count' => 1],
            'quarterly' => ['method' => 'addMonths', 'count' => 3],
            'semi_annual' => ['method' => 'addMonths', 'count' => 6],
            'annual' => ['method' => 'addYear', 'count' => 1],
        ];
        
        $config = $map[$period] ?? $map['monthly'];
        $start = Carbon::now();
        
        if ($config['method'] === 'addMonths') {
            return $start->copy()->addMonths($config['count']);
        }
        
        return $start->copy()->addYear();
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
    
    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '>', Carbon::now());
    }
    
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere('ends_at', '<=', Carbon::now());
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}