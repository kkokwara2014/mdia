<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckAdminOrSuperAdmin;
use App\Http\Middleware\CheckCanValidatePayment;
use App\Http\Middleware\CheckSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
            'super_admin' => CheckSuperAdmin::class,
            'admin' => CheckAdminOrSuperAdmin::class,
            'can_validate_payment' => CheckCanValidatePayment::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
