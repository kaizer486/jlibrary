<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateEarnedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $quizTitle;
    public $percentage;
    public $passingScore;

    public function __construct($userName, $quizTitle, $percentage, $passingScore)
    {
        $this->userName = $userName;
        $this->quizTitle = $quizTitle;
        $this->percentage = $percentage;
        $this->passingScore = $passingScore;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Congratulations! You\'ve Earned a Certificate on JLIBRARY',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.certificate-earned',
            with: [
                'userName' => $this->userName,
                'quizTitle' => $this->quizTitle,
                'percentage' => $this->percentage,
                'passingScore' => $this->passingScore,
                'headerTitle' => 'Certificate Earned! 🎓'
            ]
        );
    }
}