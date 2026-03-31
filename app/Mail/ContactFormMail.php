<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contactData;

    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Form Submission - BloodLink',
        );
    }

    public function build()
    {
        $emailContent = "New Contact Form Submission - BloodLink\n\n";
        $emailContent .= "Name: " . $this->contactData['name'] . "\n";
        $emailContent .= "Email: " . $this->contactData['email'] . "\n";
        $emailContent .= "Phone: " . ($this->contactData['phone'] ?? 'Not provided') . "\n";
        $emailContent .= "Subject: " . $this->contactData['subject'] . "\n\n";
        $emailContent .= "Message:\n" . $this->contactData['message'] . "\n\n";
        $emailContent .= "---\n";
        $emailContent .= "Submitted on: " . now()->format('Y-m-d H:i:s') . "\n";
        $emailContent .= "BloodLink - Connecting donors with those in need\n";
        $emailContent .= "Please respond to this inquiry at your earliest convenience.";

        return $this->view('emails.contact-form-simple', [
            'content' => nl2br($emailContent)
        ]);
    }
}
