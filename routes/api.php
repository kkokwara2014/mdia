<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Dashboard\DashboardController;
use App\Http\Controllers\API\Member\ClaimAccountController;
use App\Http\Controllers\API\Member\MemberController;
use App\Http\Controllers\API\Payment\MemberPaymentController;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\PaymentType\PaymentTypeController;
use App\Http\Controllers\API\Permission\PermissionController;
use App\Http\Controllers\API\Permission\RolePermissionController;
use App\Http\Controllers\API\Report\ReportController;
use App\Http\Controllers\API\Role\RoleController;
use App\Http\Controllers\API\Role\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/claim', [ClaimAccountController::class, 'claim']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/payment-types', [PaymentTypeController::class, 'index']);
    Route::put('/members/profile', [MemberController::class, 'updateProfile']);
    
    Route::get('/payments/my', [PaymentController::class, 'myPayments']);
    Route::post('/payments/submit', [MemberPaymentController::class, 'submit']);
    
    Route::get('/dashboard/member', [DashboardController::class, 'memberStats']);
    Route::get('/reports/member', [ReportController::class, 'memberReport']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{user}', [MemberController::class, 'show']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{user}', [MemberController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'can_validate_payment'])->group(function () {
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::put('/payments/{payment}/verify', [PaymentController::class, 'verify']);
    
    Route::get('/dashboard/admin', [DashboardController::class, 'adminStats']);
    Route::get('/reports/admin', [ReportController::class, 'adminReport']);
});

Route::middleware(['auth:sanctum', 'super_admin'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    Route::get('/users/{user}/roles', [UserRoleController::class, 'index']);
    Route::post('/users/{user}/roles/assign', [UserRoleController::class, 'assign']);
    Route::delete('/users/{user}/roles/revoke', [UserRoleController::class, 'revoke']);

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::post('/permissions', [PermissionController::class, 'store']);
    Route::put('/permissions/{permission}', [PermissionController::class, 'update']);
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy']);

    Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'index']);
    Route::post('/roles/{role}/permissions/assign', [RolePermissionController::class, 'assign']);
    Route::delete('/roles/{role}/permissions/revoke', [RolePermissionController::class, 'revoke']);

    Route::post('/payment-types', [PaymentTypeController::class, 'store']);
    Route::put('/payment-types/{paymentType}', [PaymentTypeController::class, 'update']);
    Route::delete('/payment-types/{paymentType}', [PaymentTypeController::class, 'destroy']);
});
