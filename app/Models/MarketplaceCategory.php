<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function listings()
    {
        return $this->hasMany(MarketplaceListing::class, 'category_id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->whereHas('listings', function($q) {
            $q->where('status', 'approved');
        });
    }
}