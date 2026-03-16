<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckAdminOrSuperAdmin;
use App\Http\Middleware\CheckCanValidatePayment;
use App\Http\Middleware\CheckGenerateReports;
use App\Http\Middleware\CheckIsMember;
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
            'generate_reports' => CheckGenerateReports::class,
            'member_only' => CheckIsMember::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });
    })->create();
