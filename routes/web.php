<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TechnicianController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/requests', [ServiceController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [ServiceController::class, 'create'])
        ->middleware('role:customer')->name('requests.create');
    Route::post('/requests', [ServiceController::class, 'store'])
        ->middleware('role:customer')->name('requests.store');
    Route::get('/requests/{serviceRequest}', [ServiceController::class, 'show'])
        ->name('requests.show');
    Route::patch('/requests/{serviceRequest}/schedule', [ServiceController::class, 'schedule'])
        ->middleware('role:admin,staff')->name('requests.schedule');
    Route::patch('/requests/{serviceRequest}/status', [ServiceController::class, 'updateStatus'])
        ->middleware('role:admin,staff,technician')->name('requests.status');

    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/technicians', [TechnicianController::class, 'index'])->name('technicians.index');
        Route::post('/technicians', [TechnicianController::class, 'store'])->name('technicians.store');
        Route::get('/reports', ReportController::class)->name('reports');
    });
});
