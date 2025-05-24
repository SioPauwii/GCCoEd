<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Middleware\ValidateSignature as BaseValidator;

class ValidateSignature extends BaseValidator
{
    /**
     * Handle the incoming request.
     */
    public function handle($request, Closure $next, ...$parameters): Response
    {
        if ($this->hasValidSignature($request)) {
            return $next($request);
        }

        return response()->json(['message' => 'Invalid signature'], 401);
    }

    /**
     * Determine if the request has a valid signature.
     */
    protected function hasValidSignature(Request $request): bool
    {
        return $request->hasValidSignature();
    }
}