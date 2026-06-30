<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
        'ends_at',      // ✅ Use ends_at (matches your migration)
        'cancelled_at',
        'auto_renew',
    ];
    
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',    // ✅ Use ends_at
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
    
    // ==========================================
    // HELPERS
    // ==========================================
    
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        if ($this->ends_at && $this->ends_at->isPast()) {  // ✅ Use ends_at
            return false;
        }
        
        return true;
    }
    
    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }
        
        if ($this->ends_at && $this->ends_at->isPast()) {  // ✅ Use ends_at
            return true;
        }
        
        return false;
    }
    
    public function daysRemaining(): int
    {
        if (!$this->ends_at) {  // ✅ Use ends_at
            return 0;
        }
        
        if ($this->ends_at->isPast()) {  // ✅ Use ends_at
            return 0;
        }
        
        return max(0, Carbon::now()->diffInDays($this->ends_at, false));  // ✅ Use ends_at
    }
    
    public function getPlanLabel(): string
    {
        return match($this->plan) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            'free' => '🆓 Free',
            default => '📘 Basic'
        };
    }
    
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'active' => 'bg-green-100 text-green-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'expired' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-gray-100 text-gray-700',
            'suspended' => 'bg-orange-100 text-orange-700',
        ];
        
        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-700';
        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $color . '">' . ucfirst($this->status) . '</span>';
    }
    
    // ==========================================
    // ACTIONS
    // ==========================================
    
    public function activate(): self
    {
        $this->status = 'active';
        $this->starts_at = Carbon::now();
        $this->ends_at = Carbon::now()->addMonth();  // ✅ Use ends_at
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
    
    public function renew(string $period = 'monthly'): self
    {
        $this->status = 'active';
        $this->starts_at = Carbon::now();
        $this->ends_at = Carbon::now()->addMonth();  // ✅ Use ends_at
        $this->cancelled_at = null;
        $this->auto_renew = true;
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
            $subscribable->subscription_expires_at = $this->ends_at;  // ✅ Use ends_at
            $subscribable->subscription_status = $this->status;
            $subscribable->save();
        }
        
        if ($subscribable instanceof User) {
            $subscribable->subscription_tier = $tier;
            $subscribable->subscription_expires_at = $this->ends_at;  // ✅ Use ends_at
            $subscribable->save();
        }
    }
}