<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $region
 * @property string|null $postal_code
 * @property string|null $country
 * @property string|null $logo
 * @property string|null $website
 * @property string|null $type
 * @property string $status
 * @property string|null $subscription_tier
 * @property \Illuminate\Support\Carbon|null $subscription_expires_at
 * @property int|null $max_users
 * @property int|null $max_books
 * @property array|null $settings
 * @property array|null $metadata
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property bool $is_featured
 * @property bool $is_verified
 * @property int $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read \App\Models\InstitutionWallet|null $wallet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WithdrawalRequest> $withdrawalRequests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read \App\Models\Subscription|null $activeSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read \App\Models\User|null $approvedBy
 */
class Institution extends Model
{
    use SoftDeletes;
    
    protected $table = 'institutions';
    
  protected $fillable = [
    'name', 'slug', 'email', 'phone', 'address', 'city', 'region',
    'postal_code', 'country', 'logo', 'website', 'type', 'status',
    'subscription_tier', 'subscription_expires_at', 'max_users',
    'max_books', 'settings', 'metadata', 'approved_by', 'approved_at',
    'is_featured', 'is_verified', 'views_count', 'institution_id',
    // ==========================================
    // REMINDER FIELDS 
    // ==========================================
    'reminder_30_sent_at',
    'reminder_15_sent_at',
    'reminder_7_sent_at',
    'reminder_3_sent_at',
    'reminder_1_sent_at',
    'reminder_expired_sent_at',
];
    
 protected $casts = [
    'settings' => 'array',
    'metadata' => 'array',
    'subscription_expires_at' => 'datetime',
    'approved_at' => 'datetime',
    'is_featured' => 'boolean',
    'is_verified' => 'boolean',
    'views_count' => 'integer',
    // ==========================================
    // REMINDER CASTS (ADD THESE)
    // ==========================================
    'reminder_30_sent_at' => 'datetime',
    'reminder_15_sent_at' => 'datetime',
    'reminder_7_sent_at' => 'datetime',
    'reminder_3_sent_at' => 'datetime',
    'reminder_1_sent_at' => 'datetime',
    'reminder_expired_sent_at' => 'datetime',
];
    protected $appends = [
        'logo_url', 'type_label', 'status_label', 'subscription_label',
        'total_users', 'total_books',
    ];
    
    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function wallet()
    {
        return $this->hasOne(InstitutionWallet::class);
    }
/**
 * Create a wallet for the institution.
 */
public function createWallet()
{
    // Check if wallet already exists
    if ($this->wallet) {
        return $this->wallet;
    }
    
    return $this->wallet()->create([
        'balance' => 0,
        'total_deposited' => 0,
        'total_withdrawn' => 0,
    ]);
}
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
    }

    // ==========================================
    // SUBSCRIPTION RELATIONSHIPS (Polymorphic)
    // ==========================================
    
    /**
     * Get all subscriptions for this institution.
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    /**
     * Get the active subscription.
     */
   /**
 * Get the active subscription.
 */
public function activeSubscription()
{
    return $this->morphOne(Subscription::class, 'subscribable')
        ->where('status', 'active')
        ->where(function($query) {
            $query->whereNull('ends_at')      // ✅ Use ends_at
                  ->orWhere('ends_at', '>', now());  // ✅ Use ends_at
        })
        ->latest();
}

    /**
     * Get subscription history (all subscriptions).
     */
    public function subscriptionHistory()
    {
        return $this->morphMany(Subscription::class, 'subscribable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest subscription (any status).
     */
    public function latestSubscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable')->latest();
    }
    
    public function books()
    {
        try {
            return $this->hasMany(Book::class);
        } catch (\Exception $e) {
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
    
    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
    
    public function scopeActive($query)
    {
        return $query->where('status', 'approved');
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
    
    public function scopeSearch($query, $search)
    {
        if (empty($search)) return $query;
        
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('city', 'LIKE', "%{$search}%")
              ->orWhere('region', 'LIKE', "%{$search}%");
        });
    }
    
    public function scopeOfType($query, $type)
    {
        return $type ? $query->where('type', $type) : $query;
    }
    
    public function scopeOfStatus($query, $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }
    
    public function scopeSortBy($query, $sort)
    {
        switch ($sort) {
            case 'newest':
                return $query->latest();
            case 'oldest':
                return $query->oldest();
            case 'name_asc':
                return $query->orderBy('name');
            case 'name_desc':
                return $query->orderBy('name', 'desc');
            case 'most_members':
                return $query->withCount('users')->orderBy('users_count', 'desc');
            case 'most_books':
                return $query->withCount('books')->orderBy('books_count', 'desc');
            default:
                return $query->latest();
        }
    }
    
    // ==========================================
    // VALIDATION & CAPACITY METHODS
    // ==========================================
    
    /**
     * Check if institution can accept new members.
     */
    public function canAddUser(): bool
    {
        if (!$this->max_users) {
            return true;
        }
        return $this->users()->count() < $this->max_users;
    }

    /**
     * Get available user slots.
     */
    public function getAvailableUserSlots(): int
    {
        if (!$this->max_users) {
            return PHP_INT_MAX;
        }
        $currentUsers = $this->users()->count();
        return max(0, $this->max_users - $currentUsers);
    }

    /**
     * Check if institution can add more books.
     */
    public function canAddBook(): bool
    {
        if (!$this->max_books) {
            return true;
        }
        return $this->books()->count() < $this->max_books;
    }

    /**
     * Get available book slots.
     */
    public function getAvailableBookSlots(): int
    {
        if (!$this->max_books) {
            return PHP_INT_MAX;
        }
        $currentBooks = $this->books()->count();
        return max(0, $this->max_books - $currentBooks);
    }
    
  public function shelves()
{
    return $this->hasMany(Shelf::class);
}
    /**
     * Get cached total users count.
     */
    public function getCachedTotalUsers()
    {
        return Cache::remember("institution_{$this->id}_users_count", 3600, function () {
            return $this->users()->count();
        });
    }
    
    /**
     * Get cached total books count.
     */
    public function getCachedTotalBooks()
    {
        return Cache::remember("institution_{$this->id}_books_count", 3600, function () {
            return $this->books()->count();
        });
    }
    
    /**
     * Clear all cached data for this institution.
     */
    public function clearCache(): void
    {
        Cache::forget("institution_{$this->id}_users_count");
        Cache::forget("institution_{$this->id}_books_count");
        Cache::forget("institution_{$this->id}_stats");
    }
    
    /**
     * Increment views count.
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
    
    // ==========================================
    // HELPER METHODS
    // ==========================================
    
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
    
    public function isActive(): bool
    {
        return $this->isApproved() && !$this->isSuspended();
    }
    
    public function isFeatured(): bool
    {
        return $this->is_featured ?? false;
    }
    
    public function isVerified(): bool
    {
        return $this->is_verified ?? false;
    }
    
    // ==========================================
    // SUBSCRIPTION METHODS
    // ==========================================
    
    /**
     * Check if institution has an active subscription.
     */
    public function isSubscriptionActive(): bool
    {
        // Check if institution has an active subscription via polymorphic relationship
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription && $activeSubscription->isActive()) {
            return true;
        }
        
        // Fallback: check the institution's subscription_tier and expiry date
        if ($this->subscription_tier && $this->subscription_tier !== 'free' && 
            $this->subscription_expires_at && $this->subscription_expires_at->isFuture()) {
            return true;
        }
        
        return false;
    }

    /**
     * Get days left in subscription.
     */
    public function getDaysLeft(): int
    {
        // Check active subscription first
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription && $activeSubscription->end_date) {
            $days = $activeSubscription->daysRemaining();
            if ($days > 0) {
                return $days;
            }
        }
        
        // Fallback: check institution's expiry date
        if ($this->subscription_expires_at && $this->subscription_expires_at->isFuture()) {
            return max(0, now()->diffInDays($this->subscription_expires_at, false));
        }
        
        return 0;
    }

    /**
     * Get subscription progress percentage.
     */
    public function getSubscriptionProgress(): int
    {
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription && $activeSubscription->start_date && $activeSubscription->end_date) {
            $total = $activeSubscription->start_date->diffInDays($activeSubscription->end_date);
            $elapsed = $activeSubscription->start_date->diffInDays(now());
            $remaining = $total - $elapsed;
            
            if ($total <= 0) return 0;
            return min(100, max(0, round(($remaining / $total) * 100)));
        }
        
        // Fallback: use institution dates
        if ($this->subscription_expires_at && $this->subscription_started_at) {
            $total = $this->subscription_started_at->diffInDays($this->subscription_expires_at);
            $elapsed = $this->subscription_started_at->diffInDays(now());
            $remaining = $total - $elapsed;
            
            if ($total <= 0) return 0;
            return min(100, max(0, round(($remaining / $total) * 100)));
        }
        
        return 0;
    }

    /**
     * Get subscription status color.
     */
    public function getSubscriptionStatusColor(): string
    {
        $daysLeft = $this->getDaysLeft();
        
        if ($daysLeft <= 0) {
            return 'red';
        } elseif ($daysLeft <= 7) {
            return 'red';
        } elseif ($daysLeft <= 30) {
            return 'yellow';
        } elseif ($daysLeft <= 60) {
            return 'orange';
        }
        return 'green';
    }

    /**
     * Get subscription status label with icon.
     */
    public function getSubscriptionStatusLabel(): string
    {
        $daysLeft = $this->getDaysLeft();
        
        if ($daysLeft <= 0) {
            return '⚠️ Expired';
        } elseif ($daysLeft <= 7) {
            return "🔴 Expires in {$daysLeft} days";
        } elseif ($daysLeft <= 30) {
            return "🟡 Expires in {$daysLeft} days";
        } elseif ($daysLeft <= 60) {
            return "🟠 Expires in {$daysLeft} days";
        }
        return "✅ Active ({$daysLeft} days left)";
    }

    /**
     * Get plan label.
     */
    public function getPlanLabel(): string
    {
        // Check active subscription first
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription && $activeSubscription->plan) {
            return $activeSubscription->plan->name ?? match($activeSubscription->plan) {
                'basic' => '📘 Basic',
                'premium' => '📚 Premium',
                'enterprise' => '🏢 Enterprise',
                default => '📘 Basic'
            };
        }
        
        // Fallback: use institution's subscription_tier
        return match($this->subscription_tier) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            default => '📘 Basic'
        };
    }

    /**
     * Get the active subscription instance.
     */
    public function getActiveSubscription()
    {
        return $this->activeSubscription;
    }

    /**
     * Get all subscription history.
     */
    public function getSubscriptionHistory()
    {
        return $this->subscriptionHistory;
    }
    
    // ==========================================
    // ACCESSORS
    // ==========================================
    
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
        $activeSubscription = $this->activeSubscription;
        
        if ($activeSubscription && $activeSubscription->plan) {
            return $activeSubscription->plan->name ?? '📘 Basic';
        }
        
        return match($this->subscription_tier) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            default => '📘 Basic'
        };
    }
    
    // Get total users count (non-cached, for real-time)
    public function getTotalUsersAttribute()
    {
        return $this->users()->count();
    }
    
    // Get total books count (non-cached, for real-time)
    public function getTotalBooksAttribute()
    {
        return $this->books()->count();
    }
    
    // ==========================================
    // STATS METHOD
    // ==========================================
    
    /**
     * Get comprehensive institution stats.
     */
    public function getStats(): array
    {
        return [
            'total_members' => $this->users()->count(),
            'total_books' => $this->books()->count(),
            'total_admins' => $this->admins()->count(),
            'total_librarians' => $this->librarians()->count(),
            'wallet_balance' => $this->wallet?->balance ?? 0,
            'pending_withdrawals' => $this->withdrawalRequests()->where('status', 'pending')->sum('amount'),
            'pending_join_requests' => \App\Models\JoinRequest::where('institution_id', $this->id)->where('status', 'pending')->count(),
            'subscription_active' => $this->isSubscriptionActive(),
            'subscription_days_left' => $this->getDaysLeft(),
            'subscription_plan' => $this->getPlanLabel(),
        ];
    }
        // ==========================================
    // JOIN METHOD CHECKS
    // ==========================================
    
    /**
     * Check if institution requires approval to join.
     * School, College, University require approval.
     */
    public function requiresApproval(): bool
    {
        return in_array($this->type, ['school', 'college', 'university']);
    }

    /**
     * Check if institution allows free join (no approval needed).
     * Library, Bookstore, Publisher, Research Center, Other.
     */
    public function allowsFreeJoin(): bool
    {
        return !$this->requiresApproval();
    }

    /**
     * Get the join type label for display.
     */
    public function getJoinTypeLabelAttribute(): string
    {
        if ($this->requiresApproval()) {
            return '🛡️ Requires Approval';
        }
        return '✅ Instant Join';
    }

    /**
     * Get the join button text.
     */
    public function getJoinButtonTextAttribute(): string
    {
        if ($this->requiresApproval()) {
            return 'Request to Join';
        }
        return 'Join Now (Free)';
    }

    /**
     * Get the join button color class.
     */
    public function getJoinButtonColorAttribute(): string
    {
        if ($this->requiresApproval()) {
            return 'bg-gradient-to-r from-purple-600 to-pink-600';
        }
        return 'bg-gradient-to-r from-emerald-600 to-emerald-500';
    }
}