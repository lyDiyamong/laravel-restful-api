<?php

use App\Http\Middleware\Cors;
use App\Http\Middleware\IsAdmin;
use Illuminate\Foundation\Application;
use App\Http\Middleware\SignatureMiddleware;
use App\Http\Middleware\JsonHeaderMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\TransformInputMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'signature' => SignatureMiddleware::class,
            'transform.input' => TransformInputMiddleware::class,
            // "auth" => Authenticate::class, 
            'cors' =>  Cors::class,
        ]);
        $middleware->use([
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
            JsonHeaderMiddleware::class,
            Cors::class,
        ]);
        $middleware->api([
            // IsAdmin::class,
            "cors",
            'signature:X-Mong-Application',
            'throttle:100,1'

        ]);
        $middleware->web([
            HandleInertiaRequests::class,
        ]);
        // 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
