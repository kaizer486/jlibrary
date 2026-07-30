<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\Certificate;  
use App\Models\Transaction;
use App\Models\BookshopBook;
use App\Traits\HasXpRewards;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles; 
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPasswordNotification;

/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $google_id
 * @property string $role
 * @property float $wallet_balance
 * @property string|null $mpesa_phone
 * @property string|null $tigopesa_phone
 * @property string|null $halopesa_phone
 * @property string|null $bank_name
 * @property string|null $bank_account_number
 * @property string|null $bank_account_name
 * @property string|null $avatar
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $bio
 * @property string|null $cover_photo
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property string|null $linkedin_url
 * @property string|null $github_url
 * @property string|null $instagram_url
 * @property string|null $website_url
 * @property string|null $location
 * @property string|null $occupation
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $referral_code
 * @property int|null $referred_by
 * @property numeric $referral_earnings
 * @property int|null $institution_id 
 * @property bool $is_institution_admin
 * @property string|null $position
 * @property string|null $subscription_tier
 * @property \Illuminate\Support\Carbon|null $subscription_expires_at
 * @property \Illuminate\Support\Carbon|null $reminder_30_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_15_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_7_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_3_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_1_sent_at
 * @property \Illuminate\Support\Carbon|null $reminder_expired_sent_at
 * @property-read int $xp_points
 * @property-read int $level
 * @property-read int $streak_days
 * @property-read string|null $last_active_at
 * @property-read int $combined_score
 * @property-read int $level_progress
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AiChat> $aiChats
 * @property-read int|null $ai_chats_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Certificate> $certificates
 * @property-read int|null $certificates_count
 * @property-read mixed $avatar_url
 * @property-read mixed $average_quiz_score
 * @property-read mixed $completed_referrals
 * @property-read mixed $cover_photo_url
 * @property-read mixed $joined_date
 * @property-read mixed $pending_referrals
 * @property-read mixed $referral_link
 * @property-read array $social_links
 * @property-read mixed $total_books_read
 * @property-read mixed $total_certificates
 * @property-read mixed $total_quizzes_passed
 * @property-read mixed $total_quizzes_taken
 * @property-read mixed $total_referrals
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Group> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MarketplaceListing> $marketplaceListings
 * @property-read int|null $marketplace_listings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GroupMessage> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $purchasedBooks
 * @property-read int|null $purchased_books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuizAttempt> $quizAttempts
 * @property-read int|null $quiz_attempts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Referral> $referrals
 * @property-read int|null $referrals_count
 * @property-read User|null $referredBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $referredUsers
 * @property-read int|null $referred_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCoverPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGithubUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHalopesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereInstagramUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMpesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferralCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferralEarnings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReferredBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTigopesaPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWalletBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsiteUrl($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles, HasXpRewards,SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'google_id',
        'role',
        'wallet_balance',
        'avatar',
        'bio',
        'cover_photo',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'github_url',
        'instagram_url',
        'website_url',
        'location',
        'occupation',
        'birth_date',
        'referral_code',
        'referred_by',
        'referral_earnings',
        'institution_id',
        'position',
        'is_institution_admin',
        // ==========================================
        // SUBSCRIPTION & REMINDER FIELDS
        // ==========================================
        'subscription_tier',
        'subscription_expires_at',
        'reminder_30_sent_at',
        'reminder_15_sent_at',
        'reminder_7_sent_at',
        'reminder_3_sent_at',
        'reminder_1_sent_at',
        'reminder_expired_sent_at',
           // ==========================================
    // SELLER APPROVAL FIELDS 
    // ==========================================
    'author_approved_at',
    'bookseller_approved_at',
    'author_approved_by',
    'bookseller_approved_by',

    'coins',
    'referral_earnings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'wallet_balance' => 'float',
        'birth_date' => 'date',
        'referral_earnings' => 'decimal:2',
        // ==========================================
        // SUBSCRIPTION & REMINDER CASTS
        // ==========================================
        'subscription_expires_at' => 'datetime',
        'reminder_30_sent_at' => 'datetime',
        'reminder_15_sent_at' => 'datetime',
        'reminder_7_sent_at' => 'datetime',
        'reminder_3_sent_at' => 'datetime',
        'reminder_1_sent_at' => 'datetime',
        'reminder_expired_sent_at' => 'datetime',
        'deleted_at' => 'datetime',
           // ==========================================
    // SELLER APPROVAL CAST
    // ==========================================
    'author_approved_at' => 'datetime',
    'bookseller_approved_at' => 'datetime',
    ];

    // ==========================================
    // API TOKEN METHODS
    // ==========================================

    /**
     * Create a new API token for the user
     */
    public function createApiToken(string $name = 'mobile_app', array $abilities = ['*']): array
    {
        $token = $this->createToken($name, $abilities);
        
        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => null,
        ];
    }
    
    /**
     * Revoke all user tokens (logout from all devices)
     */
    public function revokeAllTokens(): void
    {
        $this->tokens()->delete();
    }

    // ========== APPLICATION RELATIONSHIPS ==========
    
    public function applications()
    {
        return $this->hasMany(Application::class);
    }


// ==========================================
// MARKETPLACE ORDERS - ADD THIS ENTIRE BLOCK
// ==========================================

/**
 * Orders where user is the seller
 */
public function sellerOrders()
{
    return $this->hasMany(MarketplaceOrder::class, 'seller_id');
}

/**
 * Orders where user is the buyer
 */
public function buyerOrders()
{
    return $this->hasMany(MarketplaceOrder::class, 'buyer_id');
}

/**
 * Add user to an institution (handles both pivot and legacy field).
 */
public function addToInstitution($institutionId, $role = 'member')
{
    // Add to pivot table
    $this->institutions()->syncWithoutDetaching([
        $institutionId => [
            'role' => $role,
            'status' => 'active',
            'joined_at' => now(),
        ]
    ]);
    
    // Update legacy field
    $this->institution_id = $institutionId;
    if ($role === 'admin' || $role === 'institution_admin') {
        $this->is_institution_admin = true;
    }
    $this->save();
    
    return $this;
}

/**
 * Remove user from an institution.
 */
public function removeFromInstitution($institutionId)
{
    // Remove from pivot table
    $this->institutions()->detach($institutionId);
    
    // If this was the primary institution, clear legacy field
    if ($this->institution_id == $institutionId) {
        $this->institution_id = null;
        $this->is_institution_admin = false;
        $this->save();
    }
    
    return $this;
}
/**
 * Alias for sellerOrders (for consistency with other parts of the app)
 */


// ==========================================
// MARKETPLACE EARNINGS METHODS - ADD THIS
// ==========================================

/**
 * Get total marketplace earnings (completed orders only)
 */
public function getTotalMarketplaceEarningsAttribute()
{
    return $this->sellerOrders()
        ->where('status', 'completed')
        ->sum('seller_earnings');
}

/**
 * Get pending marketplace orders count
 */
public function getPendingMarketplaceOrdersAttribute()
{
    return $this->sellerOrders()
        ->where('status', 'pending')
        ->count();
}

/**
 * Get total sales count
 */
public function getTotalSalesAttribute()
{
    return $this->sellerOrders()
        ->where('status', 'completed')
        ->count();
}


    // ========== ROLE METHODS (Using Spatie) ==========
    
  public function isSuperAdmin(): bool
{
    return $this->hasRole('super_admin') || $this->role === 'super_admin';
}

  /**
     * Check if user is Media Team
     */
    public function isMediaTeam(): bool
    {
        return $this->hasRole('media_team');
    }
 

   public function isAdmin(): bool
{
    return $this->hasRole('admin') || $this->role === 'admin';
}

public function isUser(): bool
{
    return $this->hasRole('user') || $this->role === 'user';
}
    
  public function isInstitutionAdmin(): bool
{
    return $this->hasRole('institution_admin') || 
           $this->role === 'institution_admin' || 
           $this->is_institution_admin === true;
}

    public function isLibrarian(): bool
    {
        return $this->hasRole('librarian');
    }

    public function isInstructor(): bool
    {
        return $this->hasRole('instructor');
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author');
    }

    public function isBookseller(): bool
    {
        return $this->hasRole('bookseller');
    }

    public function isPublisher(): bool
    {
        return $this->hasRole('publisher');
    }

    // ==========================================
    // ROLE METHODS (Institution Type Based)
    // ==========================================

    /**
     * Get role name based on institution type.
     */
    public static function getRoleForInstitutionType(string $type): string
    {
        return match($type) {
            'school' => 'school_admin',
            'college' => 'college_admin',
            'university' => 'university_admin',
            'library' => 'library_admin',
            'bookstore' => 'bookstore_admin',
            'publisher' => 'publisher_admin',
            'research_center' => 'researcher',
            default => 'institution_admin',
        };
    }

    /**
     * Get the role label for display.
     */
   /**
 * Get the role label for display.
 */
public function getRoleLabel(): string
{
    $role = $this->getRoleNames()->first() ?? 'user';
    
    return match($role) {
        'super_admin' => '👑 Super Admin',
        'media_team' => '🎨 Media Team',
        'admin' => '🛡️ Admin',
        'institution_admin' => '🏢 Institution Admin',
        'school_admin' => '🏫 School Admin',
        'college_admin' => '🎓 College Admin',
        'university_admin' => '🏛️ University Admin',
        'library_admin' => '📚 Library Admin',
        'bookstore_admin' => '📖 Bookstore Admin',
        'publisher_admin' => '📰 Publisher Admin',
        'researcher' => '🔬 Researcher',
        'librarian' => '📚 Librarian',
        'instructor' => '👨‍🏫 Instructor',
        'author' => '✍️ Author',
        'bookseller' => '📖 Bookseller',
        default => '👤 Member',
    };
}

    /**
 * Get role badge class.
 */
public function getRoleBadgeClass(): string
{
    $role = $this->getRoleNames()->first() ?? 'user';
    
    return match($role) {
        'super_admin' => 'bg-red-100 text-red-700',
        'media_team' => 'bg-purple-100 text-purple-700',
        'admin' => 'bg-purple-100 text-purple-700',
        'institution_admin' => 'bg-blue-100 text-blue-700',
        'school_admin' => 'bg-green-100 text-green-700',
        'college_admin' => 'bg-cyan-100 text-cyan-700',
        'university_admin' => 'bg-indigo-100 text-indigo-700',
        'library_admin' => 'bg-sky-100 text-sky-700',
        'bookstore_admin' => 'bg-amber-100 text-amber-700',
        'publisher_admin' => 'bg-fuchsia-100 text-fuchsia-700',
        'researcher' => 'bg-teal-100 text-teal-700',
        'librarian' => 'bg-blue-100 text-blue-700',
        'instructor' => 'bg-green-100 text-green-700',
        'author' => 'bg-purple-100 text-purple-700',
        'bookseller' => 'bg-orange-100 text-orange-700',
        default => 'bg-gray-100 text-gray-700',
    };
}
    /**
     * Check if user is a specific type of institution admin.
     */
    public function isSchoolAdmin(): bool
    {
        return $this->hasRole('school_admin');
    }

    public function isCollegeAdmin(): bool
    {
        return $this->hasRole('college_admin');
    }

    public function isUniversityAdmin(): bool
    {
        return $this->hasRole('university_admin');
    }

    public function isLibraryAdmin(): bool
    {
        return $this->hasRole('library_admin');
    }

    public function isBookstoreAdmin(): bool
    {
        return $this->hasRole('bookstore_admin');
    }

    public function isPublisherAdmin(): bool
    {
        return $this->hasRole('publisher_admin');
    }

    public function isResearcher(): bool
    {
        return $this->hasRole('researcher');
    }

    /**
     * Check if user has any institution admin role.
     */
    public function isAnyInstitutionAdmin(): bool
    {
        return $this->isInstitutionAdmin() || 
               $this->isSchoolAdmin() || 
               $this->isCollegeAdmin() || 
               $this->isUniversityAdmin() || 
               $this->isLibraryAdmin() || 
               $this->isBookstoreAdmin() || 
               $this->isPublisherAdmin() || 
               $this->isResearcher();
    }

    /**
     * Get the user's institution type based on their role.
     */
    public function getInstitutionTypeFromRole(): ?string
    {
        $role = $this->getRoleNames()->first();
        
        return match($role) {
            'school_admin' => 'school',
            'college_admin' => 'college',
            'university_admin' => 'university',
            'library_admin' => 'library',
            'bookstore_admin' => 'bookstore',
            'publisher_admin' => 'publisher',
            'researcher' => 'research_center',
            default => null,
        };
    }

    public function referrals()
{
    return $this->hasMany(Referral::class, 'referrer_id');
}

public function referrer()
{
    return $this->belongsTo(User::class, 'referred_by');
}

public function institution(): BelongsTo
{
    return $this->belongsTo(Institution::class);
}

// ========== MULTIPLE INSTITUTIONS (Many-to-Many) ==========

/**
 * Get all institutions the user belongs to.
 */
public function institutions()
{
    return $this->belongsToMany(Institution::class, 'institution_members')
                ->withPivot('role', 'status', 'joined_at')
                ->withTimestamps();
}

/**
 * Get the user's role in a specific institution.
 */
public function roleInInstitution($institutionId)
{
    $member = $this->institutions()->where('institution_id', $institutionId)->first();
    return $member ? $member->pivot->role : null;
}

/**
 * Check if user belongs to any institution.
 */
public function hasInstitutions(): bool
{
    return $this->institutions()->count() > 0;
}

/**
 * Check if user is an admin of any institution.
 */
public function isAdminOfAnyInstitution(): bool
{
    return $this->institutions()->wherePivot('role', 'admin')->exists();
}

/**
 * Check if user is a member of a specific institution.
 */
public function isMemberOf($institutionId): bool
{
    return $this->institutions()->where('institution_id', $institutionId)->exists();
}

/**
 * Get all institution IDs the user belongs to.
 */
public function getInstitutionIdsAttribute()
{
    return $this->institutions()->pluck('institution_id')->toArray();
}

/**
 * Get the primary/current institution (first one).
 */
public function currentInstitution()
{
    return $this->institutions()->first();
}

/**
 * Get the count of institutions the user belongs to.
 */
public function getInstitutionCountAttribute()
{
    return $this->institutions()->count();
}

public function hasInstitution(): bool
{
    return !is_null($this->institution_id);
}


    public function canManageInstitution($institution): bool
    {
        if ($this->isSuperAdmin()) return true;
        if ($this->isAdmin()) return true;
        if ($this->isInstitutionAdmin() && $this->institution_id === $institution->id) return true;
        return false;
    }

    /**
     * Check if current user can manage another user
     */
    public function canManageUser(User $user): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if ($this->isAdmin() && !$user->isSuperAdmin()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if current user can delete another user
     */
    public function canDeleteUser(User $user): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        if ($this->isAdmin() && $user->isUser()) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if current user can change role of another user
     */
    public function canChangeRole(User $user): bool
    {
        return $this->isSuperAdmin() && $this->id !== $user->id;
    }

    public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPasswordNotification($token));
}

    

    /**
     * Get the active subscription.
     */
    public function activeSubscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable')
            ->where('status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->latest();
    }

    /**
     * Get the latest subscription.
     */
    public function latestSubscription()
    {
        return $this->morphOne(Subscription::class, 'subscribable')->latest();
    }

    /**
     * Get subscription history.
     */
    public function subscriptionHistory()
    {
        return $this->morphMany(Subscription::class, 'subscribable')
            ->orderBy('created_at', 'desc');
    }

    // ==========================================
    // SUBSCRIPTION METHODS
    // ==========================================

 public function hasActiveSubscription(): bool
{
    // Check 1: User's own subscription_tier field in users table
    if ($this->subscription_tier && $this->subscription_expires_at) {
        return $this->subscription_expires_at > now();
    }
    
    // Check 2: User's own subscription in subscriptions table
    if ($this->subscriptions()
        ->where('status', 'active')
        ->where('ends_at', '>', now())
        ->exists()) {
        return true;
    }
    
    // Check 3: Institution subscription (if user belongs to an institution)
    if ($this->institution && $this->institution->hasActiveSubscription()) {
        return true;
    }
    
    return false;
}


public function getSubscriptionDaysLeft(): int
{
    // Check 1: User's own subscription_tier field
    if ($this->subscription_expires_at) {
        if ($this->subscription_expires_at->isPast()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->subscription_expires_at, false));
    }
    
    // Check 2: User's own subscription in subscriptions table
    $subscription = $this->subscriptions()
        ->where('status', 'active')
        ->where('ends_at', '>', now())
        ->latest()
        ->first();
    
    if ($subscription && $subscription->ends_at) {
        if ($subscription->ends_at->isPast()) {
            return 0;
        }
        return max(0, now()->diffInDays($subscription->ends_at, false));
    }
    
    // Check 3: Institution subscription
    if ($this->institution) {
        return $this->institution->getSubscriptionDaysLeft();
    }
    
    return 0;
}

/**
 * Get orders placed by this user
 */
public function orders()
{
    return $this->hasMany(Order::class);
}

/**
 * Get order items for books uploaded by this author
 */
public function authorOrderItems()
{
    return $this->hasManyThrough(
        OrderItem::class,
        Book::class,
        'uploaded_by', // Foreign key on books table
        'book_id', // Foreign key on order_items table
        'id', // Local key on users table
        'id' // Local key on books table
    );
}

/**
 * Get orders for books uploaded by this author
 */
public function authorOrders()
{
    $bookIds = $this->books()->pluck('id');
    return Order::whereHas('items', function($query) use ($bookIds) {
        $query->whereIn('book_id', $bookIds);
    });
}

// Add this relationship
public function royalties()
{
    return $this->hasMany(Royalty::class, 'author_id');
}

// Add this relationship for withdrawals
public function withdrawalRequests()
{
    return $this->hasMany(WithdrawalRequest::class, 'user_id');
}



public function subscriptions()
{
    return $this->hasMany(Subscription::class, 'subscribable_id')
        ->where('subscribable_type', User::class);
}

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

public function getSubscriptionStatusLabel(): string
{
    if (!$this->hasActiveSubscription()) {
        return ' No Active Subscription';
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
   public function getPlanLabel(): string
{
    // Check 1: User's own subscription_tier
    if ($this->subscription_tier) {
        return match($this->subscription_tier) {
            'basic' => '📘 Basic',
            'premium' => '📚 Premium',
            'enterprise' => '🏢 Enterprise',
            'free' => '🆓 Free',
            default => '📘 ' . ucfirst($this->subscription_tier),
        };
    }
    
    // Check 2: User's own subscription in subscriptions table
    $subscription = $this->subscriptions()
        ->where('status', 'active')
        ->where('ends_at', '>', now())
        ->latest()
        ->first();
    
    if ($subscription) {
        return $subscription->getPlanLabel();
    }
    
    // Check 3: Institution subscription
    if ($this->institution && $this->institution->hasActiveSubscription()) {
        return $this->institution->getPlanLabel();
    }
    
    return '🆓 Free';
}


    /**
     * Check if user can access premium feature.
     */
    public function canAccessPremium(): bool
    {
        return $this->hasActiveSubscription() && 
               in_array($this->activeSubscription?->plan, ['premium', 'pro']);
    }

    /**
     * Check if user can access pro feature.
     */
    public function canAccessPro(): bool
    {
        return $this->hasActiveSubscription() && 
               $this->activeSubscription?->plan === 'pro';
    }

    // ==========================================
    // BOOK RELATIONSHIPS
    // ==========================================
    
    public function books()
    {
        return $this->belongsToMany(Book::class, 'user_books')
                    ->withPivot('progress_percent', 'current_page', 'status')
                    ->withTimestamps();
    }
    
    public function purchasedBooks()
    {
        return $this->belongsToMany(Book::class, 'payments', 'user_id', 'payable_id')
                    ->where('payable_type', Book::class)
                    ->where('status', 'completed');
    }

   public function hasPurchasedBook($bookId, $payableType = null): bool
{
    $query = $this->payments()
        ->where('payable_id', $bookId)
        ->where('status', 'completed');

    if ($payableType) {
        $query->where('payable_type', $payableType);
    } else {
        $query->whereIn('payable_type', [Book::class, BookshopBook::class]);
    }

    return $query->exists();
}
    
    public function purchaseBookWithWallet($book, $paymentMethod = 'wallet')
    {
        if ($this->hasPurchasedBook($book->id)) {
            return ['success' => false, 'message' => 'You already own this book.'];
        }
        
        if (!$book->is_paid) {
            $this->books()->syncWithoutDetaching([$book->id => ['purchased_at' => now()]]);
            return ['success' => true, 'message' => 'Free book added to your library!'];
        }
        
        if ($this->wallet_balance < $book->price) {
            return [
                'success' => false, 
                'message' => 'Insufficient wallet balance',
                'shortfall' => $book->price - $this->wallet_balance
            ];
        }
        
        $oldBalance = $this->wallet_balance;
        $newBalance = $oldBalance - $book->price;
        $this->wallet_balance = $newBalance;
        $this->save();
        
        $payment = Payment::create([
            'user_id' => $this->id,
            'payable_type' => Book::class,
            'payable_id' => $book->id,
            'amount' => $book->price,
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'transaction_id' => 'PUR_' . time() . '_' . $this->id . '_' . $book->id,
        ]);
        
        $this->books()->syncWithoutDetaching([
            $book->id => [
                'purchased_at' => now(),
                'status' => 'want_to_read'
            ]
        ]);
        
        Transaction::create([
            'user_id' => $this->id,
            'type' => 'debit',
            'amount' => $book->price,
            'balance_after' => $newBalance,
            'description' => 'Purchase: ' . $book->title,
            'reference' => $payment->transaction_id,
            'status' => 'completed',
            'method' => $paymentMethod,
            'payable_type' => Book::class,
            'payable_id' => $book->id,
        ]);
        
        return [
            'success' => true, 
            'message' => 'Book purchased successfully!',
            'new_balance' => $newBalance
        ];
    }
    
    public function getPurchasedBooks()
    {
        return $this->belongsToMany(Book::class, 'user_books')
                    ->wherePivotNotNull('purchased_at')
                    ->withPivot('progress_percent', 'current_page', 'status', 'purchased_at');
    }

    // ==========================================
    // COMMUNITY RELATIONSHIPS
    // ==========================================
    
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }
    
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    // ==========================================
    // PAYMENT & CERTIFICATE RELATIONSHIPS
    // ==========================================

    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class);
    }

    public function deposits()
    {
        return $this->transactions()
            ->where('type', 'credit')
            ->where('status', 'completed');
    }

    public function purchases()
    {
        return $this->transactions()
            ->where('type', 'debit')
            ->where('status', 'completed');
    }

    public function pendingWithdrawals()
    {
        return $this->transactions()
            ->where('type', 'debit')
            ->where('method', 'withdrawal')
            ->where('status', 'pending');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

  // ==========================================
// MARKETPLACE RELATIONSHIPS
// ==========================================
    
public function marketplaceListings()
{
    return $this->hasMany(MarketplaceListing::class, 'seller_id');
}
// ==========================================
// MARKETPLACE SELLER METHODS
// ==========================================

/**
 * Check if user can sell on marketplace
 * User must have author OR bookseller role
 */
public function canSellOnMarketplace(): bool
{
    return $this->hasRole('author') || $this->hasRole('bookseller');
}

/**
 * Check if user is an approved author
 */
public function isApprovedAuthor(): bool
{
    return $this->hasRole('author') && $this->author_approved_at !== null;
}

/**
 * Check if user is an approved bookseller
 */
public function isApprovedBookseller(): bool
{
    return $this->hasRole('bookseller') && $this->bookseller_approved_at !== null;
}

/**
 * Get the user's seller type for display
 */
public function getSellerType(): ?string
{
    $isAuthor = $this->isApprovedAuthor();
    $isBookseller = $this->isApprovedBookseller();
    
    if ($isAuthor && $isBookseller) {
        return 'Author & Bookseller';
    }
    if ($isAuthor) {
        return 'Author';
    }
    if ($isBookseller) {
        return 'Bookseller';
    }
    return null;
}
/**
 * Check if user has pending application to become author/bookseller
 */
public function hasPendingApplication($type = null): bool
{
    $query = $this->applications()->where('status', 'pending');
    if ($type) {
        $query->where('type', $type);
    }
    return $query->exists();
}

/**
 * Get user's active seller role (for permissions)
 */
public function getSellerRoles(): array
{
    $roles = [];
    if ($this->isApprovedAuthor()) {
        $roles[] = 'author';
    }
    if ($this->isApprovedBookseller()) {
        $roles[] = 'bookseller';
    }
    return $roles;
}

/**
 * Get seller dashboard stats
 */
public function getSellerStats(): array
{
    $listings = $this->marketplaceListings();
    
    return [
        'total_listings' => $listings->count(),
        'pending_listings' => $listings->where('status', 'pending')->count(),
        'approved_listings' => $listings->where('status', 'approved')->count(),
        'total_sales' => $this->marketplaceOrders()->count(),
        'total_earnings' => $this->marketplaceOrders()->sum('seller_earnings'),
        'pending_orders' => $this->marketplaceOrders()->where('status', 'pending')->count(),
    ];
}

// ==========================================
// MARKETPLACE ORDER RELATIONSHIPS - ADD THIS
// ==========================================

/**
 * Orders where user is the seller
 */
public function marketplaceOrders()
{
    return $this->hasMany(MarketplaceOrder::class, 'seller_id');
}

/**
 * Orders where user is the buyer
 */
public function marketplacePurchases()
{
    return $this->hasMany(MarketplaceOrder::class, 'buyer_id');
}

/**
 * Get seller earnings total
 */
public function getTotalSellerEarningsAttribute(): float
{
    return $this->marketplaceOrders()
        ->where('status', 'completed')
        ->sum('seller_earnings');
}

/**
 * Get pending seller orders count
 */
public function getPendingSellerOrdersAttribute(): int
{
    return $this->marketplaceOrders()
        ->where('status', 'pending')
        ->count();
}
    // ==========================================
    // AI CHAT RELATIONSHIPS
    // ==========================================
    
    public function aiChats()
    {
        return $this->hasMany(AiChat::class);
    }

    // ==========================================
    // QUIZ RELATIONSHIPS
    // ==========================================
    
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ==========================================
    // BOOKMARK RELATIONSHIPS
    // ==========================================
    
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    // ==========================================
    // XP, LEVEL & STREAK METHODS
    // ==========================================

    public function getCombinedScoreAttribute()
    {
        $certificateScore = $this->certificates()->count() * 10;
        $quizAvg = $this->quizAttempts()->avg('percentage') ?? 0;
        $streakScore = $this->streak_days;
        $booksRead = $this->books()->wherePivot('status', 'completed')->count() * 5;
        
        return round($certificateScore + $quizAvg + $streakScore + $booksRead);
    }

    public function updateStreak()
    {
        $lastActive = $this->last_active_at;
        
        if (!$lastActive) {
            $this->streak_days = 1;
        } else {
            $daysDiff = now()->diffInDays($lastActive);
            
            if ($daysDiff == 1) {
                $this->streak_days++;
            } elseif ($daysDiff > 1) {
                $this->streak_days = 1;
            }
        }
        
        $this->last_active_at = now();
        $this->save();
        $this->updateLevel();
    }

    public function updateLevel()
    {
        $newLevel = 1;
        
        if ($this->xp_points >= 1000) {
            $newLevel = 5;
        } elseif ($this->xp_points >= 600) {
            $newLevel = 4;
        } elseif ($this->xp_points >= 300) {
            $newLevel = 3;
        } elseif ($this->xp_points >= 100) {
            $newLevel = 2;
        }
        
        if ($this->level != $newLevel) {
            $this->level = $newLevel;
            $this->save();
        }
    }

    public function addXp($points)
    {
        $this->xp_points += $points;
        $this->save();
        $this->updateLevel();
        
        return $this->xp_points;
    }

    public function getLevelProgressAttribute()
    {
        $levelThresholds = [0, 100, 300, 600, 1000];
        $currentLevel = $this->level;
        
        $currentMin = $levelThresholds[$currentLevel - 1] ?? 0;
        $currentMax = $levelThresholds[$currentLevel] ?? 1000;
        
        $xpInLevel = $this->xp_points - $currentMin;
        $xpNeeded = $currentMax - $currentMin;
        
        if ($xpNeeded <= 0) return 100;
        
        return min(100, round(($xpInLevel / $xpNeeded) * 100));
    }

    public function getNextLevelXpNeededAttribute()
    {
        $levelThresholds = [0, 100, 300, 600, 1000];
        
        if ($this->level >= 5) {
            $nextLevelXp = 1000 + (($this->level - 4) * 500);
            return $nextLevelXp - $this->xp_points;
        }
        
        return $levelThresholds[$this->level] - $this->xp_points;
    }

    // ==========================================
    // RATINGS & REVIEWS RELATIONSHIPS
    // ==========================================
    
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // ==========================================
    // RATINGS & REVIEWS METHODS
    // ==========================================
    
    public function ratingForBook($bookId)
    {
        return $this->ratings()->where('book_id', $bookId)->value('rating');
    }
    
    public function hasRatedBook($bookId)
    {
        return $this->ratings()->where('book_id', $bookId)->exists();
    }
    
    public function hasReviewedBook($bookId)
    {
        return $this->reviews()->where('book_id', $bookId)->exists();
    }
    
    public function getReviewForBook($bookId)
    {
        return $this->reviews()->where('book_id', $bookId)->first();
    }

    // ==========================================
    // WALLET METHODS
    // ==========================================
    
    public function getWalletBalanceAttribute($value)
    {
        return floatval($value ?? 0);
    }
    
    public function incrementWallet($amount)
    {
        $current = floatval($this->getAttribute('wallet_balance') ?? 0);
        $newBalance = $current + floatval($amount);
        
        $this->update(['wallet_balance' => $newBalance]);
        
        return $this->fresh()->wallet_balance;
    }
    
    public function decrementWallet($amount)
    {
        $current = floatval($this->getAttribute('wallet_balance') ?? 0);
        
        if ($current >= floatval($amount)) {
            $newBalance = $current - floatval($amount);
            $this->update(['wallet_balance' => $newBalance]);
            return true;
        }
        
        return false;
    }

    // ==========================================
    // PROFILE HELPER METHODS
    // ==========================================
    
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }
        
        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?background=6366f1&color=fff&name={$name}";
    }
    
    public function getCoverPhotoUrlAttribute()
    {
        if ($this->cover_photo) {
            return Storage::url($this->cover_photo);
        }
        return null;
    }
    
    public function getSocialLinksAttribute(): array
    {
        return [
            'facebook' => $this->facebook_url,
            'twitter' => $this->twitter_url,
            'linkedin' => $this->linkedin_url,
            'github' => $this->github_url,
            'instagram' => $this->instagram_url,
            'website' => $this->website_url,
        ];
    }
    
    public function getJoinedDateAttribute()
    {
        return $this->created_at->format('F Y');
    }
    
    public function getTotalBooksReadAttribute()
    {
        return $this->books()->wherePivot('status', 'completed')->count();
    }

    // ==========================================
    // STATISTICS METHODS
    // ==========================================
    
    public function getTotalQuizzesTakenAttribute()
    {
        return $this->quizAttempts()->count();
    }
    
    public function getTotalQuizzesPassedAttribute()
    {
        return $this->quizAttempts()->where('passed', true)->count();
    }
    
    public function getAverageQuizScoreAttribute()
    {
        return round($this->quizAttempts()->avg('percentage') ?? 0, 1);
    }
    
    public function getTotalCertificatesAttribute()
    {
        return $this->certificates()->count();
    }
    
    public function getFullNameAttribute($value)
    {
        return ucwords($value);
    }

    // ==========================================
    // REFERRAL METHODS
    // ==========================================
    
    
    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }
    
    public static function generateReferralCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (User::where('referral_code', $code)->exists());
        
        return $code;
    }
    
    public function getReferralLinkAttribute()
    {
        return url('/register?ref=' . $this->referral_code);
    }
    
    public function getTotalReferralsAttribute()
    {
        return $this->referrals()->count();
    }
    
    public function getCompletedReferralsAttribute()
    {
        return $this->referrals()->where('status', 'completed')->count();
    }
    
    public function getPendingReferralsAttribute()
    {
        return $this->referrals()->where('status', 'pending')->count();
    }
    
    public function getReferralEarningsAttribute($value)
    {
        return floatval($value ?? 0);
    }
}