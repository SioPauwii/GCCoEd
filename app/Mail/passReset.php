<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class passReset extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $email;    
    public string $name;

    /**
     * Create a new message instance.
     * @param string $token
     * @param string $email
     * @param string $name
     */
    public function __construct(string $token, string $email, string $name)
    {
        $this->token = $token;
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
            with: [
                'token' => $this->token,
                'email' => $this->email,
                'name' => $this->name,
                'url' => "http://localhost:5173/reset-password?".http_build_query([
                    'email' => $this->email, 'token' => $this->token
                    ])
            ],
        );
    }
}
