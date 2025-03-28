<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\learner;
use App\Models\mentor;


class MentorController extends Controller
{
    public function retAllLear(){
        $users = User::where('role', 'learner')->get();
        
        $learners = $users->map(function ($user) {
            $learnerInfo = Learner::where('learn_inf_id', $user->id)->first();
            return [
                'user' => $user,
                'learner_info' => $learnerInfo
            ];
        });

        return response()->json($learners);
    }

    public function retOneLear($id){
        $user_info = Learner::where('learner_no', $id)->first();

        if (!$user_info) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user = User::where('id', $user_info->learn_inf_id)->first();


        return response()->json(['user' => $user, 'user_info' => $user_info]);
    }

    public function editInfo(Request $request){

        $authUser = Auth::user();

        $learner = Mentor::where('ment_inf_id', $authUser->id)->first();

        if (!$learner) {
            return response()->json(['message' => 'Mentor record not found'], 404);
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
            'proficiency' => 'required|string',
            'learn_modality' => 'required|string',
            'learn_sty' => 'required|string',
            'availability' => 'required|array',
            'prefSessDur' => 'required|string',
            'bio' => 'required|string',
            'goals' => 'required|string',
        ]);
        
        $learner->update($validateData);

        return response()->json(['message' => 'Mentor information updated successfully']);
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
