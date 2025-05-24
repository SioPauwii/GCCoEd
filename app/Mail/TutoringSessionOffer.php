<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TutoringSessionOffer extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $acceptUrl;

    public function __construct($data)
    {
        $this->data = $data;
        $this->acceptUrl = URL::signedRoute('api.schedule.accept', [
            'token' => $data['token'],
            'date' => $data['date'],
            'time' => $data['time'],
            'subject' => $data['subject'],
            'modality' => $data['modality'],
            'location' => $data['location'] ?? null,
            'mentor_id' => $data['mentorId'],
            'learner_id' => $data['learnerId']
        ], now()->addDays(7));
    }

    public function build()
    {
        return $this->view('emails.offer')
                    ->with([
                        'acceptUrl' => $this->acceptUrl,
                        'learnerName' => $this->data['learnerName'],
                        'mentorName' => $this->data['mentorName'],
                        'subject' => $this->data['subject'],
                        'date' => $this->data['date'],
                        'time' => $this->data['time'],
                        'modality' => $this->data['modality'],
                        'location' => $this->data['location'] ?? null,
                    ]);
    }
}