<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\SignatureMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'is_admin' => IsAdmin::class,
            'signature' => SignatureMiddleware::class
        ]);
        $middleware->use([
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class
        ]);
        $middleware->api([
            IsAdmin::class,
            'signature:X-Mong-Application',
            // 'throttle:2,1'

        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
