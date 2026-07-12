<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Check and expire subscriptions that have passed their end date';
    
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');
        
        $expired = Subscription::where('status', 'active')
            ->where('ends_at', '<=', Carbon::now())
            ->get();
        
        $count = 0;
        
        foreach ($expired as $subscription) {
            $subscription->expire();
            
            // Send notification
            // event(new SubscriptionExpired($subscription));
            
            $this->line("Expired subscription #{$subscription->id} for institution #{$subscription->institution_id}");
            $count++;
        }
        
        $this->info("Expired {$count} subscriptions.");
        Log::info("Subscription expiry check completed: {$count} subscriptions expired.");
        
        return 0;
    }
}