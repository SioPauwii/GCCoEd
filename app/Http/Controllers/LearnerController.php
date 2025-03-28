<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Mentor;
use App\Models\Learner;


class LearnerController extends Controller
{
    public function retAllMent(){
        $users = User::where('role', 'mentor')->get();
        
        $mentors = $users->map(function ($user) {
            $mentorInfo = Mentor::where('ment_inf_id', $user->id)->first();
            return [
                'user' => $user,
                'mentor_info' => $mentorInfo
            ];
        });

        return response()->json($mentors);
    }

    public function retOneMent($id){
        $user_info = Mentor::where('mentor_no', $id)->first();

        if (!$user_info) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user = User::where('id', $user_info->ment_inf_id)->first();


        return response()->json(['user' => $user, 'user_info' => $user_info]);
    }

    public function editInfo(Request $request){

        $authUser = Auth::user();

        $learner = Learner::where('learn_inf_id', $authUser->id)->first();

        if (!$learner) {
            return response()->json(['message' => 'Learner record not found'], 404);
        }

        $validateData = $request->validate([
            'phoneNum' => 'required|string',
            'city_muni' => 'required|string',
            'brgy' => 'required|string',
            'image' => 'string',
            'course' => 'required|string',
            'department' => 'required|string',
            'year' => 'required|string',
            'subjects' => 'required|array',
            'learn_modality' => 'required|string',
            'learn_sty' => 'required|string',
            'availability' => 'required|array',
            'prefSessDur' => 'required|string',
            'bio' => 'required|string',
            'goals' => 'required|string',
        ]);
        
        $learner->update($validateData);

        return response()->json(['message' => 'Learner information updated successfully']);
    }

    public function delAcc(){
        
        $authUser = Auth::user();

        if(!$authUser){
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($authUser->id);

        $user->delete();
            
        return response()->json(['message' => 'Account deleted successfully']);
    }
}
