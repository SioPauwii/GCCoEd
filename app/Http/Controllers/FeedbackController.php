<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user_feedback as Feedback;
use App\Models\Mentor;

class FeedbackController extends Controller
{
    public function setFeedback(Request $request) {
        $request->validate([
            "reviewee_id" => "required|integer|exists:mentor_info,mentor_no",
            "comment" => "string|max:1000",
            "rating" => "required|integer|between:1,5",
        ]);

        Feedback::create([
            "reviewer_id" => Auth::id(),
            "reviewee_id" => $request->reviewee_id,
            "comment" => $request->comment,
            "rating" => $request->rating
        ]);

        return response()->json([
            "message" => "Feedback submitted successfully",
        ], 201);
    }

}
