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
}
