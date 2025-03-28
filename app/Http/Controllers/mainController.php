<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

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

        return response()->json($user);
    }

    public function retOneLear($id){
        $user = User::where('role', 'learner')->find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function delAcc($id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    //approve account


    //reject account
}
