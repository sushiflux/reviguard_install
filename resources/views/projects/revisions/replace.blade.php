@extends('layouts.app')

@section('title', 'Revision ersetzen')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo; Revision ersetzen
@endsection

@section('content')

@php
    $typeLabels = [
        'update'  => 'Aktualisierung',
        'fix'     => 'Fehlerbehebung',
        'change'  => 'Änderung',
        'release' => 'Release',
        'hotfix'  => 'Hotfix',
    ];
@endphp

{{-- Warning notice --}}
<div style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; padding:.85rem 1.25rem; margin-bottom:1.25rem; color:#92400E; font-size:.875rem; display:flex; align-items:flex-start; gap:.6rem;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    Die bestehende Revision wird als ersetzt markiert und ist nicht mehr aktiv.
</div>

{{-- Existing revision (read-only) --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <h2 style="font-size:.9rem; color:#64748B; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Bestehende Revision</h2>
    </div>
    <div class="card-body">
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-bottom:1rem;">
            <div>
                <div style="font-size:.72rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem;">Titel</div>
                <div style="font-size:.9rem; color:#1E293B; font-weight:600;">{{ $revision->title }}</div>
            </div>
            <div>
                <div style="font-size:.72rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem;">Typ</div>
                <div style="font-size:.9rem; color:#1E293B;">{{ $typeLabels[$revision->type] ?? $revision->type }}</div>
            </div>
            <div>
                <div style="font-size:.72rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem;">Version</div>
                <div style="font-size:.9rem; color:#1E293B;">{{ $revision->version ?? '–' }}</div>
            </div>
        </div>
        <div>
            <div style="font-size:.72rem; font-weight:700; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.25rem;">Inhalt</div>
            <div style="font-size:.875rem; color:#64748B; line-height:1.6; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:.75rem 1rem; white-space:pre-wrap;">{{ $revision->content }}</div>
        </div>
    </div>
</div>

{{-- New revision form --}}
<div class="card">
    <div class="card-header">
        <h2>Neue Revision (Ersatz)</h2>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Abbrechen</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('revisions.storeReplace', [$project, $revision]) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Titel *</label>
                <input type="text" name="title" class="form-control"
                    value="{{ old('title', $revision->title) }}" required autofocus>
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Typ *</label>
                    <select name="type" class="form-control" required>
                        <option value="">– Bitte wählen –</option>
                        <option value="update"  {{ old('type', $revision->type) === 'update'  ? 'selected' : '' }}>Aktualisierung</option>
                        <option value="fix"     {{ old('type', $revision->type) === 'fix'     ? 'selected' : '' }}>Fehlerbehebung</option>
                        <option value="change"  {{ old('type', $revision->type) === 'change'  ? 'selected' : '' }}>Änderung</option>
                        <option value="release" {{ old('type', $revision->type) === 'release' ? 'selected' : '' }}>Release</option>
                        <option value="hotfix"  {{ old('type', $revision->type) === 'hotfix'  ? 'selected' : '' }}>Hotfix</option>
                    </select>
                    @error('type')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Version <span style="color:#94A3B8; font-weight:400;">(optional)</span></label>
                    <input type="text" name="version" class="form-control"
                        value="{{ old('version', $revision->version) }}"
                        placeholder="z.B. 1.2.3">
                    @error('version')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Inhalt *</label>
                <textarea name="content" class="form-control" rows="8" required>{{ old('content', $revision->content) }}</textarea>
                @error('content')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Revision ersetzen</button>
            </div>
        </form>
    </div>
</div>

@endsection
