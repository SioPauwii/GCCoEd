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
    public function retAll()
    {
        // Get users with roles (excluding admins)
        $users = User::where(function($query) {
                $query->where(function($q) {
                    $q->where('role', '!=', 'admin')
                      ->whereNotNull('role');
                })->orWhere(function($q) {
                    $q->where('secondary_role', '!=', 'admin')
                      ->whereNotNull('secondary_role');
                });
            })
            ->select('id', 'name', 'email', 'role', 'secondary_role')
            ->with(['mentor:ment_inf_id,year,course,gender,image,phoneNum,address,learn_modality,teach_sty,availability,prefSessDur,bio,exp,subjects,proficiency', 'learner:learn_inf_id,year,course,gender,image,phoneNum,address,learn_modality,learn_sty,availability,prefSessDur,bio,goals,subjects'])
            ->get()
            ->map(function ($user) {
                $info = null;
                if ($user->role === 'mentor') {
                    $info = $user->mentor;
                } elseif ($user->role === 'learner') {
                    $info = $user->learner;
                }
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'secondary_role' => $user->secondary_role,
                    'year' => $info ? $info->year : null,
                    'course' => $info ? $info->course : null,
                    'phoneNum' => $info ? $info->phoneNum : null,
                    'address' => $info ? $info->address : null,
                    'image' => $info ? $info->image : null,
                    'gender' => $info ? $info->gender : null,
                    'learn_modality' => $info ? $info->learn_modality : null,
                    'teach_sty' => $info ? $info->teach_sty : null,
                    'learn_sty' => $info ? $info->learn_sty : null,
                    'availability' => $info ? $info->availability : null,
                    'prefSessDur' => $info ? $info->prefSessDur : null,
                    'bio' => $info ? $info->bio : null,
                    'exp' => $info ? $info->exp : null,
                    'goals' => $info ? $info->goals : null,           
                    'subjects' => $info ? $info->subjects : null,
                    'proficiency' => $info ? $info->proficiency : null,
                ];
            });
        
        // Count total learners (both primary and secondary roles)
        $totalLearners = User::where(function($query) {
                $query->where('role', 'learner')
                      ->orWhere('secondary_role', 'learner');
            })
            ->whereHas('learner')
            ->count();
        
        // Count total approved mentors (both primary and secondary roles)
        $totalApprovedMentors = User::where(function($query) {
                $query->where('role', 'mentor')
                      ->orWhere('secondary_role', 'mentor');
            })
            ->whereHas('mentor', function($query) {
                $query->where('approval_status', 'approved');
            })
            ->count();
        
        // Count total pending mentors (both primary and secondary roles)
        $totalPendingMentors = User::where(function($query) {
                $query->where('role', 'mentor')
                      ->orWhere('secondary_role', 'mentor');
            })
            ->whereHas('mentor', function($query) {
                $query->where('approval_status', 'pending');
            })
            ->count();

        // Calculate total users (excluding rejected mentors)
        $totalUsers = $totalLearners + $totalApprovedMentors + $totalPendingMentors;

        return response()->json([
            'users' => $users,
            'counts' => [
                'total_users' => $totalUsers,
                'learners' => $totalLearners,
                'approved_mentors' => $totalApprovedMentors,
                'pending_mentors' => $totalPendingMentors
            ]
        ]);
    }

    public function retOne($id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $response = ['user' => $user];

        // Check if user is a learner with mentor secondary role
        if ($user->role == 'learner' && $user->secondary_role == 'mentor') {
            $info = Mentor::where('ment_inf_id', $user->id)->first();
            $response['info'] = $info;
        }
        // If user is just a mentor
        elseif ($user->role == 'mentor') {
            $info = Mentor::where('ment_inf_id', $user->id)->first();
            $response['info'] = $info;
        }
        // If user is just a learner
        elseif ($user->role == 'learner') {
            $info = Learner::where('learn_inf_id', $user->id)->first();
            $response['info'] = $info;
        }

        return response()->json($response);
    }

    // public function retAllMent(){
    //     $user = User::where('role', 'mentor')->get();

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     return response()->json($user);
    // }

    // public function retAllLear(){
    //     $user = User::where('role', 'learner')->get();

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     return response()->json($user);
    // }

    // public function retOneMent($id){
    //     $user = User::where('role', 'mentor')->find($id);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $info = Mentor::where('ment_inf_id', $user->id)->first();

    //     return response()->json(['user' => $user, 'info' => $info]);
    // }

    // public function retOneLear($id){
    //     $user = User::where('role', 'learner')->find($id);

    //     if (!$user) {
    //         return response()->json(['message' => 'User not found'], 404);
    //     }

    //     $info = Learner::where('learn_inf_id', $user->id)->first();

    //     return response()->json(['user' => $user, 'info' => $info]);
    // }

    public function delAcc($id){
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function retForApproval(){       
        try {
            // Get pending mentors
            $pendingMentors = Mentor::where('approval_status', 'pending')
                ->join('users', 'mentor_infos.ment_inf_id', '=', 'users.id')
                ->select([
                    'users.id as user_id',
                    'users.name',
                    'mentor_infos.year',
                    'mentor_infos.course',
                    'mentor_infos.created_at',
                    'mentor_infos.mentor_no',
                    'mentor_infos.approval_status'
                ])
                ->get();

            // Get approved mentors
            $approvedMentors = Mentor::where('approval_status', 'approved')
                ->join('users', 'mentor_infos.ment_inf_id', '=', 'users.id')
                ->select([
                    'users.id as user_id',
                    'users.name',
                    'mentor_infos.year',
                    'mentor_infos.course',
                    'mentor_infos.created_at',
                    'mentor_infos.mentor_no',
                    'mentor_infos.approval_status'
                ])
                ->get();

            // Get rejected mentors
            $rejectedMentors = Mentor::where('approval_status', 'rejected')
                ->join('users', 'mentor_infos.ment_inf_id', '=', 'users.id')
                ->select([
                    'users.id as user_id',
                    'users.name',
                    'mentor_infos.year',
                    'mentor_infos.course',
                    'mentor_infos.created_at',
                    'mentor_infos.mentor_no',
                    'mentor_infos.approval_status'
                ])
                ->get();

            $transformMentors = function($mentors) {
                return $mentors->map(function ($mentor) {
                    return [
                        'user_id' => $mentor->user_id,
                        'name' => $mentor->name,
                        'course' => $mentor->course . ' ' . $mentor->year,
                        'applied_on' => date('Y-m-d', strtotime($mentor->created_at)),
                        'application_id' => $mentor->mentor_no,
                        'status' => $mentor->approval_status
                    ];
                });
            };

            return response()->json([
                'status' => 'success',
                'mentors' => [
                    'pending' => $transformMentors($pendingMentors),
                    'approved' => $transformMentors($approvedMentors),
                    'rejected' => $transformMentors($rejectedMentors)
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve mentor applications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    //approve account
    public function approveAcc($id){
        try {
            $mentor = Mentor::with('user')->where('ment_inf_id', $id)->first();
            $user = User::where('id', $id)->first();
            
            if (!$mentor) {
                return response()->json(['message' => 'Mentor not found'], 404);
            }

            $mentor->approved = 1;
            $mentor->approval_status = 'approved';
            $mentor->save();

            // Get email from associated user
            // if ($mentor->user) {
                Mail::to($user->email)->send(new AccountApprovedMail($user->id));
            // }

            return response()->json(['message' => 'Mentor approved successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to approve mentor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //reject account
    public function rejectAcc($id){
        try {
            $mentor = Mentor::with('user')->where('ment_inf_id', $id)->first();
            $user = User::where('id', $id)->first();

            if (!$mentor) {
                return response()->json(['message' => 'Mentor not found'], 404);
            }

            // Send email before updating status
            // if ($mentor->user) {
                Mail::to($user->email)->send(new AccountRejectedMail($user->id));
            // }

            $mentor->approved = 0;
            $mentor->approval_status = 'rejected';
            $mentor->save();

            return response()->json(['message' => 'Mentor rejected successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to reject mentor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Function to return the admin's name
    public function getAdminName()
    {
        try {
            // Find user with admin role
            $admin = User::where('role', 'admin')->first();
            
            if (!$admin) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'name' => $admin->name,
                'id' => $admin->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve admin information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
