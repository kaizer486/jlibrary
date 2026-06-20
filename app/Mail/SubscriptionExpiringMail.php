<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $subscription;
    public $daysLeft;
    
    public function __construct(Subscription $subscription, int $daysLeft)
    {
        $this->subscription = $subscription;
        $this->daysLeft = $daysLeft;
    }
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Subscription is Expiring Soon',
        );
    }
    
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.subscriptions.expiring',
            with: [
                'subscription' => $this->subscription,
                'daysLeft' => $this->daysLeft,
                'planName' => $this->subscription->plan->name,
                'expiryDate' => $this->subscription->end_date->format('F j, Y'),
            ]
        );
    }
}