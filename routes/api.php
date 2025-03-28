<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\mainController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\MentorController;
use App\Http\Middleware\checkRole;

Route::post('/learner/register', [AuthController::class, 'learner_register']);
Route::post('/mentor/register', [AuthController::class, 'mentor_register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // learner functions
    Route::get('/learner/users', [LearnerController::class, 'retAllMent'])
    ->middleware(checkRole::class.':learner');

    Route::get('/learner/users/{id}', [LearnerController::class, 'retOneMent'])
    ->middleware(checkRole::class.':learner');

    Route::patch('/learner/edit', [LearnerController::class, 'editInfo'])
    ->middleware(checkRole::class.':learner');

    Route::delete('/learner/delete', [LearnerController::class, 'delAcc'])
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

    // general admin functions
    
    // get all users
    Route::get('/admin', [mainController::class, 'retAll'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/mentors', [mainController::class, 'retAllMent'])
    ->middleware(checkRole::class.':admin');

    Route::get('/admin/learners', [mainController::class, 'retAllLear'])
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

    //General protected routes
    Route::post('/logout', [AuthController::class, 'logout']);
});


