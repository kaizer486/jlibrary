<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shelf extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'institution_id',
        'name',
        'code',
        'category',
        'description',
        'floor',
        'section',
        'column',
        'row',
        'capacity',
        'current_count',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_count' => 'integer',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get books for this shelf (regular books table).
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'shelf_number', 'code');
    }

    /**
     * Get bookstore books for this shelf (bookshop_books table).
     */
    public function bookshopBooks()
    {
        return $this->hasMany(BookshopBook::class, 'shelf_number', 'code');
    }

    /**
     * Get all books for this shelf based on institution type.
     */
    public function getBooksForInstitution($institution)
    {
        if ($institution->type === 'bookstore') {
            return $this->bookshopBooks()
                ->where('institution_id', $institution->id)
                ->where('status', 'active')
                ->get();
        }
        
        return $this->books()
            ->where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->get();
    }

    /**
     * Get book count for this shelf based on institution type.
     */
    public function getBookCountForInstitution($institution)
    {
        if ($institution->type === 'bookstore') {
            return $this->bookshopBooks()
                ->where('institution_id', $institution->id)
                ->where('status', 'active')
                ->count();
        }
        
        return $this->books()
            ->where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->count();
    }

    /**
     * Get all books (both regular and bookstore) for this shelf.
     */
    public function getAllBooks($institution)
    {
        $books = collect();
        
        // Get regular books
        $regularBooks = $this->books()
            ->where('institution_id', $institution->id)
            ->where('status', 'approved')
            ->get();
        
        // Get bookstore books
        $bookstoreBooks = $this->bookshopBooks()
            ->where('institution_id', $institution->id)
            ->where('status', 'active')
            ->get();
        
        return $regularBooks->merge($bookstoreBooks);
    }

    // ==========================================
    // HELPERS
    // ==========================================

    public function isFull(): bool
    {
        return $this->current_count >= $this->capacity;
    }

    public function getAvailableSlots(): int
    {
        return max(0, $this->capacity - $this->current_count);
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'active' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/20',
            'inactive' => 'bg-slate-500/20 text-slate-400 border border-slate-500/20',
            'full' => 'bg-red-500/20 text-red-400 border border-red-500/20',
        ];

        $labels = [
            'active' => '🟢 Active',
            'inactive' => '⚪ Inactive',
            'full' => '🔴 Full',
        ];

        $color = $colors[$this->status] ?? 'bg-slate-500/20 text-slate-400 border border-slate-500/20';
        $label = $labels[$this->status] ?? ucfirst($this->status);
        
        return '<span class="px-2 py-1 rounded-full text-xs font-medium ' . $color . '">' . $label . '</span>';
    }

    public function getFullLocationAttribute(): string
    {
        $parts = [];
        if ($this->floor) $parts[] = "Floor: {$this->floor}";
        if ($this->section) $parts[] = "Section: {$this->section}";
        if ($this->column) $parts[] = "Column: {$this->column}";
        if ($this->row) $parts[] = "Row: {$this->row}";
        return implode(' | ', $parts) ?: 'Location not specified';
    }

    /**
     * Get usage percentage.
     */
    public function getUsagePercentageAttribute(): int
    {
        if ($this->capacity <= 0) {
            return 0;
        }
        return round(($this->current_count / $this->capacity) * 100);
    }

    /**
     * Get status color for progress bar.
     */
    public function getProgressColorAttribute(): string
    {
        $percentage = $this->usage_percentage;
        if ($percentage >= 90) {
            return 'bg-red-500';
        } elseif ($percentage >= 70) {
            return 'bg-yellow-500';
        } elseif ($percentage >= 30) {
            return 'bg-blue-500';
        } else {
            return 'bg-emerald-500';
        }
    }

    /**
     * Scope: Get active shelves.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Get full shelves.
     */
    public function scopeFull($query)
    {
        return $query->where('status', 'full');
    }

    /**
     * Scope: Get shelves with available space.
     */
    public function scopeWithAvailableSpace($query)
    {
        return $query->whereRaw('current_count < capacity');
    }

    /**
     * Scope: Search shelves.
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
}