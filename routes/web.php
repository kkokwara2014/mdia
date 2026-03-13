<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/members', function () {
        return view('members.index');
    })->name('members.index');

    Route::get('/members/create', function () {
        return view('members.create');
    })->name('members.create');

    Route::get('/payments', function () {
        return view('payments.index');
    })->name('payments.index');

    Route::get('/payments/create', function () {
        return view('payments.create');
    })->name('payments.create');

    Route::get('/payment-types', function () {
        return view('payment-types.index');
    })->name('payment-types.index');

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');

    Route::get('/roles', function () {
        return view('roles.index');
    })->name('roles.index');

    Route::get('/permissions', function () {
        return view('permissions.index');
    })->name('permissions.index');
});
