<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\schedule;
use App\Models\mentor;
use App\Models\User;
use App\Models\learner;
use Illuminate\Support\Facades\Auth;

class reSched extends Mailable
{
    use Queueable, SerializesModels;

    public $schedId;
    public $oldData;

    /**
     * Create a new message instance.
     * @param int $schedId
     * @param array $oldData
     */
    public function __construct(int $schedId, array $oldData)
    {
        $this->schedId = $schedId;
        $this->oldData = $oldData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $sched = Schedule::where('id', $this->schedId)->first();
        $mentorInf = Mentor::where('mentor_no', $sched->participant_id)->first();
        $learner = Learner::where('learn_inf_id', $sched->creator_id)->first();
        
        $loggedInUser = Auth::user();

        if ($loggedInUser->id == $sched->creator_id) {
            $userEmail = $mentorInf->email;
        } else {
            $userEmail = $learner->email;
        }

        return new Envelope(
            subject: 'Change of Schedule',
            from: 'gccoed@gmail.com',
            to: $userEmail,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $sched = Schedule::where('id', $this->schedId)->first();

        $loggedInUser = Auth::user();

        return new Content(
            markdown: 'emails.reched',
            with: [
                "olddate" => $this->oldData['date'],
                "oldtime" => $this->oldData['time'],
                // "oldlocation" => $this->oldData['location'],
                "date" => $sched->date,
                "time" => $sched->time,
                // "location" => $sched->location,
                "reschedulee" => $loggedInUser->name,
            ],
            view: 'emails.reched',
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
