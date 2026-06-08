<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quote extends Model
{
    protected $fillable = [
        'quote_text',
        'author',
        'category',
        'status',
        'views_count',
        'saves_count',
        'shares_count',
        'scheduled_date',
        'institution_id',
        'created_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'views_count' => 'integer',
        'saves_count' => 'integer',
        'shares_count' => 'integer',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_quotes', 'quote_id', 'user_id')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Get quote of the day based on user's institution
   public static function getQuoteOfTheDay($user)
{
    $today = now()->toDateString();
    $institutionId = $user->institution_id;
    
    $quote = self::active()
        ->where(function($q) use ($institutionId) {
            if ($institutionId) {
                // User belongs to institution → show their institution quotes OR global quotes
                $q->where('institution_id', $institutionId)
                  ->orWhereNull('institution_id');
            } else {
                // User has no institution → only global quotes
                $q->whereNull('institution_id');
            }
        })
        ->where(function($q) use ($today) {
            $q->whereNull('scheduled_date')
              ->orWhere('scheduled_date', $today);
        })
        ->inRandomOrder()
        ->first();
    
    if ($quote) {
        $quote->increment('views_count');
    }
    
    return $quote;
}

    // Get all quotes for admin based on role
    public static function getAdminQuotes($user, $request)
    {
        $query = self::with('creator', 'institution');
        
        // Institution admin sees only their institution quotes
        if ($user->role === 'institution_admin') {
            $query->where('institution_id', $user->institution_id);
        } else {
            // Super admin and admin see only global quotes
            $query->whereNull('institution_id');
        }
        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('quote_text', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->category && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        return $query->latest()->paginate(15);
    }

    public static function getCategories()
    {
        return [
            'motivation' => '🔥 Motivation',
            'education' => '📚 Education',
            'wisdom' => '🧠 Wisdom',
            'success' => '🏆 Success',
            'happiness' => '😊 Happiness',
            'leadership' => '👑 Leadership',
            'creativity' => '🎨 Creativity',
            'life' => '🌟 Life',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>',
            'inactive' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Inactive</span>',
            'draft' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Draft</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Unknown</span>',
        };
    }
}