<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user_feedback as Feedback;
use App\Models\Mentor;
use Illuminate\Support\Facades\DB;



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

        try {
            // Begin transaction
            DB::beginTransaction();

            // Create feedback
            Feedback::create([
                "reviewer_id" => $user->id,
                "reviewee_id" => $mentor->mentor_no,
                "feedback" => $request->feedback,
                "rating" => $request->rating
            ]);

            // Update mentor's rating average immediately
            $avgRating = Feedback::where('reviewee_id', $mentor->mentor_no)
                ->avg('rating');
            
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
                "reviewer.user:id,name",
                "reviewer" => function ($query) {
                    $query->select("learn_inf_id", "course", "year", "image"); // Include desired fields
                }
            ])
            ->get();
    
        return response()->json([
            "feedbacks" => $feedbacks,
        ], 200);
    }
}
