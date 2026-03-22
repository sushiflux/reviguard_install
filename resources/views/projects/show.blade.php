@extends('layouts.app')

@section('title', $project->name)

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; {{ $project->name }}
@endsection

@section('content')

@if(session('success'))
<div style="background:#ECFDF5; border:1px solid #6EE7B7; border-radius:8px; padding:.85rem 1.25rem; margin-bottom:1.25rem; color:#065F46; font-size:.875rem; display:flex; align-items:center; gap:.6rem;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="card-header">
        <div style="display:flex; align-items:center; gap:.75rem;">
            <h2 style="font-size:1.05rem; font-weight:700; color:#1E293B;">{{ $project->name }}</h2>
            @if($project->is_active)
                <span class="badge badge-green">Aktiv</span>
            @else
                <span class="badge badge-gray">Inaktiv</span>
            @endif
        </div>
        @if($canEdit)
            <a href="{{ route('revisions.create', $project) }}" class="btn btn-primary btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Neue Revision
            </a>
        @endif
    </div>

    @if($project->description)
    <div style="padding:.85rem 1.5rem; border-bottom:1px solid #E2E8F0; font-size:.875rem; color:#64748B; line-height:1.6;">
        {{ $project->description }}
    </div>
    @endif

    <div class="card-body" style="padding:0;">
        @if($revisions->isEmpty())
            <div style="text-align:center; padding:4rem; color:#94A3B8;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
                <div style="font-weight:600; color:#64748B;">Noch keine Revisionen vorhanden</div>
                @if($canEdit)
                    <div style="font-size:.82rem; margin-top:.5rem;">
                        <a href="{{ route('revisions.create', $project) }}" style="color:var(--c-accent1);">Erste Revision anlegen</a>
                    </div>
                @endif
            </div>
        @else
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Titel</th>
                        <th>Typ</th>
                        <th>Version</th>
                        <th>Autor</th>
                        <th>Datum</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($revisions as $revision)
                    @php
                        $typeLabels = [
                            'update'  => 'Aktualisierung',
                            'fix'     => 'Fehlerbehebung',
                            'change'  => 'Änderung',
                            'release' => 'Release',
                            'hotfix'  => 'Hotfix',
                        ];
                        $typeBadges = [
                            'update'  => 'badge-blue',
                            'fix'     => 'badge-red',
                            'change'  => 'badge-amber',
                            'release' => 'badge-green',
                            'hotfix'  => 'badge-red',
                        ];
                        $typeLabel = $typeLabels[$revision->type] ?? $revision->type;
                        $typeBadge = $typeBadges[$revision->type] ?? '';
                    @endphp
                    <tr>
                        <td style="font-weight:600; color:#1E293B;">{{ $revision->title }}</td>
                        <td><span class="badge {{ $typeBadge }}">{{ $typeLabel }}</span></td>
                        <td style="color:#64748B; font-size:.82rem;">{{ $revision->version ?? '–' }}</td>
                        <td style="color:#64748B; font-size:.82rem;">
                            @if($revision->author)
                                {{ trim($revision->author->vorname . ' ' . $revision->author->nachname) }}
                            @else
                                –
                            @endif
                        </td>
                        <td style="color:#94A3B8; font-size:.82rem; white-space:nowrap;">{{ $revision->created_at->format('d.m.Y H:i') }}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            @if($canEdit)
                                <a href="{{ route('revisions.replace', [$project, $revision]) }}" class="btn btn-ghost btn-sm">Ersetzen</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@endsection
