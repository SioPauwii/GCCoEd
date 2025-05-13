<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user_feedback as Feedback;
use App\Models\Mentor;

class FeedbackController extends Controller
{
    public function setFeedback(Request $request, $id) {
        $user = Auth::user();
        $mentor = Mentor::where("mentor_no", $id)->first();
        if (!$mentor) {
            return response()->json([
                "message" => "Mentor not found",
            ], 404);
        }
        $request->validate([
            "feedback" => "string|max:1000",
            "rating" => "required|integer|between:1,5",
        ]);

        Feedback::create([
            "reviewer_id" => $user->id,
            "reviewee_id" => $mentor->mentor_no,
            "feedback" => $request->comment,
            "rating" => $request->rating
        ]);

        return response()->json([
            "message" => "Feedback submitted successfully",
        ], 201);
    }

    public function getFeedback() {
        $user = Auth::user();
        $mentor = Mentor::where("ment_inf_id", $user->id)->first();
    
        // Fetch feedbacks with reviewer's (learner's) information
        $feedbacks = Feedback::where("reviewee_id", $mentor->mentor_no)
            ->with([
                "reviewer" => function ($query) {
                    $query->select("learn_inf_id", "name", "course", "year", "image"); // Include desired fields
                }
            ])
            ->get();
    
        return response()->json([
            "feedbacks" => $feedbacks,
        ], 200);
    }
}
