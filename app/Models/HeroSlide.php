<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'slide_type',
        'badge_text',
        'cta_text',
        'cta_url',
        'stats',
        'order',
        'slide_duration',
        'button_color',
        'text_color',
        'settings',
        'is_active'
    ];

    protected $casts = [
        'stats' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
        'slide_duration' => 'integer'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getStatsAttribute($value)
    {
        if (is_null($value)) {
            return [
                ['icon' => 'books', 'number' => '12K+', 'label' => 'Books Available'],
                ['icon' => 'certificate', 'number' => '320+', 'label' => 'Certificates Issued'],
                ['icon' => 'users', 'number' => '8.4K', 'label' => 'Active Learners']
            ];
        }
        
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        
        return $value ?? [];
    }

    public function getSlideTypeLabelAttribute()
    {
        $types = [
            'dashboard' => '📊 Dashboard',
            'books' => '📚 Books',
            'ai' => '🤖 AI Assistant',
            'community' => '🌍 Community',
            'custom' => '🎨 Custom'
        ];
        return $types[$this->slide_type] ?? '📊 Dashboard';
    }

    public function getSlideTypeIconAttribute()
    {
        $icons = [
            'dashboard' => 'ti-layout-dashboard',
            'books' => 'ti-books',
            'ai' => 'ti-robot',
            'community' => 'ti-users',
            'custom' => 'ti-settings'
        ];
        return $icons[$this->slide_type] ?? 'ti-layout-dashboard';
    }
}