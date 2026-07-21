<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunityGroup extends Model
{
    protected $table = 'community_groups';

    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'created_by',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommunityMember::class, 'group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommunityMessage::class, 'group_id');
    }
}