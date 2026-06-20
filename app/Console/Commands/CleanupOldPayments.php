<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupOldPayments extends Command
{
    protected $signature = 'payments:cleanup 
                            {--months=12 : Keep payments newer than this many months}
                            {--dry-run : Run without actually deleting}';
    
    protected $description = 'Clean up old payment records';
    
    public function handle()
    {
        $months = (int) $this->option('months');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subMonths($months);
        
        $this->info("Cleaning up payments older than {$months} months ({$cutoffDate->format('Y-m-d')})");
        
        if ($dryRun) {
            $this->warn('Running in DRY RUN mode - no actual deletions will be made');
        }
        
        // Count records to delete
        $oldPayments = Payment::where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->count();
        
        $oldTransactions = Transaction::where('created_at', '<', $cutoffDate)
            ->where('status', 'completed')
            ->count();
        
        $this->table(
            ['Table', 'Records to Delete'],
            [
                ['payments', $oldPayments],
                ['transactions', $oldTransactions],
            ]
        );
        
        if ($dryRun) {
            $this->info('DRY RUN complete. No records were deleted.');
            return Command::SUCCESS;
        }
        
        if ($this->confirm('Do you want to proceed with deletion?')) {
            DB::beginTransaction();
            
            try {
                $deletedPayments = Payment::where('created_at', '<', $cutoffDate)
                    ->where('status', 'completed')
                    ->delete();
                
                $deletedTransactions = Transaction::where('created_at', '<', $cutoffDate)
                    ->where('status', 'completed')
                    ->delete();
                
                DB::commit();
                
                $this->info("Deleted {$deletedPayments} payments");
                $this->info("Deleted {$deletedTransactions} transactions");
                
                Log::info('Payment cleanup completed', [
                    'deleted_payments' => $deletedPayments,
                    'deleted_transactions' => $deletedTransactions,
                    'cutoff_date' => $cutoffDate->format('Y-m-d')
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error('Failed to clean up: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->info('Cleanup cancelled.');
        }
        
        return Command::SUCCESS;
    }
}