<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\User;
use App\Notifications\SubscriptionReminderNotification;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription expiry reminders to users and institutions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending subscription reminders...');

        // ==========================================
        // INSTITUTION SUBSCRIPTIONS
        // ==========================================
        $this->sendInstitutionReminders();

        // ==========================================
        // USER SUBSCRIPTIONS
        // ==========================================
        $this->sendUserReminders();

        $this->info('Subscription reminders sent successfully!');
        return Command::SUCCESS;
    }

    private function sendInstitutionReminders()
    {
        $institutions = Institution::where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->get();

        $this->info("Found {$institutions->count()} active institution subscriptions.");

        foreach ($institutions as $institution) {
            $daysLeft = $institution->getDaysLeft();
            $this->sendReminder($institution, $daysLeft, 'institution');
        }
    }

    private function sendUserReminders()
    {
        $users = User::whereHas('activeSubscription')->get();

        $this->info("Found {$users->count()} active user subscriptions.");

        foreach ($users as $user) {
            $daysLeft = $user->getSubscriptionDaysLeft();
            $this->sendReminder($user, $daysLeft, 'user');
        }
    }

    private function sendReminder($subscribable, int $daysLeft, string $type)
    {
        // Get the admins or the user to notify
        $notifiables = $this->getNotifiables($subscribable, $type);

        foreach ($notifiables as $notifiable) {
            // Check if we should send this reminder
            if ($this->shouldSendReminder($notifiable, $subscribable, $daysLeft, $type)) {
                $notifiable->notify(new SubscriptionReminderNotification($subscribable, $daysLeft, $type));
                $this->info("Sent reminder to: {$notifiable->full_name} ({$type}) - {$daysLeft} days left");
                
                // Mark reminder as sent
                $this->markReminderSent($subscribable, $daysLeft);
            }
        }
    }

    private function getNotifiables($subscribable, string $type)
    {
        if ($type === 'institution') {
            // Notify all institution admins
            return $subscribable->admins()->get();
        }

        // Notify the user themselves
        return [$subscribable];
    }

    private function shouldSendReminder($notifiable, $subscribable, int $daysLeft, string $type): bool
    {
        // Don't send if already expired or no days left
        if ($daysLeft <= 0) {
            return false;
        }

        // Check which reminders have been sent
        $field = $this->getReminderField($daysLeft);
        if (!$field) {
            return false;
        }

        // Check if this reminder has already been sent
        return is_null($subscribable->$field);
    }

    private function getReminderField(int $daysLeft): ?string
    {
        $reminders = [
            30 => 'reminder_30_sent_at',
            15 => 'reminder_15_sent_at',
            7 => 'reminder_7_sent_at',
            3 => 'reminder_3_sent_at',
            1 => 'reminder_1_sent_at',
        ];

        // Find the closest reminder that matches
        foreach ($reminders as $days => $field) {
            if ($daysLeft <= $days) {
                return $field;
            }
        }

        return null;
    }

    private function markReminderSent($subscribable, int $daysLeft)
    {
        $field = $this->getReminderField($daysLeft);
        if ($field) {
            $subscribable->update([$field => now()]);
        }
    }
}