<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\learner;
use App\Models\mentor;
use App\Models\schedule;
use App\Models\User;

class LearnerSessionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sessionDeets;

    /**
     * Create a new message instance.
     * 
     * @param array  $sessionDeets
     * 
     */
    public function __construct(array $sessionDeets)
    {
        $this->sessionDeets = $sessionDeets;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $creatorId = $this->sessionDeets['creator_id']; //learner user_id
        $participantId = $this->sessionDeets['participant_id']; //menter ment_info_no -> mentor user_id     
        $learnerEmail = User::where('id', $creatorId)->first()->email;

        return new Envelope(
            subject: 'Session Reminder',
            from: 'gccoed@gmail.com',
            to: $learnerEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $mentorName = Mentor::where('mentor_no', $this->sessionDeets['participant_id'])->first()->name;//User::where('user_id', $participantId)->first()->name;
        $learnerName = Learner::where('learn_inf_id', $this->sessionDeets['creator_id'])->first()->name;
        $date = $this->sessionDeets['date'];
        $time = $this->sessionDeets['time']; 

        return new Content(
            markdown: 'emails.learnerSessRem',
            with: [
                'learnerName' => $learnerName,
                'mentorName' => $mentorName,
                'date' => $date,
                'time' => $time,
            ],
            view: 'emails.LearnerSessRem',
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
