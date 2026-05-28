<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $bookTitle;
    public $bookAuthor;
    public $bookPages;
    public $bookDownloads;
    public $bookDescription;
    public $bookId;

    public function __construct($userName, $bookTitle, $bookAuthor, $bookPages, $bookDownloads, $bookDescription, $bookId)
    {
        $this->userName = $userName;
        $this->bookTitle = $bookTitle;
        $this->bookAuthor = $bookAuthor;
        $this->bookPages = $bookPages;
        $this->bookDownloads = $bookDownloads;
        $this->bookDescription = $bookDescription;
        $this->bookId = $bookId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📚 New Book Added to Your JLIBRARY Collection!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.book-access',
            with: [
                'userName' => $this->userName,
                'bookTitle' => $this->bookTitle,
                'bookAuthor' => $this->bookAuthor,
                'bookPages' => $this->bookPages,
                'bookDownloads' => $this->bookDownloads,
                'bookDescription' => $this->bookDescription,
                'bookId' => $this->bookId,
                'headerTitle' => 'New Book Available! 📚'
            ]
        );
    }
}