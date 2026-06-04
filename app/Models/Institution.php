<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Institution extends Model
{
    protected $table = 'institutions';
    
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address', 'city', 'region',
        'postal_code', 'country', 'logo', 'website', 'type', 'status',
        'subscription_tier', 'subscription_expires_at', 'max_users',
        'max_books', 'settings', 'metadata', 'approved_by', 'approved_at'
    ];
    
    protected $casts = [
        'settings' => 'array',
        'metadata' => 'array',
        'subscription_expires_at' => 'datetime',
        'approved_at' => 'datetime'
    ];
    
    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function wallet()
{
    return $this->hasOne(InstitutionWallet::class);
}

public function withdrawalRequests()
{
    return $this->hasMany(WithdrawalRequest::class);
}

public function subscription()
{
    return $this->hasOne(Subscription::class)->latest();
}

public function createWallet()
{
    return $this->wallet()->create([
        'balance' => 0,
        'total_earned' => 0,
        'total_withdrawn' => 0,
        'pending_withdrawal' => 0,
    ]);
}
 public function books()
{
    try {
        return $this->hasMany(Book::class);
    } catch (\Exception $e) {
        // Return a dummy relationship if table doesn't have column yet
        return $this->hasMany(Book::class)->whereRaw('1=0');
    }
}
    
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    // Get institution admins
    public function admins()
    {
        return $this->users()->where('role', 'institution_admin')->orWhere('is_institution_admin', true);
    }
    
    // Get librarians
    public function librarians()
    {
        return $this->users()->where('role', 'librarian');
    }
    
    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
    
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
    
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }
        return null;
    }
    
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'school' => '🏫 School',
            'college' => '🎓 College',
            'university' => '🏛️ University',
            'library' => '📚 Library',
            'bookstore' => '📖 Bookstore',
            'publisher' => '📰 Publisher',
            'research_center' => '🔬 Research Center',
            default => '🏢 Other'
        };
    }
    
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => '✅ Approved',
            'pending' => '⏳ Pending',
            'suspended' => '⚠️ Suspended',
            'inactive' => '❌ Inactive',
            default => '❓ Unknown'
        };
    }
    
    public function getSubscriptionLabelAttribute(): string
    {
        return match($this->subscription_tier) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            default => '📘 Basic'
        };
    }
    
    public function isSubscriptionActive(): bool
    {
        if (!$this->subscription_expires_at) return true;
        return $this->subscription_expires_at->isFuture();
    }
    
    // Get total users count
    public function getTotalUsersAttribute()
    {
        return $this->users()->count();
    }
    
    // Get total books count
    public function getTotalBooksAttribute()
    {
        return $this->books()->count();
    }
}