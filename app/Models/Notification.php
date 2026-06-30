<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property array<array-key, mixed>|null $data
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 * @mixin \Eloquent
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

   protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    
    // ==========================================
    // TYPES - Platform Wide
    // ==========================================
    const TYPE_PURCHASE = 'purchase';
    const TYPE_CERTIFICATE = 'certificate';
    const TYPE_QUIZ = 'quiz';
    const TYPE_REVIEW = 'review';
    const TYPE_BOOK_APPROVAL = 'book_approval';
    const TYPE_INSTITUTION_CREATION = 'institution_creation';
    const TYPE_JOIN_REQUEST = 'join_request';

 // ==========================================
    // TYPES - Library Specific
    // ==========================================
    const TYPE_LIBRARY_BOOK_ADDED = 'library_book_added';
    const TYPE_LIBRARY_BOOK_APPROVED = 'library_book_approved';
    const TYPE_LIBRARY_BOOK_REJECTED = 'library_book_rejected';
    const TYPE_LIBRARY_JOIN_REQUEST = 'library_join_request';
    const TYPE_LIBRARY_JOIN_APPROVED = 'library_join_approved';
    const TYPE_LIBRARY_JOIN_REJECTED = 'library_join_rejected';
    const TYPE_LIBRARY_MEMBER_JOINED = 'library_member_joined';
    const TYPE_LIBRARY_SHELF_FULL = 'library_shelf_full';
    const TYPE_LIBRARY_ANNOUNCEMENT = 'library_announcement';
    const TYPE_LIBRARY_NEW_FEATURE = 'library_new_feature';



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

     public function isUnread(): bool
    {
        return !$this->is_read;
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getIconAttribute(): string
    {
        $icons = [
            self::TYPE_PURCHASE => 'ti-shopping-cart',
            self::TYPE_CERTIFICATE => 'ti-certificate',
            self::TYPE_QUIZ => 'ti-brain',
            self::TYPE_REVIEW => 'ti-star',
            self::TYPE_BOOK_APPROVAL => 'ti-check',
            self::TYPE_INSTITUTION_CREATION => 'ti-building',
            self::TYPE_JOIN_REQUEST => 'ti-user-plus',
            self::TYPE_LIBRARY_BOOK_ADDED => 'ti-book',
            self::TYPE_LIBRARY_BOOK_APPROVED => 'ti-check',
            self::TYPE_LIBRARY_BOOK_REJECTED => 'ti-x',
            self::TYPE_LIBRARY_JOIN_REQUEST => 'ti-user-plus',
            self::TYPE_LIBRARY_JOIN_APPROVED => 'ti-user-check',
            self::TYPE_LIBRARY_JOIN_REJECTED => 'ti-user-x',
            self::TYPE_LIBRARY_MEMBER_JOINED => 'ti-users',
            self::TYPE_LIBRARY_SHELF_FULL => 'ti-alert-triangle',
            self::TYPE_LIBRARY_ANNOUNCEMENT => 'ti-bullhorn',
            self::TYPE_LIBRARY_NEW_FEATURE => 'ti-rocket',
        ];

        return $icons[$this->type] ?? 'ti-bell';
    }
     public function getColorAttribute(): string
    {
        $colors = [
            self::TYPE_PURCHASE => 'text-green-400',
            self::TYPE_CERTIFICATE => 'text-yellow-400',
            self::TYPE_QUIZ => 'text-blue-400',
            self::TYPE_REVIEW => 'text-pink-400',
            self::TYPE_BOOK_APPROVAL => 'text-emerald-400',
            self::TYPE_INSTITUTION_CREATION => 'text-indigo-400',
            self::TYPE_JOIN_REQUEST => 'text-yellow-400',
            self::TYPE_LIBRARY_BOOK_ADDED => 'text-purple-400',
            self::TYPE_LIBRARY_BOOK_APPROVED => 'text-emerald-400',
            self::TYPE_LIBRARY_BOOK_REJECTED => 'text-red-400',
            self::TYPE_LIBRARY_JOIN_REQUEST => 'text-yellow-400',
            self::TYPE_LIBRARY_JOIN_APPROVED => 'text-emerald-400',
            self::TYPE_LIBRARY_JOIN_REJECTED => 'text-red-400',
            self::TYPE_LIBRARY_MEMBER_JOINED => 'text-cyan-400',
            self::TYPE_LIBRARY_SHELF_FULL => 'text-red-400',
            self::TYPE_LIBRARY_ANNOUNCEMENT => 'text-orange-400',
            self::TYPE_LIBRARY_NEW_FEATURE => 'text-pink-400',
        ];

        return $colors[$this->type] ?? 'text-gray-400';
    }

     public function getBadgeClassAttribute(): string
    {
        $classes = [
            self::TYPE_PURCHASE => 'bg-green-500/20 text-green-400 border border-green-500/20',
            self::TYPE_CERTIFICATE => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/20',
            self::TYPE_QUIZ => 'bg-blue-500/20 text-blue-400 border border-blue-500/20',
            self::TYPE_REVIEW => 'bg-pink-500/20 text-pink-400 border border-pink-500/20',
            self::TYPE_BOOK_APPROVAL => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            self::TYPE_INSTITUTION_CREATION => 'bg-indigo-500/20 text-indigo-400 border border-indigo-500/20',
            self::TYPE_JOIN_REQUEST => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/20',
            self::TYPE_LIBRARY_BOOK_ADDED => 'bg-purple-500/20 text-purple-400 border border-purple-500/20',
            self::TYPE_LIBRARY_BOOK_APPROVED => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            self::TYPE_LIBRARY_BOOK_REJECTED => 'bg-red-500/20 text-red-400 border border-red-500/20',
            self::TYPE_LIBRARY_JOIN_REQUEST => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/20',
            self::TYPE_LIBRARY_JOIN_APPROVED => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            self::TYPE_LIBRARY_JOIN_REJECTED => 'bg-red-500/20 text-red-400 border border-red-500/20',
            self::TYPE_LIBRARY_MEMBER_JOINED => 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/20',
            self::TYPE_LIBRARY_SHELF_FULL => 'bg-red-500/20 text-red-400 border border-red-500/20',
            self::TYPE_LIBRARY_ANNOUNCEMENT => 'bg-orange-500/20 text-orange-400 border border-orange-500/20',
            self::TYPE_LIBRARY_NEW_FEATURE => 'bg-pink-500/20 text-pink-400 border border-pink-500/20',
        ];

        return $classes[$this->type] ?? 'bg-gray-500/20 text-gray-400 border border-gray-500/20';
    }

    public function isLibraryNotification(): bool
    {
        return str_starts_with($this->type, 'library_');
    }
}