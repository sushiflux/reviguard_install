@extends('layouts.app')

@section('title', 'Neue Revision')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo; Neue Revision
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Neue Revision anlegen</h2>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Abbrechen</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('revisions.store', $project) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Titel *</label>
                <input type="text" name="title" class="form-control"
                    value="{{ old('title') }}" required autofocus
                    placeholder="z.B. Sicherheits-Update für Authentifizierung">
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Typ *</label>
                    <select name="type" class="form-control" required>
                        <option value="">– Bitte wählen –</option>
                        <option value="update"  {{ old('type') === 'update'  ? 'selected' : '' }}>Aktualisierung</option>
                        <option value="fix"     {{ old('type') === 'fix'     ? 'selected' : '' }}>Fehlerbehebung</option>
                        <option value="change"  {{ old('type') === 'change'  ? 'selected' : '' }}>Änderung</option>
                        <option value="release" {{ old('type') === 'release' ? 'selected' : '' }}>Release</option>
                        <option value="hotfix"  {{ old('type') === 'hotfix'  ? 'selected' : '' }}>Hotfix</option>
                    </select>
                    @error('type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Version <span style="color:#94A3B8; font-weight:400;">(optional)</span></label>
                    <input type="text" name="version" class="form-control"
                        value="{{ old('version') }}"
                        placeholder="z.B. 1.2.3">
                    @error('version')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Inhalt *</label>
                <textarea name="content" class="form-control" rows="8" required
                    placeholder="Beschreibung der Änderungen...">{{ old('content') }}</textarea>
                @error('content')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Revision anlegen</button>
            </div>
        </form>
    </div>
</div>
@endsection
