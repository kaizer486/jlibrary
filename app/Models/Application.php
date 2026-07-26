<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'country',
        'country_code',
        'type',
        'status',
        'message',
        'biography',
        'business_name',
        'business_address',
        'tax_id',
        'phone',
        'id_document',
        'certificate_document',
        'passport_photo',
        'supporting_document',
        'business_license',
        'tax_certificate',
        'reviewed_by',
        'admin_notes',
        'reviewed_at'
    ];
    
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>',
            'approved' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Approved</span>',
            'rejected' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Unknown</span>'
        };
    }
    
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'author' => '📚 Author',
            'bookseller' => '📖 Bookseller',
            'publisher' => '📰 Publisher',
            'researcher' => '🔬 Researcher',
            default => '❓ Unknown'
        };
    }
    
    // Accessor to get full phone with country code
    public function getFullPhoneAttribute(): string
    {
        if ($this->country_code && $this->phone) {
            // If phone already has country code, return as is
            if (str_starts_with($this->phone, $this->country_code)) {
                return $this->phone;
            }
            return $this->country_code . ltrim($this->phone, '+');
        }
        return $this->phone ?? '';
    }
    
    // Accessor to get formatted address (country + phone)
    public function getContactInfoAttribute(): string
    {
        $parts = [];
        
        if ($this->country) {
            $parts[] = $this->country;
        }
        
        if ($this->phone) {
            $parts[] = $this->phone;
        }
        
        return implode(' • ', $parts);
    }
    
    // Check if application has passport photo
    public function hasPassportPhoto(): bool
    {
        return !is_null($this->passport_photo) && 
               \Illuminate\Support\Facades\Storage::disk('public')->exists($this->passport_photo);
    }
    
    // Check if application has supporting document
    public function hasSupportingDocument(): bool
    {
        return !is_null($this->supporting_document) && 
               \Illuminate\Support\Facades\Storage::disk('public')->exists($this->supporting_document);
    }
    
    // Get passport photo URL
    public function getPassportPhotoUrlAttribute(): ?string
    {
        if ($this->passport_photo && 
            \Illuminate\Support\Facades\Storage::disk('public')->exists($this->passport_photo)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->passport_photo);
        }
        return null;
    }
    
    // Get supporting document URL
    public function getSupportingDocumentUrlAttribute(): ?string
    {
        if ($this->supporting_document && 
            \Illuminate\Support\Facades\Storage::disk('public')->exists($this->supporting_document)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->supporting_document);
        }
        return null;
    }
    
    // Scope for pending applications
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    // Scope for approved applications
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
    
    // Scope for rejected applications
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    
    // Scope by type
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}