<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCorsAndCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Allow-Origin', 'https://gc-co-ed.vercel.app');
            // $response->header('Access-Control-Allow-Origin', 'http://localhost:5173');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'X-XSRF-TOKEN, X-CSRF-Token, X-Requested-With, Accept, Content-Type');
        }

        return $response;
    }
}