<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $user_message
 * @property string $ai_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereAiResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AiChat whereUserMessage($value)
 * @mixin \Eloquent
 */
class AiChat extends Model
{
    protected $table = 'ai_chats';
    
    protected $fillable = [
        'user_id',
        'user_message',
        'ai_response'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}