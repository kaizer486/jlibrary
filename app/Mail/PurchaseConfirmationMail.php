<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $bookTitle;
    public $bookAuthor;
    public $amount;
    public $transactionId;
    public $bookId;

    public function __construct($userName, $bookTitle, $bookAuthor, $amount, $transactionId, $bookId)
    {
        $this->userName = $userName;
        $this->bookTitle = $bookTitle;
        $this->bookAuthor = $bookAuthor;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->bookId = $bookId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Purchase Confirmation - Thank You for Your Order!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-confirmation',
            with: [
                'userName' => $this->userName,
                'bookTitle' => $this->bookTitle,
                'bookAuthor' => $this->bookAuthor,
                'amount' => $this->amount,
                'transactionId' => $this->transactionId,
                'bookId' => $this->bookId,
                'headerTitle' => 'Purchase Confirmed! 🛍️'
            ]
        );
    }
}