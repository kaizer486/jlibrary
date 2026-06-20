<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:reminders';
    
    protected $description = 'Send reminders for expiring subscriptions';
    
    public function handle()
    {
        $this->info('Sending subscription reminders...');
        
        // Send reminder 7 days before expiration using 'ends_at'
        $expiringSoon = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                Carbon::now()->addDays(7)->startOfDay(),
                Carbon::now()->addDays(7)->endOfDay()
            ])
            ->get();
        
        $this->info("Found {$expiringSoon->count()} subscriptions expiring in 7 days");
        
        $sent = 0;
        
        foreach ($expiringSoon as $subscription) {
            $this->sendReminder($subscription, 7);
            $sent++;
        }
        
        // Send reminder 1 day before expiration
        $expiringTomorrow = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [
                Carbon::now()->addDay()->startOfDay(),
                Carbon::now()->addDay()->endOfDay()
            ])
            ->get();
        
        $this->info("Found {$expiringTomorrow->count()} subscriptions expiring tomorrow");
        
        foreach ($expiringTomorrow as $subscription) {
            $this->sendReminder($subscription, 1);
            $sent++;
        }
        
        $this->info("Sent {$sent} reminders");
        
        Log::info('Subscription reminders sent', [
            'sent' => $sent
        ]);
        
        return Command::SUCCESS;
    }
    
    protected function sendReminder(Subscription $subscription, int $daysLeft): void
    {
        $institution = $subscription->institution;
        
        if (!$institution) {
            return;
        }
        
        $this->line("  - Would send reminder to institution #{$institution->id} ({$daysLeft} days left)");
        // TODO: Implement actual email sending
    }
}