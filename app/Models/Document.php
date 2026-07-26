<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'file_path',
        'file_type',
        'file_size',
        'processed_at'
    ];
    
    protected $casts = [
        'file_size' => 'integer',
        'processed_at' => 'datetime',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }
    
    /**
     * Get the file extension
     */
    public function getFileExtensionAttribute()
    {
        if ($this->file_path) {
            return pathinfo($this->file_path, PATHINFO_EXTENSION);
        }
        return null;
    }
    
    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';
        
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}