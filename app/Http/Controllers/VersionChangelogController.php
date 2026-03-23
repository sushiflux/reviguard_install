<?php

namespace App\Http\Controllers;

use App\Models\VersionChangelog;
use Illuminate\Http\Request;

class VersionChangelogController extends Controller
{
    // Alle authentifizierten User
    public function index()
    {
        $entries = VersionChangelog::with('author')
            ->whereNotNull('released_at')
            ->orderByDesc('released_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('version')
            ->sortByDesc(function ($group, $version) {
                $parts = array_pad(array_map('intval', explode('.', $version)), 3, 0);
                return $parts[0] * 10000 + $parts[1] * 100 + $parts[2];
            });

        return view('changelog.index', compact('entries'));
    }

    // Developer: Übersicht aller Einträge
    public function manage()
    {
        $this->authorizeDeveloper();

        $entries = VersionChangelog::with('author')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('changelog.manage', compact('entries'));
    }

    public function create()
    {
        $this->authorizeDeveloper();
        return view('changelog.form', ['entry' => null]);
    }

    public function store(Request $request)
    {
        $this->authorizeDeveloper();

        $data = $request->validate([
            'version' => 'required|string|max:20',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        VersionChangelog::create([
            'version'     => $data['version'],
            'title'       => $data['title'],
            'content'     => $data['content'],
            'created_by'  => auth()->id(),
            'released_at' => $request->boolean('release') ? now() : null,
        ]);

        return redirect()->route('changelog.manage')
            ->with('success', 'Eintrag gespeichert.');
    }

    public function edit(VersionChangelog $entry)
    {
        $this->authorizeDeveloper();
        return view('changelog.form', compact('entry'));
    }

    public function update(Request $request, VersionChangelog $entry)
    {
        $this->authorizeDeveloper();

        $data = $request->validate([
            'version' => 'required|string|max:20',
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $entry->update($data);

        return redirect()->route('changelog.manage')
            ->with('success', 'Eintrag aktualisiert.');
    }

    public function release(VersionChangelog $entry)
    {
        $this->authorizeDeveloper();

        $entry->update(['released_at' => now()]);

        return redirect()->route('changelog.manage')
            ->with('success', "Version {$entry->version} veröffentlicht.");
    }

    public function destroy(VersionChangelog $entry)
    {
        $this->authorizeDeveloper();

        $entry->delete();

        return redirect()->route('changelog.manage')
            ->with('success', 'Eintrag gelöscht.');
    }

    private function authorizeDeveloper(): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('developer'),
            403
        );
    }
}
