<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectUserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionMatrixController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'projectRoles.role', 'projectRoles.project'])
            ->where('is_system_admin', false)
            ->orderBy('username')
            ->get();

        $projects     = Project::where('is_active', true)->orderBy('name')->get();
        $projectRoles = Role::where('scope', 'project')->orderBy('id')->get();

        // Matrix vorbereiten: [user_id][project_id] => role
        $matrix = [];
        foreach ($users as $user) {
            foreach ($user->projectRoles as $pur) {
                $matrix[$user->id][$pur->project_id] = $pur->role;
            }
        }

        return view('admin.permissions.matrix', compact('users', 'projects', 'projectRoles', 'matrix'));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'user_id'    => ['required', 'exists:users,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'role_id'    => ['required', 'exists:roles,id'],
        ]);

        // Sicherstellen dass nur Projektrollen zugewiesen werden
        $role = Role::findOrFail($data['role_id']);
        if ($role->scope !== 'project') {
            return response()->json(['error' => 'Nur projektgebundene Rollen erlaubt.'], 422);
        }

        ProjectUserRole::updateOrCreate(
            ['user_id' => $data['user_id'], 'project_id' => $data['project_id']],
            ['role_id' => $data['role_id'], 'assigned_by' => auth()->id()]
        );

        return response()->json(['success' => true, 'role' => $role->display_name]);
    }

    public function revoke(Request $request)
    {
        $request->validate([
            'user_id'    => ['required', 'exists:users,id'],
            'project_id' => ['required', 'exists:projects,id'],
        ]);

        ProjectUserRole::where([
            'user_id'    => $request->user_id,
            'project_id' => $request->project_id,
        ])->delete();

        return response()->json(['success' => true]);
    }
}
