<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WalletTopup query()
 * @mixin \Eloquent
 */
class WalletTopup extends Model
{
    protected $fillable = ['user_id', 'amount', 'method', 'reference', 'status'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}