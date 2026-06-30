<?php

namespace App\Services;

use App\Models\CommissionLog;
use App\Models\AuthorWallet;
use App\Models\InstitutionWallet;
use App\Models\User;
use App\Models\Book;
use App\Models\MarketplaceListing;
use App\Models\Transaction;
use App\Models\LibraryPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CommissionService
{
    // Default percentages
    const AUTHOR_PERCENTAGE = 70;
    const INSTITUTION_PERCENTAGE = 10;
    const PLATFORM_PERCENTAGE = 20;
    
    // Library specific percentages (80% to library, 20% to platform)
    const LIBRARY_PERCENTAGE = 80;
    const LIBRARY_PLATFORM_PERCENTAGE = 20;
    
    /**
     * Calculate commission split for regular sales.
     */
    public function calculateSplit(float $amount): array
    {
        $authorEarnings = ($amount * self::AUTHOR_PERCENTAGE) / 100;
        $institutionShare = ($amount * self::INSTITUTION_PERCENTAGE) / 100;
        $platformFee = ($amount * self::PLATFORM_PERCENTAGE) / 100;
        
        return [
            'author_earnings' => round($authorEarnings, 2),
            'institution_share' => round($institutionShare, 2),
            'platform_fee' => round($platformFee, 2),
            'author_percentage' => self::AUTHOR_PERCENTAGE,
            'institution_percentage' => self::INSTITUTION_PERCENTAGE,
            'platform_percentage' => self::PLATFORM_PERCENTAGE,
        ];
    }
    
    /**
     * Calculate commission split for library books.
     */
    public function calculateLibrarySplit(float $amount): array
    {
        $libraryShare = ($amount * self::LIBRARY_PERCENTAGE) / 100;
        $platformFee = ($amount * self::LIBRARY_PLATFORM_PERCENTAGE) / 100;
        
        return [
            'library_share' => round($libraryShare, 2),
            'platform_fee' => round($platformFee, 2),
            'library_percentage' => self::LIBRARY_PERCENTAGE,
            'platform_percentage' => self::LIBRARY_PLATFORM_PERCENTAGE,
        ];
    }
    
    /**
     * Process commission for a book purchase (regular).
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
                'processed_by' => auth()->id() ?? null,
            ]);
            
            // Pay author immediately
            $this->payAuthor($commissionLog);
            
            // Record platform fee (super admin)
            $this->recordPlatformFee($commissionLog);
            
            DB::commit();
            return $commissionLog;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Process commission for a library book purchase (80/20 split).
     */
    public function processLibraryCommission(
        Book $book,
        User $buyer,
        float $totalAmount,
        string $paymentMethod = 'wallet'
    ): LibraryPayment {
        $split = $this->calculateLibrarySplit($totalAmount);
        
        DB::beginTransaction();
        
        try {
            // Create library payment record
            $payment = LibraryPayment::create([
                'book_id' => $book->id,
                'user_id' => $buyer->id,
                'institution_id' => $book->institution_id,
                'amount' => $totalAmount,
                'library_share' => $split['library_share'],
                'platform_share' => $split['platform_fee'],
                'author_share' => 0, // Library books don't have author share
                'payment_method' => $paymentMethod,
                'transaction_id' => 'LIB_' . time() . '_' . $buyer->id . '_' . $book->id,
                'status' => 'completed',
            ]);
            
            // Credit institution wallet (80%)
            if ($book->institution_id) {
                $institution = $book->institution;
                if ($institution && $institution->wallet) {
                    $institution->wallet->increment('balance', $split['library_share']);
                    $institution->wallet->increment('total_earned', $split['library_share']);
                }
            }
            
            // Record platform fee (super admin) (20%)
            $this->recordPlatformFeeForLibrary($split['platform_fee'], $book, $buyer);
            
            DB::commit();
            return $payment;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Pay author their earnings.
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
                "Earnings from sale #{$commissionLog->id}"
            );
            
            // Update commission log
            $commissionLog->status = 'completed';
            $commissionLog->payout_date = Carbon::now();
            $commissionLog->payout_method = 'author_wallet';
            $commissionLog->payout_reference = 'PAY_' . time() . '_' . $commissionLog->id;
            $commissionLog->processed_by = auth()->id() ?? null;
            $commissionLog->save();
            
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
     * Record platform fee (super admin) for regular sales.
     */
    private function recordPlatformFee(CommissionLog $commissionLog): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        
        if (!$superAdmin) {
            return;
        }
        
        // Credit super admin wallet
        $superAdmin->incrementWallet($commissionLog->platform_fee);
        
        Transaction::create([
            'user_id' => $superAdmin->id,
            'type' => 'credit',
            'amount' => $commissionLog->platform_fee,
            'balance_after' => $superAdmin->wallet_balance,
            'description' => "Platform commission from sale #{$commissionLog->id}",
            'reference' => 'PLAT_' . time() . '_' . $commissionLog->id,
            'status' => 'completed',
            'method' => 'commission',
            'payable_type' => CommissionLog::class,
            'payable_id' => $commissionLog->id,
        ]);
    }
    
    /**
     * Record platform fee (super admin) for library sales.
     */
    private function recordPlatformFeeForLibrary(float $amount, Book $book, User $buyer): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        
        if (!$superAdmin) {
            return;
        }
        
        // Credit super admin wallet
        $superAdmin->incrementWallet($amount);
        
        Transaction::create([
            'user_id' => $superAdmin->id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $superAdmin->wallet_balance,
            'description' => "Platform commission for library book: {$book->title}",
            'reference' => 'PLAT_LIB_' . time() . '_' . $book->id,
            'status' => 'completed',
            'method' => 'commission',
            'payable_type' => Book::class,
            'payable_id' => $book->id,
        ]);
    }
    
    /**
     * Get author's total earnings.
     */
    public function getAuthorTotalEarnings(int $authorId): float
    {
        $total = CommissionLog::where('author_id', $authorId)
            ->where('status', 'completed')
            ->sum('author_earnings');
        
        return (float) $total;
    }
    
    /**
     * Get platform total fees collected.
     */
    public function getPlatformTotalFees(): float
    {
        $total = CommissionLog::where('status', 'completed')
            ->sum('platform_fee');
        
        return (float) $total;
    }
    
    /**
     * Get library total earnings for an institution.
     */
    public function getLibraryTotalEarnings(int $institutionId): float
    {
        $total = LibraryPayment::where('institution_id', $institutionId)
            ->where('status', 'completed')
            ->sum('library_share');
        
        return (float) $total;
    }
    
    /**
     * Get monthly platform fees for dashboard.
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
    
    /**
     * Get monthly library earnings for an institution.
     */
    public function getMonthlyLibraryEarnings(int $institutionId, int $months = 6): array
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $earnings = LibraryPayment::where('institution_id', $institutionId)
                ->where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('library_share');
            
            $data[] = [
                'month' => $month->format('M Y'),
                'earnings' => (float) $earnings,
            ];
        }
        
        return $data;
    }
}