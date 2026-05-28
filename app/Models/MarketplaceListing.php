<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $seller_id
 * @property string $title
 * @property string $description
 * @property string $book_file
 * @property string|null $cover_image
 * @property numeric $price
 * @property string $status
 * @property string|null $admin_notes
 * @property int $views
 * @property int $downloads
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $seller
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereBookFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereSellerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceListing whereViews($value)
 * @mixin \Eloquent
 */
class MarketplaceListing extends Model
{
    protected $table = 'marketplace_listings';
    
    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'book_file',
        'cover_image',
        'price',
        'status',
        'admin_notes',
        'views',
        'downloads'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'views' => 'integer',
        'downloads' => 'integer'
    ];
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}