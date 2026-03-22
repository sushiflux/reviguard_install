<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SystemAdminController extends Controller
{
    public function index()
    {
        $admins = User::with('roles')->where('is_system_admin', true)->orderBy('username')->get();
        return view('admin.system-admins.index', compact('admins'));
    }

    public function toggle(User $user)
    {
        if (!$user->is_system_admin) abort(403);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'aktiviert' : 'deaktiviert';
        return back()->with('success', "System-Admin \"{$user->username}\" wurde {$status}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        if (!$user->is_system_admin) abort(403);

        $data = $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "Passwort für \"{$user->username}\" wurde zurückgesetzt.");
    }
}
