<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\learner;
use App\Models\mentor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //register
    public function learner_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
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

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            $learner = Learner::create([
                'learn_inf_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phoneNum' => $request->phoneNum,
                'city_muni' => $request->city_muni,
                'brgy' => $request->brgy,
                'image' => $request->image,
                'course' => $request->course,
                'department' => $request->department,
                'year' => $request->year,
                'subjects' => json_encode($request->subjects),
                'learn_modality' => $request->learn_modality,
                'learn_sty' => $request->learn_sty,
                'availability' => json_encode($request->availability),
                'prefSessDur' => $request->prefSessDur,
                'bio' => $request->bio,
                'goals' => $request->goals,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return response()->json([
                'user' => $user,
                'learner' => $learner,
                'token' => $token,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function mentor_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
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
            'teach_sty' => 'required|string',
            'availability' => 'required|array',
            'prefSessDur' => 'required|string',
            'bio' => 'required|string',
            'exp' => 'required|string',

            
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            $mentor = Mentor::create([
                'ment_inf_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phoneNum' => $request->phoneNum,
                'city_muni' => $request->city_muni,
                'brgy' => $request->brgy,
                'image' => $request->image,
                'course' => $request->course,
                'department' => $request->department,
                'year' => $request->year,
                'subjects' => json_encode($request->subjects),
                'proficiency' => $request->proficiency,
                'learn_modality' => $request->learn_modality,
                'teach_sty' => $request->teach_sty,
                'availability' => json_encode($request->availability),
                'prefSessDur' => $request->prefSessDur,
                'bio' => $request->bio,
                'exp' => $request->exp,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();
            return response()->json([
                'user' => $user,
                'mentor' => $mentor,
                'token' => $token,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }


    //login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
