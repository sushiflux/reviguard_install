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
            'view' => ['sometimes', 'in:tile,list'],
            'sort' => ['sometimes', 'in:az,za'],
        ]);

        auth()->user()->update($data === [] ? [] : [
            'dashboard_view' => $data['view'] ?? auth()->user()->dashboard_view,
            'dashboard_sort' => $data['sort'] ?? auth()->user()->dashboard_sort,
        ]);

        return response()->json(['ok' => true]);
    }
}
