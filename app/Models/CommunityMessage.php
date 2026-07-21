<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunityMessage extends Model
{
    protected $table = 'community_messages';

    protected $fillable = [
        'group_id',
        'user_id',
        'message',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CommunityGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}