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
use App\Http\Controllers\GdriveController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailVerification;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    //register
    public function learner_register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'image' => 'file|required|mimes:jpg,jpeg,png|max:2048',
            'course' => 'required|string|max:255',
            'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'subjects' => 'required|string',
            // 'subjects.*' => 'string|max:255',
            'learn_modality' => 'required|string|in:Online,In-person,Hybrid',
            'learn_sty' => 'required|string|max:255',
            'availability' => 'required|string',
            // 'availability.*' => 'string|max:255',
            'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',
            'bio' => 'required|string|max:1000',
            'goals' => 'required|string|max:1000',
        ]);


        DB::beginTransaction();
        try {
            $verifToken = Str::random(64);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'learner',
                'email_verification_token' => $verifToken,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $Gdrive = new GdriveController();
            $imageID = $Gdrive->imageUp($request); 
            $this->sendVerifEmail($user);

            // event(new registered($user));

            $learner = Learner::create([
                'learn_inf_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phoneNum' => $request->phoneNum,
                'address' => $request->address,
                'image' => $imageID,
                'course' => $request->course,
                'year' => $request->year,
                'subjects' => $request->subjects,
                'learn_modality' => $request->learn_modality,
                'learn_sty' => $request->learn_sty,
                'availability' => $request->availability,
                'prefSessDur' => $request->prefSessDur,
                'bio' => $request->bio,
                'goals' => $request->goals,
            ]);

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
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'image' => 'file|required|mimes:jpg,jpeg,png|max:2048',
            'course' => 'required|string|max:255',
            'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'subjects' => 'required|string',
            // 'subjects.*' => 'string|max:255',
            'learn_modality' => 'required|string|in:Online,In-person,Hybrid',
            'teach_sty' => 'required|string|max:255',
            'availability' => 'required|string',
            // 'availability.*' => 'string|max:255',
            'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',
            'proficiency' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'exp' => 'required|string|max:1000',
            'credentials' => 'required|array|min:1',
            'credentials.*' => 'file|mimes:jpg,jpeg,png,docx,pdf|max:25600',
        ]);


        DB::beginTransaction();
        try {
            $verifToken = Str::random(64);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mentor',
                'email_verification_token' => $verifToken,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $Gdrive = new GdriveController();
            $imageID = $Gdrive->imageUp($request); 
            $credID = $Gdrive->storeCreds($request);
            $this->sendVerifEmail($user);

            // event(new registered($user));

            $mentor = Mentor::create([
                'ment_inf_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phoneNum' => $request->phoneNum,
                'address' => $request->address,
                'image' => $imageID,
                'course' => $request->course,
                'year' => $request->year,
                'subjects' => $request->subjects,
                'proficiency' => $request->proficiency,
                'learn_modality' => $request->learn_modality,
                'teach_sty' => $request->teach_sty,
                'availability' => $request->availability,
                'prefSessDur' => $request->prefSessDur,
                'bio' => $request->bio,
                'exp' => $request->exp,
                'credentials' => $credID,
            ]);

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

    public function createAdmin(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            $this->sendVerifEmail($user);

            DB::commit();

            return response()->json([
                'user' => $user,
                'token' => $token,
                'user_role' => $user->role,
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

        $AuthCookie = cookie(
            'Authorization',
            $token,
            14400, // 1 day in minutes
            '/',
            null,
            false,
            true,
            false,
            'None'
        );
        
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user_role' => $user->role
        ])->cookie($AuthCookie);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        cookie()->forget('Authorization');

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function sendVerifEmail($user){

        $link = url('api/verify-email/'.$user->id.'/'.$user->email_verification_token);

        Mail::to($user->email)->send(new emailVerification($user->id, $link));
    }
}
