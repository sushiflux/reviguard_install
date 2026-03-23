<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SystemAdminController;
use App\Http\Controllers\Admin\PermissionMatrixController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\VersionChangelogController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Http\Controllers\WebAuthn\WebAuthnRegisterController;
use App\Http\Controllers\WebAuthn\WebAuthnTwoFactorController;
use App\Http\Controllers\WebAuthn\WebAuthnCredentialController;
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
//  2FA Challenge (pending session, not yet authenticated)
// ----------------------------------------------------------------
Route::middleware('2fa.pending')->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/',                  [TwoFactorChallengeController::class, 'show'])->name('challenge');
    Route::post('/totp',             [TwoFactorChallengeController::class, 'verifyTotp'])->name('totp.verify');
    Route::post('/webauthn/options', [WebAuthnTwoFactorController::class, 'options'])->name('webauthn.options');
    Route::post('/webauthn',         [WebAuthnTwoFactorController::class, 'verify'])->name('webauthn.verify');
});

// ----------------------------------------------------------------
//  Authenticated
// ----------------------------------------------------------------
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard preferences
    Route::post('dashboard/preferences', [DashboardController::class, 'savePreferences'])->name('dashboard.preferences');

    // Profil
    Route::get('profile/roles',     [ProfileController::class, 'roles'])->name('profile.roles');
    Route::get('profile/password',  [ProfileController::class, 'showPassword'])->name('profile.password');
    Route::post('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('profile/settings',  [ProfileController::class, 'showSettings'])->name('profile.settings');
    Route::post('profile/settings', [ProfileController::class, 'saveSettings'])->name('profile.settings.save');

    // 2FA Setup (redirect to settings security tab)
    Route::get('profile/2fa', fn() => redirect()->to(route('profile.settings') . '?tab=2fa'))->name('profile.2fa');
    Route::get('profile/2fa/totp/setup',     [TwoFactorChallengeController::class, 'setupTotp'])->name('profile.2fa.totp.setup');
    Route::post('profile/2fa/totp/confirm',  [TwoFactorChallengeController::class, 'confirmTotp'])->name('profile.2fa.totp.confirm');
    Route::post('profile/2fa/totp/disable',  [TwoFactorChallengeController::class, 'disableTotp'])->name('profile.2fa.totp.disable');

    // WebAuthn Register (from profile)
    Route::post('webauthn/register/options',   [WebAuthnRegisterController::class, 'options'])->name('webauthn.register.options');
    Route::post('webauthn/register',           [WebAuthnRegisterController::class, 'register'])->name('webauthn.register');
    Route::patch('webauthn/credentials/{id}',  [WebAuthnCredentialController::class, 'update'])->name('webauthn.credentials.update');
    Route::delete('webauthn/credentials/{id}', [WebAuthnCredentialController::class, 'destroy'])->name('webauthn.credentials.destroy');

    // Projekte
    Route::get('projects/create',        [ProjectController::class, 'create'])->name('projects.create');
    Route::post('projects',              [ProjectController::class, 'store'])->name('projects.store');
    Route::delete('projects/{project}',  [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::get('projects/{project}',     [ProjectController::class, 'show'])->name('projects.show');

    // Revisionen
    Route::get('projects/{project}/revisions/create',              [RevisionController::class, 'create'])->name('revisions.create');
    Route::post('projects/{project}/revisions',                    [RevisionController::class, 'store'])->name('revisions.store');
    Route::get('projects/{project}/revisions/{revision}/replace',  [RevisionController::class, 'showReplace'])->name('revisions.replace');
    Route::post('projects/{project}/revisions/{revision}/replace', [RevisionController::class, 'storeReplace'])->name('revisions.storeReplace');

    // Changelog (alle authentifizierten User)
    Route::get('changelog', [VersionChangelogController::class, 'index'])->name('changelog.index');

    // Changelog verwalten (Developer + Admin)
    Route::prefix('developer')->name('changelog.')->group(function () {
        Route::get('changelog',              [VersionChangelogController::class, 'manage'])->name('manage');
        Route::get('changelog/create',       [VersionChangelogController::class, 'create'])->name('create');
        Route::post('changelog',             [VersionChangelogController::class, 'store'])->name('store');
        Route::get('changelog/{entry}/edit', [VersionChangelogController::class, 'edit'])->name('edit');
        Route::put('changelog/{entry}',      [VersionChangelogController::class, 'update'])->name('update');
        Route::post('changelog/{entry}/release', [VersionChangelogController::class, 'release'])->name('release');
        Route::delete('changelog/{entry}',   [VersionChangelogController::class, 'destroy'])->name('destroy');
    });

    // ---- Admin ----
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        // Benutzer & Berechtigungen (kombinierte Seite)
        Route::get('access', function () {
            $users        = \App\Models\User::with(['roles', 'projectRoles.role', 'projectRoles.project'])
                                ->where('is_system_admin', false)->orderBy('username')->get();
            $projects     = \App\Models\Project::where('is_active', true)->orderBy('name')->get();
            $projectRoles = \App\Models\Role::where('scope', 'project')->orderBy('id')->get();
            $matrix = [];
            foreach ($users as $u) {
                foreach ($u->projectRoles as $pur) {
                    $matrix[$u->id][$pur->project_id] = $pur->role;
                }
            }
            return view('admin.access', compact('users', 'projects', 'projectRoles', 'matrix'));
        })->name('access');

        Route::resource('users', UserController::class)->except(['show', 'destroy', 'index']);
        Route::get('users', fn() => redirect()->to(route('admin.access') . '?tab=benutzer'))->name('users.index');
        Route::post('users/{user}/toggle',         [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::get('system-admins', fn() => redirect()->to(route('admin.settings') . '?tab=system-admins'))->name('system-admins.index');
        Route::post('system-admins/{user}/toggle',         [SystemAdminController::class, 'toggle'])->name('system-admins.toggle');
        Route::post('system-admins/{user}/reset-password', [SystemAdminController::class, 'resetPassword'])->name('system-admins.reset-password');

        Route::get('permissions', fn() => redirect()->to(route('admin.access') . '?tab=matrix'))->name('permissions.index');
        Route::post('permissions/assign',   [PermissionMatrixController::class, 'assign'])->name('permissions.assign');
        Route::delete('permissions/revoke', [PermissionMatrixController::class, 'revoke'])->name('permissions.revoke');

        Route::get('settings', function () {
            $policy          = \App\Models\SystemSetting::get('2fa_policy', 'none');
            $sessionTimeout  = (int) \App\Models\SystemSetting::get('session_timeout', 10);
            $admins          = \App\Models\User::with('roles')->where('is_system_admin', true)->orderBy('username')->get();
            return view('admin.settings', compact('policy', 'admins', 'sessionTimeout'));
        })->name('settings');

        Route::get('2fa-policy',  fn() => redirect()->to(route('admin.settings') . '?tab=sicherheit'))->name('2fa-policy.show');
        Route::post('2fa-policy', [\App\Http\Controllers\Admin\TwoFactorPolicyController::class, 'save'])->name('2fa-policy.save');

        Route::post('session-timeout', [\App\Http\Controllers\Admin\SessionTimeoutController::class, 'save'])->name('session-timeout.save');
    });
});
