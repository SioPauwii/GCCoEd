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
use App\Http\Middleware\checkRole;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Mentor;
use App\Models\Learner;
use App\Http\Controllers\emailNotifController;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

Route::post('/learner/register', [AuthController::class, 'learner_register']);

Route::post('/mentor/register', [AuthController::class, 'mentor_register']);

Route::post('/login', [AuthController::class, 'login']);

Route::get('/login', [AuthController::class, 'login'])->name('login');


Route::middleware(['auth:sanctum'])->group(function () {

    // learner functions
    Route::get('/learner/users', [LearnerController::class, 'retAllMent'])
    ->middleware(checkRole::class.':learner');

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

    // mentor functions
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

    Route::delete('/mentor/file/delete/{id}', [GdriveController::class, 'delete'])
    ->middleware(checkRole::class.':mentor');

    // general admin functions
    
    // get all users
    Route::get('/admin', [mainController::class, 'retAll'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/mentor', [mainController::class, 'retAllMent'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/learner', [mainController::class, 'retAllLear'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/mentor/approval', [mainController::class, 'retForApproval'])
    ->middleware(checkRole::class.':admin');

    // get one specific user
    Route::get('/admin/{id}', [mainController::class, 'retOne'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/mentor/{id}', [mainController::class, 'retOneMent'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/learner/{id}', [mainController::class, 'retOneLear'])
    ->middleware(checkRole::class.':admin');

    Route::delete('/admin/delete/{id}', [mainController::class, 'delAcc'])
    ->middleware(checkRole::class.':admin');

    Route::patch('/admin/mentor/approve/{id}', [mainController::class, 'approveAcc'])
    ->middleware(checkRole::class.':admin');

    Route::patch('/admin/mentor/reject/{id}', [mainController::class, 'rejectAcc'])
    ->middleware(checkRole::class.':admin');

    //General protected routes
    Route::post('/logout', [AuthController::class, 'logout']);
    
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