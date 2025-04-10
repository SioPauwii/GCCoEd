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

    public function editInfo(Request $request)
    {
        $authUser = Auth::user();

        $learner = Mentor::where('ment_inf_id', $authUser->id)->first();

        if (!$learner) {
            return response()->json(['message' => 'Mentor record not found'], 404);
        }

        $request->validate([
            'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'city_muni' => 'required|string|max:255',
            'brgy' => 'required|string|max:255',
            'image' => 'nullable|string|url',
            'course' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'year' => 'required|string|in:1st,2nd,3rd,4th',
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'string|max:255',
            'proficiency' => 'required|string|max:255',
            'learn_modality' => 'required|string|in:online,face-to-face,hybrid',
            'teach_sty' => 'required|string|max:255',
            'availability' => 'required|array|min:1',
            'availability.*' => 'string|max:255',
            'prefSessDur' => 'required|string|in:3hrs,1hr,2hrs',
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
}
