<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $subscribable;
    protected $daysLeft;
    protected $type; // 'institution' or 'user'
    protected $plan;

    public function __construct($subscribable, int $daysLeft, string $type)
    {
        $this->subscribable = $subscribable;
        $this->daysLeft = $daysLeft;
        $this->type = $type;
        $this->plan = $subscribable->getPlanLabel();
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();
        $actionUrl = $this->getActionUrl();

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line($message)
            ->action('Manage Subscription', $actionUrl)
            ->line('If you have any questions, please contact our support team.')
            ->line('Thank you for using JLIBRARY!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'subscription_reminder',
            'sub_type' => $this->type,
            'days_left' => $this->daysLeft,
            'plan' => $this->plan,
            'message' => $this->getMessage(),
            'action_url' => $this->getActionUrl(),
            'expires_at' => $this->subscribable->subscription_expires_at?->toDateTimeString(),
        ];
    }

    private function getSubject(): string
    {
        $type = ucfirst($this->type);
        $name = $this->subscribable->name ?? $this->subscribable->full_name ?? 'Your';

        if ($this->daysLeft <= 0) {
            return "⚠️ Your {$type} Subscription Has Expired";
        } elseif ($this->daysLeft === 1) {
            return "⚠️ Your {$type} Subscription Expires TOMORROW!";
        } elseif ($this->daysLeft <= 7) {
            return "⏰ Your {$type} Subscription Expires in {$this->daysLeft} Days";
        } else {
            return "📅 Your {$type} Subscription Expires in {$this->daysLeft} Days";
        }
    }

    private function getMessage(): string
    {
        $type = ucfirst($this->type);
        $name = $this->subscribable->name ?? $this->subscribable->full_name ?? 'Your';

        if ($this->daysLeft <= 0) {
            return "Your {$this->plan} subscription has expired. Please renew to continue accessing premium features.";
        } elseif ($this->daysLeft === 1) {
            return "Your {$this->plan} subscription expires TOMORROW! Renew now to avoid service interruption.";
        } elseif ($this->daysLeft <= 7) {
            return "Your {$this->plan} subscription expires in {$this->daysLeft} days. Renew now to continue enjoying premium features.";
        } else {
            return "Your {$this->plan} subscription expires in {$this->daysLeft} days. You can renew at any time.";
        }
    }

    private function getActionUrl(): string
    {
        if ($this->type === 'institution') {
            return url('/institution/subscription');
        }
        return url('/user/subscription');
    }
}