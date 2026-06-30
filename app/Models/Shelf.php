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

    public function books()
    {
        return $this->hasMany(Book::class, 'shelf_number', 'code');
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

   /**
 * Get status badge HTML.
 */
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
        return implode(' | ', $parts);
    }
}