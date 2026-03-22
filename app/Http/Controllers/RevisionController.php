<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Revision;
use Illuminate\Http\Request;

class RevisionController extends Controller
{
    public function create(Project $project)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);
        return view('projects.revisions.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);

        $data = $request->validate([
            'title'   => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'type'    => ['required', 'in:update,fix,change,release,hotfix'],
            'version' => ['nullable', 'string', 'max:30'],
        ]);

        Revision::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'title'      => $data['title'],
            'content'    => $data['content'],
            'type'       => $data['type'],
            'version'    => $data['version'] ?? null,
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Revision wurde erfolgreich angelegt.');
    }

    public function showReplace(Project $project, Revision $revision)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);
        if ($revision->project_id !== $project->id) abort(404);
        return view('projects.revisions.replace', compact('project', 'revision'));
    }

    public function storeReplace(Request $request, Project $project, Revision $revision)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);
        if ($revision->project_id !== $project->id) abort(404);

        $data = $request->validate([
            'title'   => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'type'    => ['required', 'in:update,fix,change,release,hotfix'],
            'version' => ['nullable', 'string', 'max:30'],
        ]);

        // Create new revision
        $new = Revision::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'title'      => $data['title'],
            'content'    => $data['content'],
            'type'       => $data['type'],
            'version'    => $data['version'] ?? null,
        ]);

        // Mark old revision as replaced
        $revision->update([
            'replaced_by_revision_id' => $new->id,
            'replaced_by_user_id'     => auth()->id(),
            'replaced_at'             => now(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Revision wurde erfolgreich ersetzt.');
    }
}
