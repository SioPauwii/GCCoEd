<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\mainController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\GdriveController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MessageController;
use App\Http\Middleware\checkRole;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Mentor;
use App\Models\Learner;
use App\Http\Controllers\emailNotifController;
use App\Http\Controllers\FeedbackController;
use App\Http\Middleware\AuthCookieHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

Route::get('/user/role', [AuthController::class, 'getUserRole']);

Route::get('/auth/check', [AuthController::class, 'authCheck']);

Route::post('/learner/register', [AuthController::class, 'learner_register']);

Route::post('/mentor/register', [AuthController::class, 'mentor_register']);

Route::post('/learner/register/2nd', [AuthController::class, 'secondary_learner_register']);

Route::post('/mentor/register/2nd', [AuthController::class, 'secondary_mentor_register']);

Route::post('/admin/register', [AuthController::class, 'createAdmin']);

Route::post('/login', [AuthController::class, 'login']);

// Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::post('/APILogin', [AuthController::class, 'apiLogin']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::patch('/reset-password', [AuthController::class, 'resetPassword']);



Route::middleware(['auth:sanctum'])->group(function () {

    //image loader
    Route::get('/image/{id}', [GdriveController::class, 'streamImg']);

    // learner functions

    Route::get('/learner/details', [LearnerController::class, 'GetLearDeets'])
    ->middleware(checkRole::class.':learner');

    Route::get('/learner/users', [LearnerController::class, 'retAllMent'])
    ->middleware(checkRole::class.':learner');

    // Route::get('/learner/schedule', [ScheduleController::class, 'getSchedLearner'])
    // ->middleware(checkRole::class.':learner');

    Route::get('/learner/users/{id}', [LearnerController::class, 'retOneMent'])
    ->middleware(checkRole::class.':learner');

    Route::patch('/learner/edit', [LearnerController::class, 'editInfo'])
    ->middleware(checkRole::class.':learner');

    Route::get('/learner/mentor/files/{id}', [GdriveController::class, 'show'])
    ->middleware(checkRole::class.':learner');

    Route::delete('/learner/delete', [LearnerController::class, 'delAcc'])
    ->middleware(checkRole::class.':learner');

    Route::post('/learner/scheduleCreate', [ScheduleController::class, 'setSched'])
    ->middleware(checkRole::class.':learner');
    
    Route::get('/learner/sched', [ScheduleController::class, 'getSchedLearner'])
    ->middleware(checkRole::class.':learner');

    Route::post('/learner/feedback/{id}', [FeedbackController::class, 'setFeedback'])
    ->middleware(checkRole::class.':learner');

    Route::get('/learner/doneSched', [ScheduleController::class, 'getSchedLear4RevU'])
    ->middleware(checkRole::class.':learner');

    Route::get('/learner/mentFiles', [GdriveController::class, 'getMentorsFiles'])
    ->middleware(checkRole::class.':learner');

    // mentor functions
    Route::get('/mentor/details', [MentorController::class, 'GetMentDeets'])
    ->middleware(checkRole::class.':mentor');

    Route::get('/mentor/users', [MentorController::class, 'retAllLear'])
    ->middleware(checkRole::class.':mentor');

    Route::get('/mentor/users/{id}', [MentorController::class, 'retOneLear'])
    ->middleware(checkRole::class.':mentor');

    Route::patch('/mentor/edit', [MentorController::class, 'editInfo'])
    ->middleware(checkRole::class.':mentor');

    Route::delete('/mentor/delete', [MentorController::class, 'delAcc'])
    ->middleware(checkRole::class.':mentor');

    Route::post('/mentor/file/upload', [GdriveController::class, 'store'])
    ->middleware(checkRole::class.':mentor');

    Route::get('/mentor/files', [GdriveController::class, 'getMentorFiles'])
    ->middleware(checkRole::class.':mentor');

    Route::delete('/mentor/file/delete/{id}', [GdriveController::class, 'delete'])
    ->middleware(checkRole::class.':mentor');

    Route::get('/mentor/schedule', [ScheduleController::class, 'getSchedMentor'])
    ->middleware(checkRole::class.':mentor');

    Route::get('/mentor/getFeedback', [FeedbackController::class, 'getFeedback'])
    ->middleware(checkRole::class.':mentor');

    Route::post('/mentor/send-offer', [ScheduleController::class, 'sendOffer'])
    ->middleware(checkRole::class.':mentor');


    // general admin functions
    
    // get all users
    Route::get('/admin', [mainController::class, 'retAll'])
    ->middleware(checkRole::class.':admin');

    // Route::get('/admin/mentor', [mainController::class, 'retAllMent'])
    // ->middleware(checkRole::class.':admin');

    // Route::get('/admin/learner', [mainController::class, 'retAllLear'])
    // ->middleware(checkRole::class.':admin');

    Route::get('/admin/applicants', [mainController::class, 'retForApproval'])
    ->middleware(checkRole::class.':admin');

    // get one specific user
    Route::get('/admin/{id}', [mainController::class, 'retOne'])
    ->middleware(checkRole::class.':admin');

    // Route::get('/admin/mentor/{id}', [mainController::class, 'retOneMent'])
    // ->middleware(checkRole::class.':admin');

    // Route::get('/admin/learner/{id}', [mainController::class, 'retOneLear'])
    // ->middleware(checkRole::class.':admin');

    Route::delete('/admin/delete/{id}', [mainController::class, 'delAcc'])
    ->middleware(checkRole::class.':admin');

    Route::patch('/admin/mentor/approve/{id}', [mainController::class, 'approveAcc'])
    ->middleware(checkRole::class.':admin');

    Route::patch('/admin/mentor/reject/{id}', [mainController::class, 'rejectAcc'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/cred/{mentId}', [GdriveController::class, 'getMentorCreds'])
    ->middleware(checkRole::class.':admin');

    // Route::patch('/admin/applicants', [mainController::class, 'retAllApplicants'])
    // ->middleware(checkRole::class.':admin');

    //General protected routes
    Route::post('/logout/api', [AuthController::class, 'apiLogout']);

    Route::post('/logout/web', [AuthController::class, 'webLogout']);
   
    Route::post('/message/{receiver_id}', [MessageController::class, 'store'])
    ->middleware('auth:sanctum'); // Ensure the user is authenticated

    Route::get('/message/{receiver_id}', [MessageController::class, 'conversation'])
    ->middleware('auth:sanctum');

    Route::post('/send/session/reminder/{id}', [ScheduleController::class, 'sendRem'])
    ->middleware('auth:sanctum'); 

    Route::post('/send/session/cancel/{id}', [ScheduleController::class, 'cancelSched'])
    ->middleware('auth:sanctum');

    Route::patch('/resched/{id}', [ScheduleController::class, 'editSched'])
    ->middleware('auth:sanctum'); 

    Route::get('/preview/file/{id}', [GdriveController::class, 'previewFile'])
    ->middleware('auth:sanctum');   

    Route::get('/download/file/{id}', [GdriveController::class, 'downloadFile'])
    ->middleware('auth:sanctum');

    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::post('/set/2nd_role', [AuthController::class, 'setSecondaryRole']);

    Route::post("/switch", [AuthController::class, 'switchRole']);

});


Route::post('/imageUp', [GdriveController::class, 'imageUP']);
Route::post('/send/session/reminder', [emailNotifController::class, 'SessionReminder']);

Route::get('/verify-email/{id}/{token}', function ($id, $token) {
    $user = User::find($id);

    if(!$user || $user->email_verification_token != $token) {
        return response()->json(['message' => 'Invalid verification link.'], 400);
    }

    $user->email_verified_at = now();
    $user->email_verification_token = null;
    $user->save();

    return response()->json(['message' => 'Email verified.']);
});

// Route::post('/message/{receiver_id}', [MessageController::class, 'store'])
// ->middleware('auth:sanctum'); // Ensure the user is authenticated

// Route::get('/message/{receiver_id}', [MessageController::class, 'conversation'])
// ->middleware('auth:sanctum'); // Ensure the user is authenticated

Route::get('/schedule/accept/{token}', [ScheduleController::class, 'acceptSchedule'])
    ->name('api.schedule.accept')
    ->middleware('signed');