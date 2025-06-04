<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Schedule;
use App\Models\Mentor;
use App\Models\User;
use App\Models\Learner;
use Illuminate\Support\Facades\Auth;

class remindSched extends Mailable
{
    use Queueable, SerializesModels;

    public $schedId;

    /**
     * Create a new message instance.
     * @param int $schedId
     */
    public function __construct(int $schedId)
    {
        $this->schedId = $schedId;
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

        if($loggedInUser->id == $sched->creator_id){
            $userEmail = $mentorInf->email;
        } else {
            $userEmail = $learner->email;
        }

        return new Envelope(
            subject: 'Schedule Reminder',
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
        return new Content(
            markdown: 'emails.SchedReminder',
            with: [
                'date' => $sched->date,
                'time' => $sched->time,
                'location' => $sched->location,
                'mentorName' => User::where('id', $sched->participant_id)->first()->name,
                'learnerName' => User::where('id', $sched->creator_id)->first()->name,
            ],
            view: 'emails.SchedReminder',
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
