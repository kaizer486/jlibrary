<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMessage whereUserId($value)
 * @mixin \Eloquent
 */
class GroupMessage extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'message'
    ];
    
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}