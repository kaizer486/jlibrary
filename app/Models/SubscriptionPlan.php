<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';
    
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'currency',
        'billing_interval', 'features', 'max_users', 'max_books',
        'is_active', 'sort_order'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
    
    /**
     * Get formatted price attribute
     *  Fixed: convert decimal to float for number_format
     */
    public function getFormattedPriceAttribute(): string
    {
        $symbol = $this->currency === 'USD' ? '$' : 'TSh';
        //  Convert price to float using (float) cast
        return $symbol . ' ' . number_format((float) $this->price, 2) . '/' . $this->billing_interval;
    }
}