<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Mentor;
use App\Models\User;

class AccountRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mentorId;
    /**
     * Create a new message instance.
     *@param int $mentorId
     */
    public function __construct(int $mentorId)
    {
        $this->mentorId = $mentorId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $mentorEmail = Mentor::where('mentor_no', $this->mentorId)->first()->email;
        return new Envelope(
            subject: 'Account Rejected',
            from: 'gccoed@gmail.com',
            to: $mentorEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.AccRejected',
            view: 'emails.AccRejected',
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
