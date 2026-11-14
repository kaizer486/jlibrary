<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
        'slug',

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
public function isPaidItem(): bool
{
    return (bool) $this->is_paid && $this->price > 0;
}

public function completedPayments()
{
    return $this->morphMany(\App\Models\Payment::class, 'payable');
}

public function userHasAccess($userId)
{
    if (!$this->isPaidItem()) {
        return true;
    }

    return $this->completedPayments()
        ->where('user_id', $userId)
        ->where('status', 'completed')
        ->exists();
}

protected static function boot()
{
    parent::boot();

    static::creating(function ($book) {
        if (empty($book->slug)) {
            $book->slug = static::generateUniqueSlug($book->title);
        }
    });
}

public static function generateUniqueSlug($title)
{
    $slug = Str::slug($title);
    $original = $slug;
    $count = 1;

    while (static::where('slug', $slug)->exists()) {
        $slug = $original . '-' . $count;
        $count++;
    }

    return $slug;
}

public function getRouteKeyName(): string
{
    return 'id';
}

public function getFormattedPriceAttribute()
{
    if ($this->isPaidItem()) {
        return 'TSh ' . number_format($this->price, 0);
    }
    return 'FREE';
}

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


/**
 * Get order items for this book
 */
public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

/**
 * Get orders for this book
 */
public function orders()
{
    return $this->hasManyThrough(
        Order::class,
        OrderItem::class,
        'book_id', // Foreign key on order_items
        'id', // Local key on orders
        'id', // Local key on books
        'order_id' // Foreign key on order_items
    );
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

    /* ── Unified Rating / Review helpers ── */

    /**
     * Average rating from the reviews table (single source of truth).
     */
    public function averageRating(): float
    {
        return round((float) $this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->averageRating();
    }

    /**
     * How many reviews have a non-null rating.
     */
    public function ratingCount(): int
    {
        return $this->reviews()->whereNotNull('rating')->count();
    }

    public function getRatingCountAttribute(): int
    {
        return $this->ratingCount();
    }

    /**
     * TRUE only if the user has left a review WITH a rating number.
     * If they only have text (or nothing), the form will still show.
     */
    public function hasUserReviewed(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return false;

        return $this->reviews()
            ->where('user_id', $userId)
            ->whereNotNull('rating')
            ->exists();
    }

 public function userRating(?int $userId = null): ?int
{
    $userId = $userId ?? auth()->id();
    if (!$userId) return null;
    
    $review = $this->reviews()
        ->where('user_id', $userId)
        ->whereNotNull('rating')
        ->first();
        
    return $review ? (int) $review->rating : null;
}

    /**
     * The user's review text (if any).
     */
    public function userReview(?int $userId = null): ?Review
    {
        $userId = $userId ?? auth()->id();
        if (!$userId) return null;

        return $this->reviews()->where('user_id', $userId)->first();
    }

        // ==========================================
    // BOOK FORMAT HELPERS (Softcopy / Hardcopy)
    // ==========================================

    /**
     * Returns the effective format for this book.
     *
     * Prefers the explicit `book_type` column if it is one of the known
     * values. Otherwise, infers from legacy fields (hardcopy_available,
     * price) so old books uploaded before the format fields existed
     * still behave sensibly.
     *
     * @return string  'softcopy' | 'hardcopy' | 'both'
     */
        public function effectiveBookType(): string
    {
        // What the format prices actually support. A PDF file existing
        // does NOT mean the softcopy is on sale — the author may have
        // uploaded it as the print source for a hardcopy-only book.
        $hasSoft = (float) ($this->softcopy_price ?? 0) > 0;
        $hasHard = (bool) $this->hardcopy_available
            && (float) ($this->hardcopy_price ?? 0) > 0;

        // Trust the author's declared book_type FIRST when it exists.
        // This preserves "hardcopy only" even if a PDF was uploaded.
        if (in_array($this->book_type, ['softcopy', 'hardcopy', 'both'], true)) {
            if ($this->book_type === 'hardcopy') {
                return $hasHard ? 'hardcopy' : 'softcopy';
            }
            if ($this->book_type === 'softcopy') {
                return 'softcopy';
            }
            // Declared "both" — verify both prices are actually set.
            if ($hasSoft && $hasHard) {
                return 'both';
            }
            if ($hasHard) {
                return 'hardcopy';
            }
            return 'softcopy';
        }

        // No book_type declared (legacy book) — infer from prices.
        if ($hasSoft && $hasHard) {
            return 'both';
        }
        if ($hasHard) {
            return 'hardcopy';
        }
        return 'softcopy';
    }
      public function offersSoftcopy(): bool
    {
        $type = $this->effectiveBookType();

        // Must be explicitly softcopy or both — a PDF file existing is
        // NOT enough. If the author chose "hardcopy only", the file still
        // exists but the digital version is NOT for sale.
        if (!in_array($type, ['softcopy', 'both'], true)) {
            return false;
        }

        // Free digital download (has file, price 0) is fine.
        // Paid digital download requires a softcopy_price > 0.
        return $this->hasPdf() && (
            $this->effectiveSoftcopyPrice() > 0
            || !$this->isPaid
        );
    }
    /**
     * Does this book offer a hardcopy (physical) version?
     */
    public function offersHardcopy(): bool
    {
        $type = $this->effectiveBookType();

        return in_array($type, ['hardcopy', 'both'], true)
            && (bool) $this->hardcopy_available
            && $this->effectiveHardcopyPrice() > 0;
    }

    /**
     * Softcopy price (falls back to legacy `price`).
     */
    public function effectiveSoftcopyPrice(): float
    {
        $soft = $this->softcopy_price;

        if ($soft !== null && (float) $soft > 0) {
            return (float) $soft;
        }

        // Legacy paid books only stored `price` — treat that as softcopy.
        if ($this->is_paid && (float) $this->price > 0) {
            return (float) $this->price;
        }

        return 0.0;
    }

    /**
     * Hardcopy price.
     */
    public function effectiveHardcopyPrice(): float
    {
        return (float) ($this->hardcopy_price ?? 0);
    }

    /**
     * Whether the book has enough physical stock for the requested quantity.
     */
    public function hasStockFor(int $quantity): bool
    {
        return $this->offersHardcopy()
            && (int) ($this->stock_quantity ?? 0) >= $quantity;
    }

    /**
     * Returns a human label for the effective format.
     */
    public function getFormatLabelAttribute(): string
    {
        return match ($this->effectiveBookType()) {
            'softcopy' => '📱 Softcopy',
            'hardcopy' => '📦 Hardcopy',
            'both'     => '📱📦 Softcopy & Hardcopy',
            default    => 'Unknown',
        };
    }

    // ==========================================
    // ORDER OWNER HELPERS
    // ==========================================

    /**
     * Who owns this book — used to route incoming orders to the correct
     * dashboard (institution admin OR author).
     *
     * Returns an array like:
     *   ['type' => 'institution', 'id' => 5, 'column' => 'institution_id']
     *   ['type' => 'author',      'id' => 7, 'column' => 'author_id']
     * or null if the book has no identifiable owner.
     *
     * @return array{type:string,id:int,column:string}|null
     */
    public function orderOwner(): ?array
    {
        if (!empty($this->institution_id)) {
            return [
                'type'   => 'institution',
                'id'     => (int) $this->institution_id,
                'column' => 'institution_id',
            ];
        }

        if (!empty($this->uploaded_by)) {
            return [
                'type'   => 'author',
                'id'     => (int) $this->uploaded_by,
                'column' => 'author_id',
            ];
        }

        return null;
    }

    /**
     * Convenience: is this book owned by an author (uploader)?
     */
    public function isAuthorOwned(): bool
    {
        return empty($this->institution_id) && !empty($this->uploaded_by);
    }

    /**
     * Convenience: is this book owned by an institution?
     */
    public function isInstitutionOwned(): bool
    {
        return !empty($this->institution_id);
    }

}