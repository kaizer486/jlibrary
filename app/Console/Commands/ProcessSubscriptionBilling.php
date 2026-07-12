<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\InstitutionWallet;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionBilling extends Command
{
    protected $signature = 'subscriptions:billing
                            {--dry-run : Run without actually charging}';
    
    protected $description = 'Process recurring subscription billing';
    
    public function handle()
    {
        $this->info('Starting subscription billing process...');
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no actual charges will be made');
        }
        
        // Find subscriptions that need billing (expiring in next 3 days)
        $subscriptions = Subscription::where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', Carbon::now()->addDays(3))
            ->where('ends_at', '>', Carbon::now())
            ->with('institution')
            ->get();
        
        $this->info("Found {$subscriptions->count()} subscriptions to process");
        
        $processed = 0;
        $successful = 0;
        $failed = 0;
        
        foreach ($subscriptions as $subscription) {
            $processed++;
            $this->line("Processing subscription #{$subscription->id}...");
            
            $result = $this->processSubscription($subscription, $dryRun);
            
            if ($result['success']) {
                $successful++;
                $this->info("  ✓ {$result['message']}");
            } else {
                $failed++;
                $this->error("  ✗ {$result['message']}");
            }
        }
        
        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', $processed],
                ['Successful', $successful],
                ['Failed', $failed],
            ]
        );
        
        Log::info('Subscription billing completed', [
            'processed' => $processed,
            'successful' => $successful,
            'failed' => $failed,
            'dry_run' => $dryRun
        ]);
        
        return Command::SUCCESS;
    }
    
    protected function processSubscription(Subscription $subscription, bool $dryRun): array
    {
        try {
            $institution = $subscription->institution;
            
            if (!$institution) {
                return [
                    'success' => false,
                    'message' => "Institution not found for subscription #{$subscription->id}"
                ];
            }
            
            $amount = $subscription->amount;
            
            // Check if institution has wallet
            $wallet = InstitutionWallet::where('institution_id', $institution->id)->first();
            
            if (!$wallet) {
                return [
                    'success' => false,
                    'message' => "No wallet found for institution"
                ];
            }
            
            if ($wallet->balance < $amount) {
                // Notify admins about insufficient balance
                $subscription->status = 'pending_payment';
                $subscription->save();
                
                return [
                    'success' => false,
                    'message' => "Insufficient balance. Needs {$amount}, has " . $wallet->balance
                ];
            }
            
            if ($dryRun) {
                return [
                    'success' => true,
                    'message' => "DRY RUN: Would charge {$amount} from institution wallet"
                ];
            }
            
            DB::beginTransaction();
            
            try {
                // Lock wallet row
                $lockedWallet = InstitutionWallet::where('id', $wallet->id)->lockForUpdate()->first();
                
                // Deduct from wallet
                $lockedWallet->balance -= $amount;
                $lockedWallet->save();
                
                // Create transaction record
                Transaction::create([
                    'institution_id' => $institution->id,
                    'type' => 'debit',
                    'amount' => $amount,
                    'balance_after' => $lockedWallet->balance,
                    'description' => "Subscription renewal: {$subscription->plan}",
                    'reference' => 'SUB_RENEW_' . uniqid(),
                    'status' => 'completed',
                    'payment_method' => 'wallet',
                ]);
                
                // Update subscription dates
                $subscription->starts_at = Carbon::now();
                $subscription->ends_at = $this->calculateNewExpiry($subscription);
                $subscription->status = 'active';
                $subscription->save();
                
                DB::commit();
                
                return [
                    'success' => true,
                    'message' => "Successfully charged {$amount} from wallet. New expiry: " . $subscription->ends_at->format('Y-m-d')
                ];
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Subscription billing error', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function calculateNewExpiry(Subscription $subscription): Carbon
    {
        $period = $subscription->billing_period ?? 'monthly';
        
        return match($period) {
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'semi_annual' => Carbon::now()->addMonths(6),
            'annual' => Carbon::now()->addYear(),
            default => Carbon::now()->addMonth(),
        };
    }
}