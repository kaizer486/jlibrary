<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Get logo as base64
        $logoPath = public_path('images/logo.jpeg');
        $logoBase64 = '';
        
        if (File::exists($logoPath)) {
            $logoData = File::get($logoPath);
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
        }

        // Get logo as URL (for backup)
        $logoUrl = config('app.url') . '/images/logo.jpeg';

        return (new MailMessage)
            ->subject('Reset Your Password - JLIBRARY')
            ->view('vendor.notifications.email', [
                'greeting' => $notifiable->name ?? 'User',
                'actionUrl' => $url,
                'logoBase64' => $logoBase64,
                'logoUrl' => $logoUrl,
                'appUrl' => config('app.url'),
            ]);
    }
}