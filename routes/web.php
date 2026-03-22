<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemAdminController;
use Illuminate\Support\Facades\Route;

// ----------------------------------------------------------------
//  Auth
// ----------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ----------------------------------------------------------------
//  Authenticated
// ----------------------------------------------------------------
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // ---- Admin ----
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Benutzer & Rollen
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::post('users/{user}/toggle',         [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // System-Admins
        Route::get('system-admins',                              [SystemAdminController::class, 'index'])->name('system-admins.index');
        Route::post('system-admins/{user}/toggle',               [SystemAdminController::class, 'toggle'])->name('system-admins.toggle');
        Route::post('system-admins/{user}/reset-password',       [SystemAdminController::class, 'resetPassword'])->name('system-admins.reset-password');

        // Einstellungen (Platzhalter)
        Route::get('settings', fn() => view('admin.settings'))->name('settings');
    });
});
