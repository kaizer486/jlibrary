<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'link',
        'is_featured',
        'published_at',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'date',
        'order' => 'integer'
    ];

    /**
     * Scope active news
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope featured news
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope ordered news
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Scope latest news
     */
    public function scopeLatestPublished($query)
    {
        return $query->orderBy('published_at', 'desc');
    }
}