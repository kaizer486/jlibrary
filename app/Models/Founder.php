<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Founder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'bio',
        'photo',
        'email',
        'phone',
        'social_links',
        'order',
        'is_active'
    ];

    protected $casts = [
        'social_links' => 'array', // This ensures it's cast to array
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Scope active founders
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered founders
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get social link by platform
     */
    public function getSocialLink($platform)
    {
        $links = $this->social_links ?? [];
        return $links[$platform] ?? null;
    }

    /**
     * Get all social links as array
     */
    public function getSocialLinksAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        // If it's already an array, return it
        if (is_array($value)) {
            return $value;
        }
        
        // If it's a JSON string, decode it
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }
}