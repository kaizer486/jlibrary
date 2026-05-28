<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'user_id', 'type', 'status', 'message', 'business_name', 'business_address',
        'tax_id', 'phone', 'id_document', 'certificate_document', 'business_license',
        'tax_certificate', 'reviewed_by', 'admin_notes', 'reviewed_at'
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
}