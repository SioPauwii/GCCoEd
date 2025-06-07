<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Remove statefulApi() as we're using token-based auth
        
        // Keep CORS middleware
        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\HandleCorsAndCookies::class,
        ]);
        
        // Modify Sanctum middleware for API routes - remove EnsureFrontendRequestsAreStateful
        $middleware->api(append: [
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();



