<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Mentor;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function setSched(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|integer|exists:mentor_info,mentor_no',
            'date' => 'required|date|after:today',
            'time' => 'required|date_format:H:i',
        ]);

        $user = Auth::user();

        $sched = Schedule::create([
            'creator_id' => $user->id,
            'participant_id' => $request->participant_id,
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'time' => $request->time,
        ]);

        return response()->json([
            'message' => 'Schedule created successfully',
            'schedule' => $sched,
        ], 201);
    }
}
