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
        'description',
        'isbn',
        'publication_year',
        'publisher',
        'language',
        'total_pages',

        // Media
        'cover_image',
        'file_path',

        // Pricing
        'is_paid',
        'price',

        // Institution
        'institution_id',
        'uploaded_by',

        // Status
        'status',
        'availability',

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
 * Get average rating
 */
public function getAverageRatingAttribute()
{
    return $this->ratings()->avg('rating') ?? 0;
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
    // HELPERS
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

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads');
    }

    public function hasAvailableCopies(): bool
    {
        return $this->copies_available > 0;
    }

    public function getAvailableCopies(): int
    {
        return $this->copies_available ?? 0;
    }

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
}