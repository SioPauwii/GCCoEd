<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\learner;
use App\Models\mentor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountRejectedMail;

class mainController extends Controller
{
    public function retAll(){
        $users = User::all();
        return response()->json($users);
    }

    public function retOne($id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->role == 'mentor') {
            $info = Mentor::where('ment_inf_id', $user->id)->first();
            return response()->json(['user' => $user, 'info' => $info]);
        }

        if ($user->role == 'learner') {
            $info = Learner::where('learn_inf_id', $user->id)->first();
            return response()->json(['user' => $user, 'info' => $info]);
        }

        return response()->json($user);
    }

    public function retAllMent(){
        $user = User::where('role', 'mentor')->get();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function retAllLear(){
        $user = User::where('role', 'learner')->get();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function retOneMent($id){
        $user = User::where('role', 'mentor')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $info = Mentor::where('ment_inf_id', $user->id)->first();

        return response()->json(['user' => $user, 'info' => $info]);
    }

    public function retOneLear($id){
        $user = User::where('role', 'learner')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $info = Learner::where('learn_inf_id', $user->id)->first();

        return response()->json(['user' => $user, 'info' => $info]);
    }

    public function delAcc($id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function retForApproval(){       
        $users = Mentor::where('approved', '0')->get();
        // Check if users exist
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users waiting for approval'], 404);
        }

        return response()->json($users);
    }
    
    //approve account
    public function approveAcc($id){
        $mentor = Mentor::where('mentor_no', $id)->first();
        if (!$mentor) {
            return response()->json(['message' => 'Mentor not found'], 404);
        }
        $mentor->approved = 1;
        $mentor->save();
        Mail::to($mentor->email)->send(new AccountApprovedMail($mentor->mentor_no));
        return response()->json(['message' => 'Mentor approved successfully']);
    }

    //reject account
    public function rejectAcc($id){
        $mentor = Mentor::where('mentor_no', $id)->first();
        Mail::to($mentor->email)->send(new AccountRejectedMail($mentor->mentor_no));
        if (!$mentor) {
            return response()->json(['message' => 'Mentor not found'], 404);
        }
        $userId = $mentor->ment_inf_id;
        User::find($userId)->delete();
        return response()->json(['message' => 'Mentor rejected and account deleted successfully']);
    }
}
