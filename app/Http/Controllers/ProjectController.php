<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->canSeeAllProjects()) {
            $projects = Project::with('creator')->orderBy('name')->get();
        } else {
            // Nur Projekte, für die der User eine Rolle hat
            $projects = Project::with('creator')
                ->whereHas('userRoles', fn($q) => $q->where('user_id', $user->id))
                ->orderBy('name')
                ->get();
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        if (!auth()->user()->canCreateProjects()) {
            abort(403);
        }

        return view('projects.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canCreateProjects()) {
            abort(403);
        }

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $slug = Str::slug($data['name']);
        $base = $slug;
        $i    = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        Project::create([
            'name'        => $data['name'],
            'slug'        => $slug,
            'description' => $data['description'] ?? null,
            'is_active'   => true,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Projekt \"{$data['name']}\" wurde angelegt.");
    }

    public function destroy(Project $project)
    {
        if (!auth()->user()->canCreateProjects()) {
            abort(403);
        }

        $name = $project->name;
        $project->delete();

        return redirect()->route('dashboard')
            ->with('success', "Projekt \"{$name}\" wurde gelöscht.");
    }
}
