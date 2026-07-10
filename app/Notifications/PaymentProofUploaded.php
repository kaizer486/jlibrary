<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentProofUploaded extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $subscription;
    protected $filePath;
    
    public function __construct(Subscription $subscription, string $filePath)
    {
        $this->subscription = $subscription;
        $this->filePath = $filePath;
    }
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Payment Proof Uploaded')
            ->greeting('Hello ' . $notifiable->full_name . '!')
            ->line('A new payment proof has been uploaded for subscription verification.')
            ->line('Institution: ' . $this->subscription->institution->name)
            ->line('Plan: ' . ucfirst($this->subscription->plan))
            ->line('Amount: TSh ' . number_format($this->subscription->amount, 2))
            ->action('View Subscription', route('super-admin.institutions.show', $this->subscription->institution_id))
            ->line('Please verify and activate the subscription.');
    }
    
    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'institution_id' => $this->subscription->institution_id,
            'institution_name' => $this->subscription->institution->name,
            'plan' => $this->subscription->plan,
            'amount' => $this->subscription->amount,
            'file_path' => $this->filePath,
            'message' => 'New payment proof uploaded for verification',
        ];
    }
}