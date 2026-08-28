<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PermintaanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// AUTH
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// PROTECTED ROUTES
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/permintaan', [PermintaanController::class, 'index'])->name('permintaan.index');

    // PPS: buat permintaan (before {permintaan} to avoid conflict)
    Route::middleware('role:pps,admin')->group(function () {
        Route::get('/permintaan/buat', [PermintaanController::class, 'create'])->name('permintaan.create');
        Route::post('/permintaan', [PermintaanController::class, 'store'])->name('permintaan.store');
        Route::post('/permintaan/{permintaan}/kembalikan', [PermintaanController::class, 'kembalikan'])->name('permintaan.kembalikan');
    });

    Route::get('/permintaan/{permintaan}', [PermintaanController::class, 'show'])->name('permintaan.show');

    // TU: approve
    Route::middleware('role:tu,admin')->group(function () {
        Route::post('/permintaan/{permintaan}/approve-tu', [PermintaanController::class, 'approveTU'])->name('permintaan.approve.tu');
    });

    // PHPT dan SP: upload warkah untuk permintaan yang ditujukan ke seksinya
    Route::middleware('role:phpt,sp,admin')->group(function () {
        Route::post('/permintaan/{permintaan}/upload', [PermintaanController::class, 'uploadWarkah'])->name('permintaan.upload');
    });

    Route::get('/warkah/{file}/download', [PermintaanController::class, 'downloadFile'])->name('warkah.download');
    Route::get('/warkah/{file}/preview', [PermintaanController::class, 'previewFile'])->name('warkah.preview');

    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    });
});
