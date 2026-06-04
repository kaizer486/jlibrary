<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
      protected $fillable = [
        'instructor_id', 
        'institution_id', 
        'title', 
        'slug', 
        'description',
        'cover_image', 
        'level', 
        'status', 
        'price', 
        'is_paid', 
        'duration', 
        'enrollment_count'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'is_paid' => 'boolean',
    ];
    
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
    
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
    
    public function getLevelBadgeAttribute(): string
    {
        return match($this->level) {
            'beginner' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">📘 Beginner</span>',
            'intermediate' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">📙 Intermediate</span>',
            'advanced' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">📕 Advanced</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">Unknown</span>'
        };
    }
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">📝 Draft</span>',
            'published' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Published</span>',
            'archived' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">📦 Archived</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">Unknown</span>'
        };
    }
}