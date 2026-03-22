<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->where('is_system_admin', false)->orderBy('username')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        // Nur globale Rollen (außer system_admin) — Projektrollen über die Matrix
        $roles = Role::where('scope', 'global')
            ->where('name', '!=', 'system_admin')
            ->orderBy('display_name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', Password::min(8)],
            'roles'    => ['nullable', 'array'],
            'roles.*'  => ['exists:roles,id'],
        ]);

        $user = User::create([
            'username' => $data['username'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Benutzer \"{$user->username}\" wurde angelegt.");
    }

    public function edit(User $user)
    {
        if ($user->is_system_admin) abort(403);

        $roles     = Role::where('scope', 'global')
            ->where('name', '!=', 'system_admin')
            ->orderBy('display_name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->is_system_admin) abort(403);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'unique:users,email,' . $user->id],
            'roles'   => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        $user->roles()->sync($data['roles'] ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', "Benutzer \"{$user->username}\" wurde aktualisiert.");
    }

    public function toggle(User $user)
    {
        if ($user->is_system_admin) abort(403);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'aktiviert' : 'deaktiviert';
        return back()->with('success', "Benutzer \"{$user->username}\" wurde {$status}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->is_system_admin) abort(403);

        $data = $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', "Passwort für \"{$user->username}\" wurde zurückgesetzt.");
    }
}
