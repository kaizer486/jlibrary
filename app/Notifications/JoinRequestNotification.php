<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JoinRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $subject = 'Join Request Update';
        $message = $this->data['message'];

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('View Details', url('/notifications'))
            ->line('Thank you for using our platform!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'join_request',
            'sub_type' => $this->data['type'],
            'message' => $this->data['message'],
            'join_request_id' => $this->data['join_request_id'] ?? null,
            'institution_id' => $this->data['institution_id'] ?? null,
            'institution_name' => $this->data['institution_name'] ?? null,
            'user_id' => $this->data['user_id'] ?? null,
            'rejection_reason' => $this->data['rejection_reason'] ?? null,
            'created_at' => now(),
        ];
    }
}