<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Mentor;
use App\Models\Learner;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\RemindSched;
use App\Mail\cancelSched;
use App\Mail\reSched;
use Illuminate\Support\Facades\Mail;
use App\Mail\TutoringSessionOffer;

class ScheduleController extends Controller
{
    public function setSched(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|integer|exists:mentor_infos,mentor_no',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();

        $sched = Schedule::create([
            'creator_id' => $user->id,
            'participant_id' => $request->participant_id,
            'subject' => $request->subject,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'time' => $request->time,
            'location' => $request->location,
        ]);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $sched,
        ], 201);
    }

    // public function getSchedLearner(Request $request)
    // {
    //     $user = Auth::user();

    //     // Get today's date
    //     $today = Carbon::today();

    //     // Fetch schedules created by the user
    //     $schedulesToday = Schedule::where('creator_id', $user->id)
    //         ->whereDate('date', $today)
    //         ->with(['mentor' => function ($query) {
    //             $query->select('mentor_no', 'name');
    //         }])
    //         ->get();

    //     $upcomingSchedules = Schedule::where('creator_id', $user->id)
    //         ->whereDate('date', '>', $today)
    //         ->with(['mentor' => function ($query) {
    //             $query->select('mentor_no', 'name');
    //         }])
    //         ->get();

    //     return response()->json([
    //         'schedules_today' => $schedulesToday,
    //         'upcoming_schedules' => $upcomingSchedules,
    //     ], 200);
    // }
    public function getSchedLearner(Request $request)
    {
        $user = Auth::user();

        $learner = Learner::where('learn_inf_id', $user->id)->first();
        if (!$learner) {
            return response()->json(['error' => 'Learner not found.'], 404);
        }

        $learnerId = $learner->learn_inf_id;
        $today = Carbon::today();

        $schedulesToday = Schedule::where('creator_id', $learnerId)
            ->whereDate('date', $today)
            ->with(['mentor.user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();

        $upcomingSchedules = Schedule::where('creator_id', $learnerId)
            ->whereDate('date', '>', $today)
            ->with(['mentor.user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();

        return response()->json([
            'schedules_today' => $schedulesToday,
            'upcoming_schedules' => $upcomingSchedules,
        ]);
    }

    // public function getSchedMentor(Request $request)
    // {
    //     $user = Auth::user();

    //     $mentId = Mentor::where('ment_inf_id', $user->id)->first()->mentor_no;

    //     // Get today's date
    //     $today = Carbon::today();

    //     // Fetch schedules where the user is the participant (mentor)
    //     $schedulesToday = Schedule::where('participant_id', $mentId)
    //         ->whereDate('date', $today)
    //         ->with(['learner' => function ($query) {
    //             $query->select('learn_inf_id', 'name');
    //         }])
    //         ->get();

    //     $upcomingSchedules = Schedule::where('participant_id', $mentId)
    //         ->whereDate('date', '>', $today)
    //         ->with(['learner' => function ($query) {
    //             $query->select('learn_inf_id', 'name');
    //         }])
    //         ->get();

    //     return response()->json([
    //         'schedules_today' => $schedulesToday,
    //         'upcoming_schedules' => $upcomingSchedules,
    //     ], 200);
    // }
    public function getSchedMentor(Request $request)
    {
        $user = Auth::user();

        $mentor = Mentor::where('ment_inf_id', $user->id)->first();
        if (!$mentor) {
            return response()->json(['error' => 'Mentor not found.'], 404);
        }

        $mentId = $mentor->mentor_no;
        $today = Carbon::today();

        $schedulesToday = Schedule::where('participant_id', $mentId)
            ->whereDate('date', $today)
            ->with(['learner.user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();

        $upcomingSchedules = Schedule::where('participant_id', $mentId)
            ->whereDate('date', '>', $today)
            ->with(['learner.user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->get();

        return response()->json([
            'schedules_today' => $schedulesToday,
            'upcoming_schedules' => $upcomingSchedules,
        ]);
    }


    public function sendRem(int $schedid){
        $sched = Schedule::where('id', $schedid)->first();
        $mentorInf = Mentor::where('mentor_no', $sched->participant_id)->first();
        $learner = Learner::where('learn_inf_id', $sched->creator_id)->first();

        $loggedInUser = Auth::user();

        $userEmail = null;

        if($loggedInUser->role == 'learner'){
            $userEmail = User::where('id', $mentorInf->ment_inf_id)->first()->email;
        } 

        if($loggedInUser->role == 'mentor'){
            $userEmail = User::where('id', $learner->learn_inf_id)->first()->email;
        }

        if (!$userEmail) {
            return response()->json(['message' => 'User email not found'], 404);
        }

        // Send email logic here
        Mail::to($userEmail)->send(new RemindSched($schedid));

        return response()->json(['message' => 'Email sent successfully'], 200);
    }

    public function cancelSched(int $schedid){
        $sched = Schedule::where('id', $schedid)->first();
        $mentorInf = Mentor::where('mentor_no', $sched->participant_id)->first();
        $learner = Learner::where('learn_inf_id', $sched->creator_id)->first();

        $loggedInUser = Auth::user();

        $userEmail = null;

        if($loggedInUser->role == 'learner'){
            $userEmail = User::where('id', $mentorInf->ment_inf_id)->first()->email;
        } 

        if($loggedInUser->role == 'mentor'){
            $userEmail = User::where('id', $learner->learn_inf_id)->first()->email;
        }

        if (!$userEmail) {
            return response()->json(['message' => 'User email not found'], 404);
        }

        // Send email logic here
        Mail::to($userEmail)->send(new cancelSched($schedid));

        // Delete the schedule
        $sched->delete();

        return response()->json(['message' => 'Email sent successfully'], 200);

    }

    public function editSched(Request $request, int $schedid)
    {
        $request->validate([
            'date' => 'nullable|date|after:today',
            'time' => 'nullable|date_format:H:i',
            // 'location' => 'nullable|string|max:255',
        ]);

        $sched = Schedule::where('id', $schedid)->first();

        if (!$sched) {
            return response()->json(['message' => 'Schedule not found'], 404);
        }

        // Store old data for email content
        $oldData = [
            'date' => $sched->date,
            'time' => $sched->time,
            // 'location' => $sched->location,
        ];

        // Update only the fields provided in the request
        if ($request->has('date') && $request->date !== null) {
            $sched->date = Carbon::parse($request->date)->format('Y-m-d');
        }
        if ($request->has('time') && $request->time !== null) {
            $sched->time = $request->time;
        }
        // if ($request->has('location') && $request->location !== null) {
        //     $sched->location = $request->location;
        // }

        $sched->save();

        $mentorInf = Mentor::where('mentor_no', $sched->participant_id)->first();
        $learner = Learner::where('learn_inf_id', $sched->creator_id)->first();

        $loggedInUser = Auth::user();

        $userEmail = null;

        if ($loggedInUser->role == 'learner') {
            $userEmail = User::where('id', $mentorInf->ment_inf_id)->first()->email;
        } 

        if ($loggedInUser->role == 'mentor') {
            $userEmail = User::where('id', $learner->learn_inf_id)->first()->email;
        }

        if (!$userEmail) {
            return response()->json(['message' => 'User email not found'], 404);
        }

        // Send email with old and new data
        Mail::to($userEmail)->send(new reSched($schedid, $oldData));

        return response()->json(['message' => 'Schedule updated and email sent successfully'], 200);
    }

    public function getSchedLear4RevU()
    {
        $user = Auth::user();

        $learner = Learner::where('learn_inf_id', $user->id)->first();
        if (!$learner) {
            return response()->json(['error' => 'Learner not found.'], 404);
        }

        $learnerId = $learner->learn_inf_id;
        $today = Carbon::today();

        $schedulesDone = Schedule::where('creator_id', $learnerId)
            ->whereDate('date', '<', $today)
            ->with(['mentor' => function ($query) {
                $query->select('mentor_no', 'ment_inf_id', 'course', 'year', 'image')
                ->with(['user' => function ($query) {
                    $query->select('id', 'name');
                }]);
            }])
            ->get()
            ->map(function ($schedule) use ($user) {
                // Check for feedback and get it if it exists
                $feedback = \App\Models\user_feedback::where('schedule_id', $schedule->id)
                    ->where('reviewer_id', $user->id)
                    ->where('reviewee_id', $schedule->participant_id)
                    ->first();
                
                $schedule->has_feedback = !is_null($feedback);
                
                // If feedback exists, add it to the schedule object
                if ($schedule->has_feedback) {
                    $schedule->feedback = [
                        'rating' => $feedback->rating,
                        'feedback' => $feedback->feedback,
                        'created_at' => $feedback->created_at
                    ];
                }
                
                return $schedule;
            });

        return response()->json([
            'schedules_done' => $schedulesDone,
        ]);
    }

    public function acceptSchedule(Request $request, $token)
    {
        // Validate the signature
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired link'], 401);
        }

        try {
            // Validate request parameters
            $validatedData = $request->validate([
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'modality' => 'required|string|in:online,in-person',
                'subject' => 'required|string|max:255',
                'location' => 'nullable|string',
                'mentor_id' => 'required|integer|exists:mentor_infos,mentor_no',
                'learner_id' => 'required|integer|exists:learner_info,learn_inf_id',
            ]);

            // Set location based on modality
            $location = $validatedData['modality'] === 'online' 
                ? 'home' 
                : $validatedData['location'];

            // Create the schedule
            $schedule = Schedule::create([
                'creator_id' => $validatedData['learner_id'],
                'participant_id' => $validatedData['mentor_id'],
                'subject' => $validatedData['subject'],
                'date' => Carbon::parse($validatedData['date'])->format('Y-m-d'),
                'time' => $validatedData['time'],
                'location' => $location,
                'status' => 'confirmed'
            ]);

            return response()->json([
                'message' => 'Schedule accepted and created successfully',
                'schedule' => $schedule,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendOffer(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'learner_id' => 'required|exists:learner_info,learn_inf_id',
                'subject' => 'required|string|max:255',
                'date' => 'required|date|after:today',
                'time' => 'required|date_format:H:i',
                'modality' => 'required|in:online,in-person',
                'location' => 'required_if:modality,in-person'
            ]);

            // Get authenticated user (mentor)
            $user = Auth::user();
            $mentor = Mentor::where('ment_inf_id', $user->id)->first();

            if (!$mentor) {
                return response()->json(['message' => 'Mentor not found'], 404);
            }

            // Get learner information
            $learner = Learner::with('user')->where('learn_inf_id', $validatedData['learner_id'])->first();

            if (!$learner || !$learner->user) {
                return response()->json(['message' => 'Learner not found'], 404);
            }

            // Generate a unique token for the offer
            $token = hash('sha256', time() . $learner->learn_inf_id . $mentor->mentor_no);

            // Send the email offer
            Mail::to($learner->user->email)->send(new TutoringSessionOffer([
                'token' => $token,
                'learnerName' => $learner->user->name,
                'mentorName' => $user->name,
                'subject' => $validatedData['subject'],
                'date' => $validatedData['date'],
                'time' => $validatedData['time'],
                'modality' => $validatedData['modality'],
                'location' => $validatedData['modality'] === 'online' ? 'home' : $validatedData['location'],
                'mentorId' => $mentor->mentor_no,
                'learnerId' => $learner->learn_inf_id
            ]));

            return response()->json([
                'message' => 'Tutoring session offer sent successfully',
                'details' => [
                    'learner' => $learner->user->name,
                    'date' => $validatedData['date'],
                    'time' => $validatedData['time'],
                    'modality' => $validatedData['modality']
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending tutoring offer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
