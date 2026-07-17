<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;  

class SubscriptionBackup extends Model
{
    protected $table = 'subscriptions';
    
    protected $fillable = [
        'institution_id', 'plan', 'amount', 'status', 'starts_at', 
        'ends_at', 'cancelled_at', 'auto_renew'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];
    
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
    
    public function activate()
    {
        $this->status = 'active';
        $this->starts_at = Carbon::now();  
        $this->save();
        
        return $this;
    }
    
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->cancelled_at = Carbon::now();  
        $this->auto_renew = false;
        $this->save();
        
        return $this;
    }
    
    public function expire()
    {
        $this->status = 'expired';
        $this->save();
        
        return $this;
    }
    
    public function renew()
    {
        $this->status = 'active';
        $this->starts_at = Carbon::now();  
        $this->ends_at = Carbon::now()->addMonth();  
        $this->cancelled_at = null;
        $this->auto_renew = true;
        $this->save();
        
        return $this;
    }
}