<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscription:send-reminders';
    protected $description = 'Send subscription expiry reminders to users and institutions';

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
        // Get institutions with active subscriptions from subscriptions table
        $subscriptions = Subscription::where('subscribable_type', Institution::class)
            ->where('status', 'active')
            ->where('ends_at', '>', Carbon::now())
            ->with('subscribable')
            ->get();

        $this->info("Found {$subscriptions->count()} active institution subscriptions.");

        foreach ($subscriptions as $subscription) {
            $institution = $subscription->subscribable;
            if ($institution) {
                $daysLeft = $subscription->daysRemaining();
                $this->sendReminder($institution, $subscription, $daysLeft, 'institution');
            }
        }
    }

    private function sendUserReminders()
    {
        // Get users with active subscriptions
        $subscriptions = Subscription::where('subscribable_type', User::class)
            ->where('status', 'active')
            ->where('ends_at', '>', Carbon::now())
            ->with('subscribable')
            ->get();

        $this->info("Found {$subscriptions->count()} active user subscriptions.");

        foreach ($subscriptions as $subscription) {
            $user = $subscription->subscribable;
            if ($user) {
                $daysLeft = $subscription->daysRemaining();
                $this->sendReminder($user, $subscription, $daysLeft, 'user');
            }
        }
    }

    private function sendReminder($subscribable, $subscription, int $daysLeft, string $type)
    {
        // Get the admins or the user to notify
        $notifiables = $this->getNotifiables($subscribable, $type);

        foreach ($notifiables as $notifiable) {
            // Check if we should send this reminder
            $field = $this->getReminderField($daysLeft);
            if ($field && $this->shouldSendReminder($subscribable, $field)) {
                // Send notification
                try {
                    $notifiable->notify(new SubscriptionReminderNotification($subscription, $daysLeft, $type));
                    $this->info("Sent reminder to: {$notifiable->name} ({$type}) - {$daysLeft} days left");
                    
                    // Mark reminder as sent
                    $this->markReminderSent($subscribable, $field);
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder: " . $e->getMessage());
                }
            }
        }
    }

    private function getNotifiables($subscribable, string $type)
    {
        if ($type === 'institution') {
            // Notify all institution admins
            if (method_exists($subscribable, 'admins')) {
                return $subscribable->admins()->get();
            }
            // Fallback: get the institution owner
            return [$subscribable->user ?? $subscribable];
        }

        // Notify the user themselves
        return [$subscribable];
    }

    private function shouldSendReminder($subscribable, string $field): bool
    {
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

        // Find the closest reminder
        foreach ($reminders as $days => $field) {
            if ($daysLeft <= $days) {
                return $field;
            }
        }

        return null;
    }

    private function markReminderSent($subscribable, string $field)
    {
        $subscribable->update([$field => Carbon::now()]);
    }
}