<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function roles()
    {
        $user = auth()->user()->load([
            'roles',
            'projectRoles.project',
            'projectRoles.role',
        ]);

        return view('profile.roles', compact('user'));
    }

    public function showPassword()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::min(8), 'confirmed'],
        ], [
            'current_password.current_password' => 'Das aktuelle Passwort ist falsch.',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->to(route('profile.settings') . '?tab=passwort')
            ->with('success', 'Passwort wurde erfolgreich geändert.');
    }

    public function showSettings()
    {
        $user        = auth()->user();
        $credentials = $user->webAuthnCredentials()->whereEnabled()->get();

        return view('profile.settings', compact('credentials'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->validate([
            'dashboard_view'        => ['required', 'in:tile,list'],
            'revision_view'         => ['required', 'in:journal,list'],
            'predecessors_expanded' => ['required', 'in:0,1'],
        ]);

        auth()->user()->update([
            'dashboard_view'        => $data['dashboard_view'],
            'revision_view'         => $data['revision_view'],
            'predecessors_expanded' => (bool) $data['predecessors_expanded'],
        ]);

        return back()->with('success', 'Einstellungen wurden gespeichert.');
    }
}
