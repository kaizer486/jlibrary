<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ==========================================
        // SUBSCRIPTION COMMANDS
        // ==========================================
        
        // Send subscription reminders - Daily at 9:00 AM
        $schedule->command('subscription:send-reminders')
            ->dailyAt('09:00')
            ->withoutOverlapping();
        
        // Process expirations - Daily at 12:00 AM
        $schedule->command('subscriptions:expire')
            ->daily()
            ->withoutOverlapping();
        
        // Process billing for auto-renew - Daily at 2:00 AM
        $schedule->command('subscriptions:billing')
            ->dailyAt('02:00')
            ->withoutOverlapping();
        
        // ==========================================
        // OTHER COMMANDS
        // ==========================================
        
        // Backup, cache clearing, etc.
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}