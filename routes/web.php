<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\Web\Member\MemberController;
use App\Http\Controllers\Web\Payment\PaymentController;
use App\Http\Controllers\Web\Profile\ProfileController;
use App\Http\Controllers\Web\PaymentType\PaymentTypeController;
use App\Http\Controllers\Web\Permission\PermissionController;
use App\Http\Controllers\Web\Report\ReportController;
use App\Http\Controllers\Web\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');

    Route::get('/members/search', [MemberController::class, 'search'])->name('members.search');

    Route::middleware(['admin'])->group(function () {
        Route::get('/members', [MemberController::class, 'index'])->name('members.index');
        Route::get('/members/create', [MemberController::class, 'create'])->name('members.create');
        Route::post('/members', [MemberController::class, 'store'])->name('members.store');
        Route::get('/members/download-pdf', [MemberController::class, 'downloadPdf'])->name('members.download-pdf');
        Route::get('/members/{user}', [MemberController::class, 'show'])->name('members.show');
        Route::get('/members/{user}/edit', [MemberController::class, 'edit'])->name('members.edit');
        Route::put('/members/{user}', [MemberController::class, 'update'])->name('members.update');
    });

    Route::delete('/members/{user}', [MemberController::class, 'destroy'])->name('members.destroy')
        ->middleware(['auth', 'super_admin']);

    Route::middleware(['member_only'])->group(function () {
        Route::get('/my-payments', [PaymentController::class, 'myPayments'])->name('payments.my-payments');
        Route::get('/payments/submit', [PaymentController::class, 'submitPayment'])->name('payments.submit');
        Route::post('/payments/submit', [PaymentController::class, 'storeSubmitPayment'])->name('payments.submit.store');
    });

    Route::middleware(['can_validate_payment'])->group(function () {
        Route::get('/payments/filter', [PaymentController::class, 'filter'])->name('payments.filter');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::put('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    });

    Route::post('/members/{user}/regenerate-password', [MemberController::class, 'regeneratePassword'])
        ->name('members.regenerate-password')
        ->middleware(['auth', 'super_admin']);

    Route::middleware(['admin'])->group(function () {
        Route::get('/payment-types', [PaymentTypeController::class, 'index'])->name('payment-types.index');
        Route::get('/payment-types/create', [PaymentTypeController::class, 'create'])->name('payment-types.create');
        Route::post('/payment-types', [PaymentTypeController::class, 'store'])->name('payment-types.store');
        Route::get('/payment-types/{paymentType}/edit', [PaymentTypeController::class, 'edit'])->name('payment-types.edit');
        Route::put('/payment-types/{paymentType}', [PaymentTypeController::class, 'update'])->name('payment-types.update');
        Route::delete('/payment-types/{paymentType}', [PaymentTypeController::class, 'destroy'])->name('payment-types.destroy');
    });

    Route::middleware(['super_admin'])->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    Route::middleware(['can_validate_payment'])->group(function () {
        Route::get('/reports/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.download-pdf');
        Route::get('/reports/filter', [ReportController::class, 'filter'])->name('reports.filter');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

});
