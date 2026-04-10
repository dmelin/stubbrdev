<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdaptiveThrottle;
use App\Http\Middleware\ThrottleTokenRequests;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/daniel.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloud Run sits behind a proxy/load balancer, so trust forwarded headers.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'adaptiveThrottle' => AdaptiveThrottle::class,
            'throttle.token' => ThrottleTokenRequests::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'daniel/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
