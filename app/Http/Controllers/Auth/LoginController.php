<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Benutzername oder Passwort ist falsch.']);
        }

        if (!$user->is_active) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Dieses Konto wurde deaktiviert.']);
        }

        if (!Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => 'Benutzername oder Passwort ist falsch.']);
        }

        $request->session()->regenerate();

        // Check if 2FA is required
        if ($user->requiresTwoFactor()) {
            Auth::logout(); // Logout until 2FA is completed
            session(['2fa_pending_user_id' => $user->id]);
            return redirect()->route('2fa.challenge');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
