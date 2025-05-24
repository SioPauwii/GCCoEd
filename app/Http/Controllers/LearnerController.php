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
        $users = User::where(function($query) {
                $query->where('role', 'mentor')
                      ->orWhere('secondary_role', 'mentor');
            })
            ->whereHas('mentor', function($query) {
                $query->where('approval_status', 'approved');
            })
            ->get();
        
        $mentors = $users->map(function ($user) {
            $mentorInfo = Mentor::where('ment_inf_id', $user->id)
                               ->where('approval_status', 'approved')
                               ->first();
            return [
                'user' => $user,
                'mentor_infos' => $mentorInfo
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

    public function editInfo(Request $request)
    {
        $authUser = Auth::user();

        $learner = Learner::where('learn_inf_id', $authUser->id)->first();

        if (!$learner) {
            return response()->json(['message' => 'Learner record not found'], 404);
        }

        $request->validate([
            'gender' => 'nullable|string|in:Male,Female,Non-binary,Other,male,female,non-binary,other',
            'phoneNum' => 'nullable|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'year' => 'nullable|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'subjects' => 'nullable|string',
            'learn_modality' => 'nullable|string|in:Online,In-person,Hybrid',
            'learn_sty' => 'nullable|string',
            'availability' => 'nullable|string',
            'prefSessDur' => 'nullable|string|in:1 hour,2 hours,3 hours',
            'bio' => 'nullable|string|max:1000',
            'goals' => 'nullable|string|max:1000'
        ]);
        
        $learner->update($request->all());

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

    public function GetLearDeets(){
        $user = Auth::user();
        $learn = learner::where('learn_inf_id', $user->id)->first();

        // $ment->image = 'https://drive.google.com/uc?export=view&id='.$ment->image;

        return response()->json(['user' => $user, 'learn' => $learn]);
    }
}
