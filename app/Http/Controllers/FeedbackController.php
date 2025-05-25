<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user_feedback as Feedback;
use App\Models\Mentor;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;



class FeedbackController extends Controller
{
    public function setFeedback(Request $request, $schedule_id) 
    {
        $user = Auth::user();
        
        // Get the schedule and check if it exists
        $schedule = Schedule::where('id', $schedule_id)
            // ->whereDate('date', '<', now()) // Only allow feedback for past sessions
            ->first();
        
        if (!$schedule) {
            return response()->json([
                "message" => "Schedule not found or session hasn't occurred yet",
            ], 404);
        }

        // Check if feedback already exists
        $existingFeedback = Feedback::where('schedule_id', $schedule_id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($existingFeedback) {
            return response()->json([
                "message" => "Feedback already submitted for this session",
            ], 400);
        }
        
        $request->validate([
            "feedback" => "string|max:1000",
            "rating" => "required|integer|between:1,5",
        ]);

        try {
            // Begin transaction
            DB::beginTransaction();

            // Create feedback using participant_id from schedule as reviewee_id
            Feedback::create([
                "schedule_id" => $schedule_id,
                "reviewer_id" => $user->id,
                "reviewee_id" => $schedule->participant_id,
                "feedback" => $request->feedback,
                "rating" => $request->rating
            ]);

            // Update mentor's rating average immediately
            $avgRating = Feedback::where('reviewee_id', $schedule->participant_id)
                ->avg('rating');
            
            $mentor = Mentor::where('mentor_no', $schedule->participant_id)->first();
            $mentor->rating_ave = round($avgRating, 1);
            $mentor->save();

            // Commit transaction
            DB::commit();

            return response()->json([
                "message" => "Feedback submitted and rating updated successfully",
            ], 201);

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return response()->json([
                "message" => "Error submitting feedback",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function getFeedback() {
        $user = Auth::user();
        $mentor = Mentor::where("ment_inf_id", $user->id)->with('user')->first();

        // Fetch feedbacks with reviewer's (learner's) information
        $feedbacks = Feedback::where("reviewee_id", $mentor->mentor_no)
            ->with([
                'reviewer.user', // Load all user fields
                "reviewer" => function ($query) {
                    $query->select("learn_inf_id", "course", "year", "image");
                }
            ])
            ->select('id', 'reviewer_id', 'feedback', 'rating', 'created_at') // Specify which feedback fields you want
            ->get();

        // Transform the data to include reviewer name directly
        $formattedFeedbacks = $feedbacks->map(function ($feedback) {
            return [
                'id' => $feedback->id,
                'feedback' => $feedback->feedback,
                'rating' => $feedback->rating,
                'created_at' => $feedback->created_at,
                'reviewer' => [
                    'name' => $feedback->reviewer->user->name,
                    'course' => $feedback->reviewer->course,
                    'year' => $feedback->reviewer->year,
                    'image' => $feedback->reviewer->image,
                ]
            ];
        });

        return response()->json([
            "feedbacks" => $formattedFeedbacks,
        ], 200);
    }
}
