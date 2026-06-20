<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AuthorWallet extends Model
{
    protected $table = 'author_wallets';
    
    protected $fillable = [
        'user_id',
        'balance',
        'total_earned',
        'total_withdrawn',
        'pending_withdrawal',
        'currency',
        'stripe_account_id',
        'stripe_onboarded',
        'preferred_payout_method',
        'payout_phone',
        'payout_bank_account',
    ];
    
    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_withdrawal' => 'decimal:2',
        'stripe_onboarded' => 'boolean',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Add earnings to author wallet (after commission split)
     */
    public function addEarnings(float $amount, ?string $description = null): void
    {
        DB::beginTransaction();
        
        try {
            // Use a fresh instance with lock to prevent race conditions
            $wallet = AuthorWallet::where('id', $this->id)->lockForUpdate()->first();
            
            // ✅ Convert float to numeric string for decimal column
            $currentBalance = (float) $wallet->balance;
            $newBalanceValue = $currentBalance + $amount;
            
            $wallet->balance = number_format($newBalanceValue, 2, '.', '');
            $wallet->total_earned = number_format((float) $wallet->total_earned + $amount, 2, '.', '');
            $wallet->save();
            
            // Log the transaction
            Transaction::create([
                'user_id' => $wallet->user_id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_after' => $wallet->balance,
                'description' => $description ?? 'Commission earnings from sale',
                'reference' => 'COMM_' . uniqid(),
                'status' => 'completed',
                'method' => 'commission',
                'payable_type' => 'App\\Models\\User',
                'payable_id' => $wallet->user_id,
            ]);
            
            // Update the current instance
            $this->balance = $wallet->balance;
            $this->total_earned = $wallet->total_earned;
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to add earnings: ' . $e->getMessage());
        }
    }
    
    /**
     * Deduct pending withdrawal amount
     */
    public function markWithdrawalRequest(float $amount): void
    {
        DB::beginTransaction();
        
        try {
            $wallet = AuthorWallet::where('id', $this->id)->lockForUpdate()->first();
            
            $currentBalance = (float) $wallet->balance;
            $currentPending = (float) $wallet->pending_withdrawal;
            
            if ($currentBalance < $amount) {
                throw new \Exception('Insufficient balance for withdrawal');
            }
            
            $newBalanceValue = $currentBalance - $amount;
            $newPendingValue = $currentPending + $amount;
            
            $wallet->balance = number_format($newBalanceValue, 2, '.', '');
            $wallet->pending_withdrawal = number_format($newPendingValue, 2, '.', '');
            $wallet->save();
            
            $this->balance = $wallet->balance;
            $this->pending_withdrawal = $wallet->pending_withdrawal;
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Complete withdrawal (admin approved)
     */
    public function completeWithdrawal(float $amount): void
    {
        DB::beginTransaction();
        
        try {
            $wallet = AuthorWallet::where('id', $this->id)->lockForUpdate()->first();
            
            $currentPending = (float) $wallet->pending_withdrawal;
            $currentWithdrawn = (float) $wallet->total_withdrawn;
            
            $newPendingValue = $currentPending - $amount;
            $newWithdrawnValue = $currentWithdrawn + $amount;
            
            $wallet->pending_withdrawal = number_format($newPendingValue, 2, '.', '');
            $wallet->total_withdrawn = number_format($newWithdrawnValue, 2, '.', '');
            $wallet->save();
            
            $this->pending_withdrawal = $wallet->pending_withdrawal;
            $this->total_withdrawn = $wallet->total_withdrawn;
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cancel withdrawal (refund to balance)
     */
    public function cancelWithdrawal(float $amount): void
    {
        DB::beginTransaction();
        
        try {
            $wallet = AuthorWallet::where('id', $this->id)->lockForUpdate()->first();
            
            $currentBalance = (float) $wallet->balance;
            $currentPending = (float) $wallet->pending_withdrawal;
            
            $newBalanceValue = $currentBalance + $amount;
            $newPendingValue = $currentPending - $amount;
            
            $wallet->balance = number_format($newBalanceValue, 2, '.', '');
            $wallet->pending_withdrawal = number_format($newPendingValue, 2, '.', '');
            $wallet->save();
            
            $this->balance = $wallet->balance;
            $this->pending_withdrawal = $wallet->pending_withdrawal;
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}