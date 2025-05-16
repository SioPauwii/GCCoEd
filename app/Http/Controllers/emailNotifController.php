<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Learner;
use App\Models\Mentor;
use App\Mail\LearnerSessionReminderMail;
use App\Mail\MentorSessionReminderMail;
use Illuminate\Support\Facades\Mail;

class emailNotifController extends Controller
{
    /**
     * Send email reminders to all learners about sessions scheduled in 3 days or less.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function SessionReminder()
    {
        // Get the current date and the date 3 days from now
        $currentDate = now()->toDateString();
        $targetDate = now()->addDays(3)->toDateString();

        // Fetch all schedules between today and 3 days from now
        $schedules = Schedule::whereBetween('date', [$currentDate, $targetDate])->with(['learner.user:id,email', 'mentor.user:id,email'])->get();

        if ($schedules->isEmpty()) {
            return response()->json(['message' => 'No sessions scheduled in the next 3 days.'], 200);
        }

        foreach ($schedules as $schedule) {
            // Retrieve learner and mentor details
            $learner = optional($schedule->learner->user)->email;
            $mentor = optional($schedule->mentor->user)->email;

            if ($learner && $mentor) {
                // Prepare session details
                $sessionDeets = [
                    'creator_id' => $schedule->creator_id,
                    'participant_id' => $schedule->participant_id,
                    'date' => $schedule->date,
                    'time' => $schedule->time,
                ];

                // Send the email
                Mail::to($learner)->send(new LearnerSessionReminderMail($sessionDeets));
                Mail::to($mentor)->send(new MentorSessionReminderMail($sessionDeets));
            }
        }

        return response()->json(['message' => 'Session reminders sent successfully.'], 200);
    }
}
