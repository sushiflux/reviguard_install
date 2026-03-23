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
        $nextVersion = Revision::nextVersion($project->id);
        return view('projects.revisions.create', compact('project', 'nextVersion'));
    }

    public function store(Request $request, Project $project)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);

        $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'entries'           => ['required', 'array', 'min:1'],
            'entries.*.type'    => ['required', 'in:update,fix,change,release,hotfix,deaktiviert,broken'],
            'entries.*.content' => ['required', 'string', 'max:5000'],
        ]);

        $entries = array_values(array_filter($request->entries, fn($e) => !empty($e['content'])));
        $types   = implode(',', array_unique(array_column($entries, 'type')));

        Revision::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'content'    => json_encode($entries),
            'type'       => $types,
            'version'    => Revision::nextVersion($project->id),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Revision wurde erfolgreich angelegt.');
    }

    public function showReplace(Project $project, Revision $revision)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);
        if ($revision->project_id !== $project->id) abort(404);
        $nextVersion = Revision::nextVersion($project->id);
        return view('projects.revisions.replace', compact('project', 'revision', 'nextVersion'));
    }

    public function storeReplace(Request $request, Project $project, Revision $revision)
    {
        if (!auth()->user()->canEditProject($project->id)) abort(403);
        if ($revision->project_id !== $project->id) abort(404);

        $request->validate([
            'title'              => ['required', 'string', 'max:200'],
            'entries'            => ['required', 'array', 'min:1'],
            'entries.*.type'     => ['required', 'in:update,fix,change,release,hotfix,deaktiviert,broken'],
            'entries.*.content'  => ['required', 'string', 'max:5000'],
            'entries.*.removed'  => ['nullable', 'in:0,1'],
        ]);

        $entries = array_values(array_filter($request->entries, fn($e) => !empty($e['content'])));
        $entries = array_map(function ($e) {
            $e['removed'] = isset($e['removed']) && $e['removed'] === '1';
            return $e;
        }, $entries);

        $activeEntries = array_values(array_filter($entries, fn($e) => !$e['removed']));
        if (empty($activeEntries)) {
            return back()->withErrors(['entries' => 'Die neue Revision muss mindestens einen aktiven (nicht gestrichenen) Eintrag enthalten.'])->withInput();
        }

        $types = implode(',', array_unique(array_column($activeEntries, 'type')));

        $new = Revision::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'content'    => json_encode($entries),
            'type'       => $types,
            'version'    => Revision::nextVersion($project->id),
        ]);

        $revision->update([
            'replaced_by_revision_id' => $new->id,
            'replaced_by_user_id'     => auth()->id(),
            'replaced_at'             => now(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Revision wurde erfolgreich ersetzt.');
    }
}
