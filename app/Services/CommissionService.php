<?php

namespace App\Services;

use App\Models\CommissionLog;
use App\Models\AuthorWallet;
use App\Models\User;
use App\Models\MarketplaceListing;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CommissionService
{
    // 80% to author, 20% to platform
    const AUTHOR_PERCENTAGE = 80;
    const PLATFORM_PERCENTAGE = 20;
    
    /**
     * Calculate commission split
     */
    public function calculateSplit(float $amount): array
    {
        $authorEarnings = ($amount * self::AUTHOR_PERCENTAGE) / 100;
        $platformFee = ($amount * self::PLATFORM_PERCENTAGE) / 100;
        
        return [
            'author_earnings' => round($authorEarnings, 2),
            'platform_fee' => round($platformFee, 2),
            'author_percentage' => self::AUTHOR_PERCENTAGE,
            'platform_percentage' => self::PLATFORM_PERCENTAGE,
        ];
    }
    
    /**
     * Process commission for a sale (called when someone buys a book)
     */
    public function processCommission(
        User $author,
        User $buyer,
        $saleable,
        float $totalAmount,
        string $currency = 'TZS',
        ?float $exchangeRate = null
    ): CommissionLog {
        $split = $this->calculateSplit($totalAmount);
        
        DB::beginTransaction();
        
        try {
            // Create commission log
            $commissionLog = CommissionLog::create([
                'author_id' => $author->id,
                'buyer_id' => $buyer->id,
                'saleable_type' => get_class($saleable),
                'saleable_id' => $saleable->id,
                'total_amount' => $totalAmount,
                'author_earnings' => $split['author_earnings'],
                'platform_fee' => $split['platform_fee'],
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'status' => 'pending',
            ]);
            
            // Immediately pay to author wallet (or queue for later)
            $this->payAuthor($commissionLog);
            
            DB::commit();
            return $commissionLog;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Pay author their earnings (add to author wallet)
     */
    public function payAuthor(CommissionLog $commissionLog): bool
    {
        if ($commissionLog->status !== 'pending') {
            return false;
        }
        
        DB::beginTransaction();
        
        try {
            // Get or create author wallet
            $authorWallet = AuthorWallet::firstOrCreate(
                ['user_id' => $commissionLog->author_id],
                ['currency' => $commissionLog->currency ?? 'TZS']
            );
            
            // Add earnings to author wallet
            $authorWallet->addEarnings(
                (float) $commissionLog->author_earnings,
                "Earnings from sale #{$commissionLog->id} - " . $commissionLog->saleable_type
            );
            
            // Update commission log
            $commissionLog->status = 'completed';
            $commissionLog->payout_date = Carbon::now();
            $commissionLog->payout_method = 'author_wallet';
            $commissionLog->save();
            
            // If it's a marketplace listing, mark commission as paid
            if ($commissionLog->saleable_type === MarketplaceListing::class) {
                $listing = MarketplaceListing::find($commissionLog->saleable_id);
                if ($listing) {
                    $listing->commission_paid = true;
                    $listing->commission_log_id = $commissionLog->id;
                    $listing->save();
                }
            }
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to pay author: ' . $e->getMessage(), [
                'commission_log_id' => $commissionLog->id
            ]);
            return false;
        }
    }
    
    /**
     * Get author's total earnings
     */
    public function getAuthorTotalEarnings(int $authorId): float
    {
        $total = CommissionLog::where('author_id', $authorId)
            ->where('status', 'completed')
            ->sum('author_earnings');
        
        return (float) $total;
    }
    
    /**
     * Get platform total fees collected
     */
    public function getPlatformTotalFees(): float
    {
        $total = CommissionLog::where('status', 'completed')
            ->sum('platform_fee');
        
        return (float) $total;
    }
    
    /**
     * Get monthly platform fees for dashboard
     */
    public function getMonthlyPlatformFees(int $months = 6): array
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $fees = CommissionLog::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('platform_fee');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'fees' => (float) $fees,
            ];
        }
        
        return $data;
    }
}