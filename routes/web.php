<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Admin\PermissionMatrixController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
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

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard-Einstellungen
    Route::post('dashboard/preferences', [DashboardController::class, 'savePreferences'])->name('dashboard.preferences');

    // Profil
    Route::get('profile/roles',    [ProfileController::class, 'roles'])->name('profile.roles');
    Route::get('profile/password', [ProfileController::class, 'showPassword'])->name('profile.password');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Projekte
    Route::get('projects',        [ProjectController::class, 'index'])->name('projects.index');
    Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects',       [ProjectController::class, 'store'])->name('projects.store');

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

        // Berechtigungsmatrix
        Route::get('permissions',           [PermissionMatrixController::class, 'index'])->name('permissions.index');
        Route::post('permissions/assign',   [PermissionMatrixController::class, 'assign'])->name('permissions.assign');
        Route::delete('permissions/revoke', [PermissionMatrixController::class, 'revoke'])->name('permissions.revoke');

        // Einstellungen (Platzhalter)
        Route::get('settings', fn() => view('admin.settings'))->name('settings');
    });
});
