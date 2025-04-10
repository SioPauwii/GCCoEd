<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class emailVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $userId, $verificationUrl;

    /**
     * Create a new message instance.
     * @param int $userId
     * @param string $verificationUrl
     */
    public function __construct(int $userId, string $verificationUrl)
    {
        $this->userId = $userId;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $userEmail = User::where('id', $this->userId)->first()->email;
        return new Envelope(
            subject: 'Email Verification',
            from: 'gccoed@gmail.com',
            to: $userEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $id = User::where('id', $this->userId)->first()->id;
        $verifToken = User::where('id', $this->userId)->first()->email_verification_token;
        return new Content(
            markdown: 'emails.emailVerification',
            with:['verificationUrl' => $this->verificationUrl],
            view: 'emails.emailVerification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
