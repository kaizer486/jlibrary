<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMember extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $password;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to ' . ($this->user->institution?->name ?? 'JLIBRARY'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-member',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'institution' => $this->user->institution,
                'loginUrl' => route('login'),
                'dashboardUrl' => route('dashboard'),
            ]
        );
    }
}