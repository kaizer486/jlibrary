<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\Payment;
use App\Models\Certificate;  
use App\Models\Transaction;
use App\Traits\HasXpRewards;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles; 

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
    use HasFactory, Notifiable, HasRoles, HasXpRewards;

    protected $table = 'users';

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'google_id',
        'role',
        'wallet_balance',
        'avatar',
        // Profile Fields
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
        // Referral Fields
        'referral_code',
        'referred_by',
        'referral_earnings',
        'institution_id',
        'position',
        'is_institution_admin',
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
    ];

    // ========== APPLICATION RELATIONSHIPS ==========
    
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function hasPendingApplication($type = null)
    {
        $query = $this->applications()->where('status', 'pending');
        if ($type) {
            $query->where('type', $type);
        }
        return $query->exists();
    }

    public function isApprovedAuthor()
    {
        return $this->hasRole('author') || 
               $this->applications()->where('type', 'author')->where('status', 'approved')->exists();
    }

    public function isApprovedBookseller()
    {
        return $this->hasRole('bookseller') || 
               $this->applications()->where('type', 'bookseller')->where('status', 'approved')->exists();
    }

    // ========== ROLE METHODS (Using Spatie) ==========
    
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }
    
    public function isInstitutionAdmin(): bool
    {
        return $this->hasRole('institution_admin') || $this->is_institution_admin === true;
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

    public function isResearcher(): bool
    {
        return $this->hasRole('researcher');
    }

    public function isPublisher(): bool
    {
        return $this->hasRole('publisher');
    }

    // ========== INSTITUTION RELATIONSHIPS ==========

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
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
    
    // ========== BOOK RELATIONSHIPS ==========
    
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

    public function hasPurchasedBook($bookId)
    {
        return $this->payments()
            ->where('payable_type', Book::class)
            ->where('payable_id', $bookId)
            ->where('status', 'completed')
            ->exists();
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
    
    // ========== COMMUNITY RELATIONSHIPS ==========
    
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
    
    // ========== PAYMENT & CERTIFICATE RELATIONSHIPS ==========
    
    public function payments()
    {
        return $this->hasMany(\App\Models\Payment::class);
    }
    
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    
    // ========== MARKETPLACE RELATIONSHIPS ==========
    
    public function marketplaceListings()
    {
        return $this->hasMany(MarketplaceListing::class, 'seller_id');
    }
    
    // ========== AI CHAT RELATIONSHIPS ==========
    
    public function aiChats()
    {
        return $this->hasMany(AiChat::class);
    }
    
    // ========== QUIZ RELATIONSHIPS ==========
    
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    // ========== BOOKMARK RELATIONSHIPS ==========
    
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
    
    // ========== XP, LEVEL & STREAK METHODS ==========

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

    // ========== RATINGS & REVIEWS RELATIONSHIPS ==========
    
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    // ========== RATINGS & REVIEWS METHODS ==========
    
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
    
    // ========== WALLET METHODS ==========
    
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
    
    // ========== PROFILE HELPER METHODS ==========
    
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
    
    // ========== STATISTICS METHODS ==========
    
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
    
    // ========== REFERRAL METHODS ==========
    
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }
    
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