<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->canSeeAllProjects()) {
            $projects = Project::with('creator')->orderBy('name')->get();
        } else {
            $projects = Project::with('creator')
                ->whereHas('userRoles', fn($q) => $q->where('user_id', $user->id))
                ->orderBy('name')
                ->get();
        }

        return view('dashboard', compact('projects'));
    }

    public function savePreferences(Request $request)
    {
        $data = $request->validate([
            'view'          => ['sometimes', 'in:tile,list'],
            'sort'          => ['sometimes', 'in:az,za'],
            'revision_view' => ['sometimes', 'in:journal,list'],
        ]);

        $user = auth()->user();
        $user->update([
            'dashboard_view' => $data['view']          ?? $user->dashboard_view,
            'dashboard_sort' => $data['sort']          ?? $user->dashboard_sort,
            'revision_view'  => $data['revision_view'] ?? $user->revision_view,
        ]);

        return response()->json(['ok' => true]);
    }
}
