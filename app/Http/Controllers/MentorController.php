<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\learner;
use App\Models\mentor;
use App\Http\Controllers\GdriveController;


class MentorController extends Controller
{
    public function retAllLear(){
        $users = User::where('role', 'learner')->orWhere('secondary_role', 'learner')->get();
        
        $learners = $users->map(function ($user) {
            $learnerInfo = Learner::where('learn_inf_id', $user->id)->first();
            
            // Skip users without learner info
            if (!$learnerInfo) {
                return null;
            }

            return [
                'image_id' => $learnerInfo->image,
                'id' => $learnerInfo->learner_no,
                'userName' => $user->name,
                'yearLevel' => $learnerInfo->year,
                'course' => $learnerInfo->course,                
            ];
        })->filter(); // Remove null values from the collection

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

    public function editInfo(Request $request)
    {
        $authUser = Auth::user();

        $learner = Mentor::where('ment_inf_id', $authUser->id)->first();

        if (!$learner) {
            return response()->json(['message' => 'Mentor record not found'], 404);
        }

        $request->validate([
            'gender' => 'required|string|in:Male,Female,Non-binary,Other',
            'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'course' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',  // Changed year values
            // 'subjects' => 'required|array|min:1',
            'subjects' => 'required|string',
            'proficiency' => 'required|string|max:255',
            'learn_modality' => 'required|string|in:Online,In-person,Hybrid',  // Changed modality values
            // 'teach_sty' => 'required|array|min:1',
            'teach_sty' => 'required|string',
            // 'availability' => 'required|array|min:1',
            'availability' => 'required|string',
            'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',  // Changed duration values
            'bio' => 'required|string|max:1000',
            'exp' => 'required|string|max:1000',
        ]);
        
        $learner->update($request->all());

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

    public function GetMentDeets(){
        $user = Auth::user();
        $ment = Mentor::where('ment_inf_id', $user->id)->first();

        // $ment->image = 'https://drive.google.com/uc?export=view&id='.$ment->image;

        return response()->json(['user' => $user, 'ment' => $ment]);
    }
}
