<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionExpirations extends Command
{
    protected $signature = 'subscriptions:expire';
    
    protected $description = 'Handle expired subscriptions and revoke access';
    
    public function handle()
    {
        $this->info('Processing subscription expirations...');
        
        // Find expired subscriptions
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', Carbon::now())
            ->get();
        
        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions");
        
        $expired = 0;
        
        foreach ($expiredSubscriptions as $subscription) {
            $subscription->expire();
            $expired++;
            $this->line("Expired subscription #{$subscription->id} for institution #{$subscription->institution_id}");
        }
        
        $this->info("Expired {$expired} subscriptions");
        
        Log::info('Subscription expiration completed', [
            'expired' => $expired
        ]);
        
        return Command::SUCCESS;
    }
}