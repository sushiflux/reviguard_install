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

        return back()->with('success', 'Passwort wurde erfolgreich geändert.');
    }
}
