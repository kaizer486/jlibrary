<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $referrer_id
 * @property int $referred_id
 * @property string $referral_code
 * @property numeric $referrer_earned
 * @property numeric $referred_earned
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $referred
 * @property-read \App\Models\User|null $referrer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferredEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferredId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferrerEarned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Referral whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Referral extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'referrer_earned',
        'referred_earned',
        'status'
    ];

    protected $casts = [
        'referrer_earned' => 'decimal:2',
        'referred_earned' => 'decimal:2',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    public function complete()
    {
        $this->status = 'completed';
        $this->save();
    }
}