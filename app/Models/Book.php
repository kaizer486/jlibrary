<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $table = 'books';

    // ==========================================
    // FILLABLE
    // ==========================================
    protected $fillable = [
        // Basic Info
        'title',
        'author',
        'category',
        'sub_category',
        'description',
        'isbn',
        'publication_year',
        'publisher',
        'language',
        'total_pages',
        'published_date',

        // Media
        'cover_image',
        'file_path',

        // Pricing
        'is_paid',
        'price',

        // Institution
        'institution_id',
        'uploaded_by',

        // Status & Flags
        'status',
        'availability',
        'is_featured',
        'is_trending',

        // Shelf Location
        'shelf_number',
        'shelf_name',
        'column_location',
        'position',
        'floor',
        'section',

        // Statistics
        'views_count',
        'downloads',
        'copies_available',
        'total_copies',

        // Bookstore fields
        'is_bookstore_item',
        'book_type',
        'softcopy_price',
        'hardcopy_price',
        'stock_quantity',
        'hardcopy_available',
    ];

    // ==========================================
    // CASTS
    // ==========================================
    protected $casts = [
        'is_paid' => 'boolean',
        'price' => 'decimal:2',
        'total_pages' => 'integer',
        'views_count' => 'integer',
        'downloads' => 'integer',
        'copies_available' => 'integer',
        'total_copies' => 'integer',
        'publication_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_bookstore_item' => 'boolean',
        'softcopy_price' => 'decimal:2',
        'hardcopy_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'hardcopy_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'published_date' => 'date',
    ];

    // ==========================================
    // CATEGORY ICONS
    // ==========================================
    protected $categoryIcons = [
        'Computer Science & Information Technology' => '💻',
        'Artificial Intelligence & Data Science' => '🤖',
        'Engineering & Technology' => '⚙️',
        'Mathematics & Statistics' => '📐',
        'Physical Sciences' => '🔬',
        'Biological Sciences' => '🧬',
        'Health & Medical Sciences' => '🏥',
        'Public Health' => '🌍',
        'Agriculture & Veterinary Sciences' => '🌾',
        'Environmental & Earth Sciences' => '🌿',
        'Business & Management' => '💼',
        'Economics & Finance' => '💰',
        'Accounting' => '📊',
        'Marketing' => '📈',
        'Entrepreneurship' => '🚀',
        'Law' => '⚖️',
        'Education' => '📚',
        'Social Sciences' => '👥',
        'Psychology' => '🧠',
        'Political Science & Public Administration' => '🏛️',
        'Humanities' => '📖',
        'Philosophy' => '💭',
        'Languages & Linguistics' => '🗣️',
        'Literature' => '✍️',
        'History & Archaeology' => '🏺',
        'Geography & Tourism' => '🗺️',
        'Religion & Theology' => '🕊️',
        'Arts, Design & Music' => '🎨',
        'Architecture & Urban Planning' => '🏗️',
        'Children\'s Books' => '🧒',
        'Fiction' => '📕',
        'Non-Fiction' => '📗',
        'Biographies & Memoirs' => '👤',
        'Self-Help & Personal Development' => '🌱',
        'Leadership' => '👔',
        'Research & Academic Publications' => '🔍',
        'Journals & Conference Proceedings' => '📑',
        'Theses & Dissertations' => '🎓',
        'Government Publications' => '🏛️',
        'Policies, Acts & Regulations' => '📜',
        'Reports & White Papers' => '📄',
        'Reference Books' => '📚',
        'Open Educational Resources (OER)' => '🌐',
        'Newspapers & Magazines' => '📰',
        'Encyclopedias & Dictionaries' => '📖',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class, 'shelf_number', 'code');
    }

    public function payments()
    {
        return $this->hasMany(LibraryPayment::class);
    }

    public function purchasers()
    {
        return $this->belongsToMany(User::class, 'library_payments')
                    ->wherePivot('status', 'completed');
    }

    // ==========================================
    // BORROWING RELATIONSHIPS
    // ==========================================

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function activeBorrowings()
    {
        return $this->hasMany(Borrowing::class)->where('status', 'borrowed');
    }

    public function isBorrowed(): bool
    {
        return $this->activeBorrowings()->count() > 0;
    }

    public function currentBorrower()
    {
        $borrowing = $this->activeBorrowings()->with('user')->first();
        return $borrowing ? $borrowing->user : null;
    }

    // ==========================================
    // RATINGS & REVIEWS RELATIONSHIPS
    // ==========================================

    /**
     * Get all ratings for this book
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get all reviews for this book
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    /**
     * Get total ratings count
     */
    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    // ==========================================
    // BOOKMARKS
    // ==========================================

    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    // ==========================================
    // QUIZZES & CERTIFICATES
    // ==========================================

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // ==========================================
    // USER BOOK PROGRESS
    // ==========================================

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books')
                    ->withPivot('progress', 'status', 'completed_at')
                    ->withTimestamps();
    }

    // ==========================================
    // AVAILABILITY HELPERS
    // ==========================================

    /**
     * Update book availability based on status and copies.
     */
    public function updateAvailability(): void
    {
        if ($this->isBorrowed()) {
            $this->availability = 'borrowed';
        } elseif ($this->copies_available > 0) {
            $this->availability = 'available';
        } else {
            $this->availability = 'under_maintenance';
        }
        $this->save();
    }

    /**
     * Check if book is available to borrow.
     */
    public function isAvailableToBorrow(): bool
    {
        return $this->status === 'approved' 
            && $this->availability === 'available' 
            && !$this->isBorrowed()
            && $this->copies_available > 0;
    }

    /**
     * Check if book is available for purchase.
     */
    public function isAvailableForPurchase(): bool
    {
        return $this->status === 'approved' 
            && ($this->availability === 'available' || $this->availability === 'borrowed');
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

    public function scopeFree($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeInInstitution($query, $institutionId)
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available')
            ->where('status', 'approved');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('availability', 'borrowed');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('author', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%")
              ->orWhere('isbn', 'LIKE', "%{$search}%");
        });
    }

    // ==========================================
    // CATEGORY HELPERS
    // ==========================================

    /**
     * Get category icon
     */
    public function getCategoryIconAttribute()
    {
        return $this->categoryIcons[$this->category] ?? '📘';
    }

    /**
     * Get formatted category with icon
     */
    public function getCategoryLabelAttribute()
    {
        return $this->category ? $this->category_icon . ' ' . $this->category : '📘 Uncategorized';
    }

    /**
     * Get category icon by category name (static method)
     */
    public static function getCategoryIcon($category)
    {
        $icons = [
            'Computer Science & Information Technology' => '💻',
            'Artificial Intelligence & Data Science' => '🤖',
            'Engineering & Technology' => '⚙️',
            'Mathematics & Statistics' => '📐',
            'Physical Sciences' => '🔬',
            'Biological Sciences' => '🧬',
            'Health & Medical Sciences' => '🏥',
            'Public Health' => '🌍',
            'Agriculture & Veterinary Sciences' => '🌾',
            'Environmental & Earth Sciences' => '🌿',
            'Business & Management' => '💼',
            'Economics & Finance' => '💰',
            'Accounting' => '📊',
            'Marketing' => '📈',
            'Entrepreneurship' => '🚀',
            'Law' => '⚖️',
            'Education' => '📚',
            'Social Sciences' => '👥',
            'Psychology' => '🧠',
            'Political Science & Public Administration' => '🏛️',
            'Humanities' => '📖',
            'Philosophy' => '💭',
            'Languages & Linguistics' => '🗣️',
            'Literature' => '✍️',
            'History & Archaeology' => '🏺',
            'Geography & Tourism' => '🗺️',
            'Religion & Theology' => '🕊️',
            'Arts, Design & Music' => '🎨',
            'Architecture & Urban Planning' => '🏗️',
            'Children\'s Books' => '🧒',
            'Fiction' => '📕',
            'Non-Fiction' => '📗',
            'Biographies & Memoirs' => '👤',
            'Self-Help & Personal Development' => '🌱',
            'Leadership' => '👔',
            'Research & Academic Publications' => '🔍',
            'Journals & Conference Proceedings' => '📑',
            'Theses & Dissertations' => '🎓',
            'Government Publications' => '🏛️',
            'Policies, Acts & Regulations' => '📜',
            'Reports & White Papers' => '📄',
            'Reference Books' => '📚',
            'Open Educational Resources (OER)' => '🌐',
            'Newspapers & Magazines' => '📰',
            'Encyclopedias & Dictionaries' => '📖',
        ];

        return $icons[$category] ?? '📘';
    }

    // ==========================================
    // BADGE HELPERS
    // ==========================================

    /**
     * Get featured badge HTML
     */
    public function getFeaturedBadgeAttribute()
    {
        if ($this->is_featured) {
            return '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⭐ Featured</span>';
        }
        return '';
    }

    /**
     * Get trending badge HTML
     */
    public function getTrendingBadgeAttribute()
    {
        if ($this->is_trending) {
            return '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">🔥 Trending</span>';
        }
        return '';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'approved' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            'pending' => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/20',
            'rejected' => 'bg-red-500/20 text-red-400 border border-red-500/20',
            'available' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            'borrowed' => 'bg-blue-500/20 text-blue-400 border border-blue-500/20',
            'reserved' => 'bg-amber-500/20 text-amber-400 border border-amber-500/20',
            'under_maintenance' => 'bg-gray-500/20 text-gray-400 border border-gray-500/20',
        ];

        $labels = [
            'approved' => '✅ Approved',
            'pending' => '⏳ Pending',
            'rejected' => '❌ Rejected',
            'available' => '✅ Available',
            'borrowed' => '📖 Borrowed',
            'reserved' => '🔖 Reserved',
            'under_maintenance' => '🔧 Maintenance',
        ];

        $class = $badges[$this->status] ?? 'bg-gray-500/20 text-gray-400 border border-gray-500/20';
        $label = $labels[$this->status] ?? ucfirst($this->status);

        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $class . '">' . $label . '</span>';
    }

    // ==========================================
    // FILE HELPERS
    // ==========================================

    public function hasCover(): bool
    {
        return !empty($this->cover_image);
    }

    public function hasPdf(): bool
    {
        return !empty($this->file_path);
    }

    public function getCoverUrl(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return null;
    }

    public function getPdfUrl(): ?string
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // ==========================================
    // SHELF LOCATION HELPERS
    // ==========================================

    public function getFullShelfLocation(): string
    {
        $parts = [];

        if ($this->shelf_number) {
            $parts[] = "Shelf: {$this->shelf_number}";
        }
        if ($this->shelf_name) {
            $parts[] = $this->shelf_name;
        }
        if ($this->floor) {
            $parts[] = "Floor: {$this->floor}";
        }
        if ($this->section) {
            $parts[] = "Section: {$this->section}";
        }
        if ($this->column_location) {
            $parts[] = "Column: {$this->column_location}";
        }
        if ($this->position) {
            $parts[] = "Position: {$this->position}";
        }

        return implode(' | ', $parts) ?: 'Location not specified';
    }

    // ==========================================
    // STATUS HELPERS
    // ==========================================

    public function isFree(): bool
    {
        return !$this->is_paid;
    }

    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isTrending(): bool
    {
        return $this->is_trending;
    }

    // ==========================================
    // INCREMENT HELPERS
    // ==========================================

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads');
    }

    // ==========================================
    // COPY HELPERS
    // ==========================================

    public function hasAvailableCopies(): bool
    {
        return $this->copies_available > 0;
    }

    public function getAvailableCopies(): int
    {
        return $this->copies_available ?? 0;
    }


    /**
 * Get institution name or 'Global' if no institution
 */
public function getInstitutionNameAttribute()
{
    if ($this->institution_id && $this->institution) {
        return $this->institution->name;
    }
    return 'Global Library';
}

/**
 * Check if book belongs to an institution
 */
public function hasInstitution(): bool
{
    return !empty($this->institution_id) && $this->institution !== null;
}

    /**
     * Get the average rating for this book
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get the average rating as a float (alias for averageRating)
     */
    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    /**
     * Get the total number of ratings
     */
    public function ratingCount()
    {
        return $this->ratings()->count();
    }

    /**
     * Get the total number of ratings (alias)
     */
    public function getRatingCountAttribute()
    {
        return $this->ratingCount();
    }

    /**
     * Check if a user has rated this book
     */
    public function hasUserRated($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    /**
     * Get a user's rating for this book
     */
    public function userRating($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return null;
        }
        $rating = $this->ratings()->where('user_id', $userId)->first();
        return $rating ? $rating->rating : null;
    }

    /**
     * Check if a user has reviewed this book
     */
    public function hasUserReviewed($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return false;
        }
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Get a user's review for this book
     */
    public function userReview($userId = null)
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) {
            return null;
        }
        return $this->reviews()->where('user_id', $userId)->first();
    }

    /**
     * Check if user has access to this book (for paid books)
     */
    public function userHasAccess($userId)
    {
        // If book is free, everyone has access
        if (!$this->is_paid) {
            return true;
        }

        // Check if user has purchased this book
        return $this->payments()
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        if ($this->is_paid) {
            return '$' . number_format($this->price, 2);
        }
        return 'FREE';
    }

    public function getPriceInTshAttribute()
    {
        if ($this->is_paid) {
            return 'TSh ' . number_format($this->price * 2500, 0);
        }
        return 'FREE';
    }
}