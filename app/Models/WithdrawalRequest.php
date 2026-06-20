<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @property int $id
 * @property int|null $institution_id
 * @property int|null $user_id
 * @property int $requested_by
 * @property float $amount
 * @property string $status
 * @property string $payment_method
 * @property string $account_details
 * @property string|null $notes
 * @property int|null $processed_by
 * @property Carbon|null $processed_at
 * @property string|null $rejection_reason
 * @property string|null $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WithdrawalRequest extends Model
{
    protected $table = 'withdrawal_requests';
    
    protected $fillable = [
        'institution_id', 'user_id', 'requested_by', 'amount', 'status', 
        'payment_method', 'account_details', 'notes', 'processed_by', 
        'processed_at', 'rejection_reason', 'type'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];
    
  // ENCRYPT when setting
    public function setAccountDetailsAttribute($value)
    {
        $this->attributes['account_details'] = encrypt($value);
    }
    
    // DECRYPT when getting
    public function getAccountDetailsAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value; // Fallback for unencrypted data
        }
    }

    // Get the institution (if institution withdrawal)
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    //
    
    // Get the user (if user withdrawal)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    // Check if this is an institution withdrawal
    public function isInstitutionWithdrawal(): bool
    {
        return !is_null($this->institution_id);
    }
    
    // Check if this is a user withdrawal
    public function isUserWithdrawal(): bool
    {
        return !is_null($this->user_id);
    }
    
    public function approve(int $processedBy): self
    {
        $this->status = 'processing';
        $this->processed_by = $processedBy;
        $this->processed_at = Carbon::now();
        $this->save();
        
        return $this;
    }
    
    public function complete(): self
    {
        $this->status = 'completed';
        $this->save();
        
        // Update wallet based on type
        if ($this->isInstitutionWithdrawal()) {
            $wallet = $this->institution->wallet;
            if ($wallet) {
                $wallet->pending_withdrawal -= $this->amount;
                $wallet->total_withdrawn += $this->amount;
                $wallet->save();
            }
        }
        
        return $this;
    }
    
    public function reject(int $processedBy, string $reason): self
    {
        $this->status = 'rejected';
        $this->processed_by = $processedBy;
        $this->processed_at = Carbon::now();
        $this->rejection_reason = $reason;
        $this->save();
        
        // Return money to wallet based on type
        if ($this->isInstitutionWithdrawal()) {
            $wallet = $this->institution->wallet;
            if ($wallet) {
                $wallet->balance += $this->amount;
                $wallet->pending_withdrawal -= $this->amount;
                $wallet->save();
            }
        } elseif ($this->isUserWithdrawal()) {
            // Refund to user's wallet
            $user = $this->user;
            if ($user) {
                $user->wallet_balance += $this->amount;
                $user->save();
            }
            
            // Update transaction record
            Transaction::where('reference', 'WD-' . $this->id)
                ->update(['status' => 'failed']);
        }
        
        return $this;
    }
    
    public function cancel(): self
    {
        $this->status = 'cancelled';
        $this->save();
        
        return $this;
    }
    
    // Get status badge HTML
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Pending</span>',
            'processing' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">🔄 Processing</span>',
            'completed' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Completed</span>',
            'rejected' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">❌ Rejected</span>',
            'cancelled' => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Cancelled</span>',
            default => '<span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Unknown</span>'
        };
    }
    
    // Get formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return 'TSh ' . number_format($this->amount, 2);
    }
}