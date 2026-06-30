<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'monthly_price',
        'quarterly_price',
        'semi_annual_price',
        'annual_price',
        'max_users',
        'max_books',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'quarterly_price' => 'decimal:2',
        'semi_annual_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'max_users' => 'integer',
        'max_books' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscription_plan_id');
    }

    public function getPriceForPeriod($period): float
    {
        return match($period) {
            'monthly' => $this->monthly_price,
            'quarterly' => $this->quarterly_price ?? $this->monthly_price * 3,
            'semi_annual' => $this->semi_annual_price ?? $this->monthly_price * 6,
            'annual' => $this->annual_price ?? $this->monthly_price * 12,
            default => $this->monthly_price,
        };
    }

    public function getPeriodLabel($period): string
    {
        return match($period) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly (3 months)',
            'semi_annual' => 'Semi-Annual (6 months)',
            'annual' => 'Annual (12 months)',
            default => 'Monthly',
        };
    }

    public function isUnlimitedUsers(): bool
    {
        return is_null($this->max_users);
    }

    public function isUnlimitedBooks(): bool
    {
        return is_null($this->max_books);
    }
}