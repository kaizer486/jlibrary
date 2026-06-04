<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Payment; 

/**
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string|null $description
 * @property string|null $cover_image
 * @property string $file_path
 * @property bool $is_paid
 * @property numeric $price
 * @property int $total_pages
 * @property int $downloads
 * @property int $uploaded_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $embedding
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $allReviews
 * @property-read int|null $all_reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificate> $certificates
 * @property-read int|null $certificates_count
 * @property-read mixed $bookmark_count
 * @property-read array $rating_distribution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Quiz> $quizzes
 * @property-read int|null $quizzes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User|null $uploader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCoverImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDownloads($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereEmbedding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereIsPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTotalPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUploadedBy($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
    protected $fillable = [
        'title', 'author', 'description', 'cover_image',
        'file_path', 'is_paid', 'price', 'total_pages',
        'downloads', 'uploaded_by', 'status'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'decimal:2'
    ];

    // ========== EXISTING RELATIONSHIPS ==========
    
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
                    ->withPivot('progress_percent', 'current_page', 'status')
                    ->withTimestamps();
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    // Add this with your other relationships
public function institution()
{
    return $this->belongsTo(Institution::class);
}
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
    
    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    // ========== BOOKMARK RELATIONSHIPS ==========
    
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    public function isBookmarkedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function getBookmarkCountAttribute()
    {
        return $this->bookmarks()->count();
    }

    // ========== RATINGS & REVIEWS RELATIONSHIPS ==========
    
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function allReviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ========== RATINGS METHODS ==========
    
    public function averageRating(): float
    {
        return round($this->ratings()->avg('rating') ?? 0, 1);
    }

    public function ratingCount(): int
    {
        return $this->ratings()->count();
    }
 public function getCommissionBreakdown($price)
{
    $institutionCommission = CommissionSetting::getInstitutionCommission();
    $platformCommission = CommissionSetting::getPlatformCommission();
    $authorCommission = CommissionSetting::getAuthorCommission();
    
    return [
        'price' => $price,
        'institution_amount' => ($price * $institutionCommission) / 100,
        'platform_amount' => ($price * $platformCommission) / 100,
        'author_amount' => ($price * $authorCommission) / 100,
    ];
}
    public function userRating($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->ratings()->where('user_id', $userId)->value('rating');
    }

    public function hasUserRated($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    public function getRatingDistributionAttribute(): array
    {
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        
        $ratings = $this->ratings()
            ->select('rating', \DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get();
        
        foreach ($ratings as $rating) {
            $distribution[$rating->rating] = $rating->count;
        }
        
        return $distribution;
    }

    // ========== REVIEWS METHODS ==========
    
    public function userReview($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->allReviews()->where('user_id', $userId)->first();
    }

    public function hasUserReviewed($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->allReviews()->where('user_id', $userId)->exists();
    }

    // ========== ACCESS METHODS ==========
    
    public function userHasAccess($userId): bool
    {
        if (!$this->is_paid) {
            return true;
        }
        
        return Payment::where('user_id', $userId)
            ->where('payable_type', Book::class)
            ->where('payable_id', $this->id)
            ->where('status', 'completed')
            ->exists();
    }
    
    /**
     * Check if user has purchased this book 
     */
    public function isPurchasedByUser($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) return false;
        
        // For free books, always return true for access check
        if (!$this->is_paid) {
            return true;
        }
        
        return $this->userHasAccess($userId);
    }
    
    /**
     * Check if user can access this book
     */
    public function canUserAccess($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) return false;
        
        return $this->userHasAccess($userId);
    }
    
    /**
     * Get user's progress for this book
     */
    public function getUserProgress($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$userId) return null;
        
        return $this->users()->where('user_id', $userId)->first();
    }
    
    /**
     * Purchase this book for a user
     */
    public function purchaseForUser($userId, $paymentMethod = 'wallet')
    {
        $user = User::find($userId);
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Check if already purchased
        if ($this->isPurchasedByUser($userId)) {
            return ['success' => false, 'message' => 'Book already purchased'];
        }
        
        // Use the user's purchase method
        return $user->purchaseBookWithWallet($this, $paymentMethod);
    }
    
}