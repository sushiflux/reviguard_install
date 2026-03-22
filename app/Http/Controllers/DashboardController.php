<?php

namespace App\Http\Controllers;

use App\Models\Project;

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
}
