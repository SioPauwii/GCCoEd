<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Mentor;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated first
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        // Check if user has the required role
        if ($user->role !== $role) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Additional check for mentor role - verify approval status
        if ($role === 'mentor') {
            $mentorInfo = Mentor::where('ment_inf_id', $user->id)->first();
            
            // Check if mentor info exists and is approved
            if (!$mentorInfo || $mentorInfo->approval_status !== 'approved') {
                return response()->json([
                    'message' => 'Mentor account not approved yet',
                    'status' => $mentorInfo ? $mentorInfo->approval_status : 'not_found'
                ], 403);
            }
        }

        return $next($request);
    }
}
