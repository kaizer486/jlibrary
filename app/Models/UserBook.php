<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $progress_percent
 * @property int $current_page
 * @property string $status
 * @property string|null $purchased_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Book|null $book
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereCurrentPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereProgressPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook wherePurchasedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBook whereUserId($value)
 * @mixin \Eloquent
 */
class UserBook extends Model
{
    protected $table = 'user_books';
    
    protected $fillable = [
        'user_id',
        'book_id',
        'progress_percent',
        'current_page',
        'status'
    ];
    
    protected $casts = [
        'progress_percent' => 'integer',
        'current_page' => 'integer'
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}