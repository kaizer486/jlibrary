<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
 * @property \Illuminate\Support\Carbon|null $reminder_30_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_15_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_7_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_3_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_1_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_expired_sent_at
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

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating
        static::creating(function ($institution) {
            if (empty($institution->slug)) {
                $slug = Str::slug($institution->name);
                
                // Make sure slug is unique
                $originalSlug = $slug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $institution->slug = $slug;
            }
        });

        // Auto-update slug when name changes
        static::updating(function ($institution) {
            if ($institution->isDirty('name') && empty($institution->slug)) {
                $slug = Str::slug($institution->name);
                
                $originalSlug = $slug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $institution->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                $institution->slug = $slug;
            }
        });
    }
    
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
        // REMINDER CASTS
        // ==========================================
        'reminder_30_sent_at' => 'datetime',
        'reminder_15_sent_at' => 'datetime',
        'reminder_7_sent_at' => 'datetime',
        'reminder_3_sent_at' => 'datetime',
        'reminder_1_sent_at' => 'datetime',
        'reminder_expired_sent_at' => 'datetime',
        'deleted_at' => 'datetime',
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
    // SUBSCRIPTION RELATIONSHIPS
    // ==========================================
    
    /**
     * Get all subscriptions for this institution (polymorphic).
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    /**
     * Get the active subscription.
     * ✅ FIXED: Use hasOne with explicit conditions
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'subscribable_id')
            ->where('subscribable_type', Institution::class)
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->latest();
    }

    /**
     * Get subscription history (all subscriptions ordered by latest).
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
    
    public function shelves()
    {
        return $this->hasMany(Shelf::class);
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
    // SUBSCRIPTION METHODS - FIXED
    // ==========================================
    
    /**
     * Check if institution has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        // Check 1: Institution fields
        if ($this->subscription_tier && $this->subscription_tier !== 'free' && $this->subscription_expires_at) {
            return $this->subscription_expires_at > now();
        }
        
        // Check 2: Subscriptions table (use property, not method)
        $subscription = $this->activeSubscription;
        
        return $subscription !== null;
    }

    /**
     * Get days left in subscription.
     */
    public function getSubscriptionDaysLeft(): int
    {
        // Check 1: Institution fields
        if ($this->subscription_expires_at) {
            if ($this->subscription_expires_at->isPast()) {
                return 0;
            }
            return max(0, now()->diffInDays($this->subscription_expires_at, false));
        }
        
        // Check 2: Subscriptions table (use property, not method)
        $subscription = $this->activeSubscription;
        
        if ($subscription && $subscription->ends_at) {
            if ($subscription->ends_at->isPast()) {
                return 0;
            }
            return max(0, now()->diffInDays($subscription->ends_at, false));
        }
        
        return 0;
    }

    /**
     * Get plan label.
     */
    public function getPlanLabel(): string
    {
        // Check 1: Institution fields
        if ($this->subscription_tier) {
            return match($this->subscription_tier) {
                'basic' => '📘 Basic',
                'premium' => '📚 Premium',
                'enterprise' => '🏢 Enterprise',
                'free' => '🆓 Free',
                default => '📘 ' . ucfirst($this->subscription_tier),
            };
        }
        
        // Check 2: Subscriptions table (use property, not method)
        $subscription = $this->activeSubscription;
        
        if ($subscription) {
            return match($subscription->plan) {
                'basic' => '📘 Basic',
                'premium' => '📚 Premium',
                'enterprise' => '🏢 Enterprise',
                'free' => '🆓 Free',
                default => '📘 ' . ucfirst($subscription->plan),
            };
        }
        
        return '🆓 Free';
    }

    /**
     * Get subscription status color.
     */
    public function getSubscriptionStatusColor(): string
    {
        $daysLeft = $this->getSubscriptionDaysLeft();
        
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
        if (!$this->hasActiveSubscription()) {
            return '⚠️ No Active Subscription';
        }
        
        $daysLeft = $this->getSubscriptionDaysLeft();
        
        if ($daysLeft <= 0) {
            return '🔴 Expired';
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
     * Check if institution has an active subscription (alias).
     */
    public function isSubscriptionActive(): bool
    {
        return $this->hasActiveSubscription();
    }

    /**
     * Get days left (alias).
     */
    public function getDaysLeft(): int
    {
        return $this->getSubscriptionDaysLeft();
    }

    /**
     * Get subscription progress percentage.
     */
    public function getSubscriptionProgress(): int
    {
        $subscription = $this->activeSubscription;
        
        if ($subscription && $subscription->starts_at && $subscription->ends_at) {
            $total = $subscription->starts_at->diffInDays($subscription->ends_at);
            $elapsed = $subscription->starts_at->diffInDays(now());
            
            if ($total <= 0) return 0;
            return min(100, max(0, round(($elapsed / $total) * 100)));
        }
        
        // Fallback: use institution dates
        if ($this->subscription_expires_at) {
            $start = $this->created_at ?? now()->subDays(30);
            $total = $start->diffInDays($this->subscription_expires_at);
            $elapsed = $start->diffInDays(now());
            
            if ($total <= 0) return 0;
            return min(100, max(0, round(($elapsed / $total) * 100)));
        }
        
        return 0;
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
        return $this->subscriptions()->orderBy('created_at', 'desc')->get();
    }

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
    // STATUS METHODS
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
    // JOIN METHOD CHECKS
    // ==========================================
    
    /**
     * Check if institution requires approval to join.
     */
    public function requiresApproval(): bool
    {
        return in_array($this->type, ['school', 'college', 'university']);
    }

    /**
     * Check if institution allows free join (no approval needed).
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
        return $this->getPlanLabel();
    }
    
    public function getTotalUsersAttribute()
    {
        return $this->users()->count();
    }
    
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
            'subscription_active' => $this->hasActiveSubscription(),
            'subscription_days_left' => $this->getSubscriptionDaysLeft(),
            'subscription_plan' => $this->getPlanLabel(),
            'subscription_progress' => $this->getSubscriptionProgress(),
        ];
    }
}