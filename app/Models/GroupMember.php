<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Group|null $group
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupMember whereUserId($value)
 * @mixin \Eloquent
 */
class GroupMember extends Model
{
    protected $table = 'group_members';
    
    protected $fillable = [
        'group_id',
        'user_id',
        'role'
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