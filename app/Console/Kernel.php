<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Run subscription billing daily at 2 AM
        $schedule->command('subscriptions:billing')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->emailOutputOnFailure(config('mail.admin_address'));
        
        // Run subscription expiration daily at 3 AM
        $schedule->command('subscriptions:expire')
            ->dailyAt('03:00')
            ->withoutOverlapping();
        
        // Send subscription reminders daily at 9 AM
        $schedule->command('subscriptions:reminders')
            ->dailyAt('09:00')
            ->withoutOverlapping();
        
        // Clean up old payment records monthly
        $schedule->command('payments:cleanup --months=12')
            ->monthly()
            ->withoutOverlapping();

        $schedule->command('db:backup')->daily()->at('01:00');
    }
    
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        
        require base_path('routes/console.php');
    }
}