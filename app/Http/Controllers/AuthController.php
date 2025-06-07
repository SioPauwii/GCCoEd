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
use Illuminate\Support\Facades\Mail;
use App\Mail\emailVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Mail\passReset;

class AuthController extends Controller
{
    //register
public function learner_register(Request $request)
{
    $validated = $request->validate([
        'gender' => 'required|string|in:Male,Female,Non-binary,Other',
        'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
        'address' => 'required|string|max:255',
        'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'course' => 'required|string|max:255',
        'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
        'subjects' => 'required|string',
        'learn_modality' => 'required|string|in:Online,In-person,Hybrid',
        'learn_sty' => 'required|string|max:255',
        'availability' => 'required|string',
        'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',
        'bio' => 'required|string|max:1000',
        'goals' => 'required|string|max:1000',
    ]);

    $user = Auth::user();

    $user_info = User::where('id', $user->id)->first();

    if (!$user) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    // if ($user->role !== null && $user->role !== 'learner') {
    //     return response()->json(['error' => 'User already has a different role.'], 403);
    // }

    if (Learner::where('learn_inf_id', $user->id)->exists()) {
        return response()->json(['error' => 'Learner profile already exists.'], 409);
    }

    DB::beginTransaction();

    try {
        // Update user role
        $user_info->update(['role' => 'learner']);

        // Handle file upload via your controller
        $Gdrive = new GdriveController();
        $imageID = $Gdrive->imageUp($request);

        // Save learner-specific data
        $learner = Learner::create([
            'learn_inf_id' => $user->id,
            'gender' => $validated['gender'],
            'phoneNum' => $validated['phoneNum'],
            'address' => $validated['address'],
            'image' => $imageID,
            'course' => $validated['course'],
            'year' => $validated['year'],
            'subjects' => $validated['subjects'],
            'learn_modality' => $validated['learn_modality'],
            'learn_sty' => $validated['learn_sty'],
            'availability' => $validated['availability'],
            'prefSessDur' => $validated['prefSessDur'],
            'bio' => $validated['bio'],
            'goals' => $validated['goals'],
        ]);

        DB::commit();

        return response()->json([
            'user' => $user,
            'learner' => $learner,
        ], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Registration failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function mentor_register(Request $request)
    {
        $request->validate([
            'role' => 'required|string|in:learner,mentor,admin', 
            'gender' => 'required|string|in:Male,Female,Non-binary,Other',
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


       $user = Auth::user();

        $user_info = User::where('id', $user->id)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // s

        if (Mentor::where('ment_inf_id', $user->id)->exists()) {
            return response()->json(['error' => 'Mentor profile already exists.'], 409);
        }

        DB::beginTransaction();

        try {
            // Update user role
            $user_info->update(['role' => 'mentor']);

            // Handle file upload via your controller
            $Gdrive = new GdriveController();
            $imageID = $Gdrive->imageUp($request);
            $credID = $Gdrive->storeCreds($request);
            // $this->sendVerifEmail($user);

            // event(new registered($user));

            $mentor = Mentor::create([
                'ment_inf_id' => $user->id,
                'gender' => $request->gender,
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
            'login' => 'required|string',
            'password' => 'required',
        ]);
    
        // Create throttle key based on the login input
        $throttleKey = Str::lower($request->input('login')) . '|' . $request->ip();
    
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'login' => ['Too many login attempts. Please try again in ' . RateLimiter::availableIn($throttleKey) . ' seconds.'],
            ]);
        }
    
        // Determine if input is email or ID
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'id';
        $user = User::where($field, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        RateLimiter::clear($throttleKey);
        
        // Check if user is a mentor and verify approval status
        if ($user->role === 'mentor') {
            $mentorInfo = Mentor::where('ment_inf_id', $user->id)->first();
            
            if (!$mentorInfo || $mentorInfo->approval_status !== 'approved') {
                return response()->json([
                    'error' => 'Mentor account not approved yet',
                    'status' => $mentorInfo ? $mentorInfo->approval_status : 'not_found',
                    'message' => 'Your mentor application is still under review. You will receive an email once approved.'
                ], 403);
            }
        }

        // Delete existing tokens for this user (optional - for single session)
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'user_role' => $user->role
        ]);
    }

    //logout
    public function logout(Request $request)
    {
        // Delete all tokens for the current user
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function sendVerifEmail($user){

        $link = url('api/verify-email/'.$user->id.'/'.$user->email_verification_token);

        Mail::to($user->email)->send(new emailVerification($user->id, $link));
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'pre_cred' => 'required|string', // either name, email, or id
            'email' => 'required|email',
            'id' => 'required|integer|max_digits:9|regex:/^\d+$/',
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'role' => 'required|string|in:learner,mentor',
            'secondary_role' => 'nullable|string|in:learner,mentor',
        ]);

        // First, verify the account exists with matching credentials
       $query = User::query()
            ->where(function ($q) use ($request) {
                $q->where('email', $request->pre_cred)
                ->orWhere('id', $request->pre_cred)
                ->orWhere('name', 'LIKE', '%' . $request->pre_cred . '%');
            })
            ->where('email', $request->email)
            ->where('id', $request->id)
            ->where('name', $request->name);

        // Handle role validation based on secondary_role presence
        if ($request->secondary_role) {
            // If secondary_role is provided, look for exact match of both roles
            $query->where(function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->where('role', $request->role)
                        ->where('secondary_role', $request->secondary_role);
                })->orWhere(function ($inner) use ($request) {
                    $inner->where('role', $request->secondary_role)
                        ->where('secondary_role', $request->role);
                });
            });
        } else {
            // If no secondary_role provided, look for primary role only
            $query->where('role', $request->role)
                ->whereNull('secondary_role');
        }

        $user = $query->first();

        if (!$user) {
            return response()->json([
                'error' => 'No account found matching all provided credentials'

            ], 404);
        }

        $email = $user->email;

        $token = Password::createToken($user);

    // Store token in password_reset_tokens table
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => Hash::make($token),
            'created_at' => now()
        ]
    );

    // Send password reset email
    try {
        Mail::to($user->email)->send(new passReset($token, $user->email, $user->name));

        return response()->json([
            'message' => 'Password reset link has been sent to your email',
            'email' => $user->email
        ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to send reset email',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully']);
        }

        return response()->json(['error' => 'Unable to reset password'], 400);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => 'Current password is incorrect'
            ], 401);
        }

        $token = Password::createToken($user);

    // Store token in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Send password reset email
        try {
            Mail::to($user->email)->send(new passReset($token, $user->email, $user->name));

            return response()->json([
                'message' => 'Password change link has been sent to your email',
                'email' => $user->email
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to send reset email',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function setSecondaryRole()
    {
        // $request->validate([
        //     'secondary_role' => 'required|string|in:learner,mentor',
        // ]);

        $user = Auth::user();

        $user_info = User::where('id', $user->id)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // Check if the user already has a secondary role
        if ($user->secondary_role !== null) {
            return response()->json(['error' => 'User already has a secondary role.'], 403);
        }

        if ($user->role !== null && $user->secondary_role !== null) {
            return response()->json(['error' => 'User already holds both roles.'], 403);
        }

        if ($user->role !== null && $user->role === 'learner') {
            $user_info->update(['secondary_role' => 'mentor']);
            return response()->json(['message' => 'Secondary role set to mentor successfully.']);
        }

        if ($user->role !== null && $user->role === 'mentor') {
            $user_info->update(['secondary_role' => 'learner']);
            return response()->json(['message' => 'Secondary role set to learner successfully.']);
        }

        // Update the user's secondary role
        // $user_info->update(['secondary_role' => $request->secondary_role]);

        return response()->json(['error' => 'Failed to set secondary role.'], 403);
    }

    public function switchRole()
    {
        $user = Auth::user();
        $user_info = User::where('id', $user->id)->first();

        // Check if user has both roles
        if (!$user->secondary_role) {
            return response()->json([
                'error' => 'You do not have a secondary role to switch to.'
            ], 403);
        }

        if ($user->role === 'learner'){
            $mentor_info = Mentor::where('ment_inf_id', $user->id)->first();
            if ($user->secondary_role === 'mentor' && $mentor_info->approval_status !== 'approved') {
                return response()->json([
                    'error' => 'You can only switch to mentor role after approval.'
                ], 403);
            }
        }

        // Swap the roles
        $temp = $user_info->role;
        $user_info->role = $user_info->secondary_role;
        $user_info->secondary_role = $temp;
        $user_info->save();

        // Delete current tokens and create new one
        $user_info->tokens()->delete();
        $token = $user_info->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Roles switched successfully.',
            'token' => $token,
            'new_primary_role' => $user_info->role
        ]);
    }

    public function secondary_learner_register(Request $request)
{
    $validated = $request->validate([
        'gender' => 'required|string|in:Male,Female,Non-binary,Other',
        'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
        'address' => 'required|string|max:255',
        'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        'course' => 'required|string|max:255',
        'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
        'subjects' => 'required|string',
        'learn_modality' => 'required|string|in:Online,In-person,Hybrid',
        'learn_sty' => 'required|string|max:255',
        'availability' => 'required|string',
        'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',
        'bio' => 'required|string|max:1000',
        'goals' => 'required|string|max:1000',
    ]);

    $user = Auth::user();

    $user_info = User::where('id', $user->id)->first();

    if (!$user) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    // if ($user->role !== null && $user->role !== 'learner') {
    //     return response()->json(['error' => 'User already has a different role.'], 403);
    // }

    if (Learner::where('learn_inf_id', $user->id)->exists()) {
        return response()->json(['error' => 'Learner profile already exists.'], 409);
    }

    DB::beginTransaction();

    try {
        // Update user role
        // $user_info->update(['role' => 'learner']);

        // Handle file upload via your controller
        $Gdrive = new GdriveController();
        $imageID = $Gdrive->imageUp($request);

        // Save learner-specific data
        $learner = Learner::create([
            'learn_inf_id' => $user->id,
            'gender' => $validated['gender'],
            'phoneNum' => $validated['phoneNum'],
            'address' => $validated['address'],
            'image' => $imageID,
            'course' => $validated['course'],
            'year' => $validated['year'],
            'subjects' => $validated['subjects'],
            'learn_modality' => $validated['learn_modality'],
            'learn_sty' => $validated['learn_sty'],
            'availability' => $validated['availability'],
            'prefSessDur' => $validated['prefSessDur'],
            'bio' => $validated['bio'],
            'goals' => $validated['goals'],
        ]);

        DB::commit();

        return response()->json([
            'user' => $user,
            'learner' => $learner,
        ], 201);
    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Registration failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function secondary_mentor_register(Request $request)
    {
        $request->validate([
            'gender' => 'required|string|in:Male,Female,Non-binary,Other',
            'phoneNum' => 'required|string|regex:/^\+?[0-9]{10,15}$/',
            'address' => 'required|string|max:255',
            'image' => 'file|required|mimes:jpg,jpeg,png|max:2048',
            'course' => 'required|string|max:255',
            'year' => 'required|string|in:1st Year,2nd Year,3rd Year,4th Year',
            'subjects' => 'required|string',
            'learn_modality' => 'required|string|in:Online,In-person,Hybrid',
            'teach_sty' => 'required|string|max:255',
            'availability' => 'required|string',
            'prefSessDur' => 'required|string|in:1 hour,2 hours,3 hours',
            'proficiency' => 'required|string|max:255',
            'bio' => 'required|string|max:1000',
            'exp' => 'required|string|max:1000',
            'credentials' => 'required|array|min:1',
            'credentials.*' => 'file|mimes:jpg,jpeg,png,docx,pdf|max:25600',
        ]);

        $user = Auth::user();
        $user_info = User::where('id', $user->id)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        if (Mentor::where('ment_inf_id', $user->id)->exists()) {
            return response()->json(['error' => 'Mentor profile already exists.'], 409);
        }

        DB::beginTransaction();

        try {
            $Gdrive = new GdriveController();
            $imageID = $Gdrive->imageUp($request);
            $credID = $Gdrive->storeCreds($request);

            $mentor = Mentor::create([
                'ment_inf_id' => $user->id,
                'gender' => $request->gender,
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
                'approval_status' => 'pending'
            ]);

            DB::commit();

            return response()->json([
                'mentor' => $mentor,
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function userRole(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'role' => $user->role,
            'secondary_role' => $user->secondary_role
        ]);
    }

    // public function authCheck(Request $request)
    // {
    //     $user = Auth::user(); // This will use the default guard (sanctum)

    //     if (!$user) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Unauthenticated.',
    //             'authenticated' => false
    //         ], 401);
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'User is authenticated',
    //         'authenticated' => true,
    //         'user' => [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'role' => $user->role,
    //             'secondary_role' => $user->secondary_role
    //         ]
    //     ]);
    // }
}
