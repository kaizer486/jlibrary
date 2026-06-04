<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $institution_id
 * @property float $balance
 * @property float $total_earned
 * @property float $total_withdrawn
 * @property float $pending_withdrawal
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class InstitutionWallet extends Model
{
    protected $table = 'institution_wallets';
    
    protected $fillable = [
        'institution_id', 'balance', 'total_earned', 'total_withdrawn', 'pending_withdrawal'
    ];
    
    protected $casts = [
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'pending_withdrawal' => 'decimal:2',
    ];
    
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
    
    public function addEarnings(float $amount): self
    {
        $this->balance += $amount;
        $this->total_earned += $amount;
        $this->save();
        
        return $this;
    }
    
    public function deductForWithdrawal(float $amount): self
    {
        $this->balance -= $amount;
        $this->pending_withdrawal += $amount;
        $this->save();
        
        return $this;
    }
    
    public function completeWithdrawal(float $amount): self
    {
        $this->pending_withdrawal -= $amount;
        $this->total_withdrawn += $amount;
        $this->save();
        
        return $this;
    }
    
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }
    
    // Initialize wallet for new institution
    public static function createForInstitution(int $institutionId): self
    {
        return self::create([
            'institution_id' => $institutionId,
            'balance' => 0,
            'total_earned' => 0,
            'total_withdrawn' => 0,
            'pending_withdrawal' => 0,
        ]);
    }
    
    // Accessor for formatted balance
    public function getFormattedBalanceAttribute(): string
    {
        return 'TSh ' . number_format($this->balance, 2);
    }
    
    // Accessor for formatted total earned
    public function getFormattedTotalEarnedAttribute(): string
    {
        return 'TSh ' . number_format($this->total_earned, 2);
    }
}