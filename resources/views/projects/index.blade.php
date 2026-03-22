@extends('layouts.app')

@section('title', 'Projekte')
@section('breadcrumb', 'Projekte')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Projekte</h2>
        @if(auth()->user()->canCreateProjects())
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Neues Projekt
            </a>
        @endif
    </div>

    @if($projects->isEmpty())
        <div class="card-body" style="text-align:center; padding:3rem; color:#94A3B8;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
            <div style="font-weight:600; color:#64748B;">Keine Projekte vorhanden</div>
            @if(auth()->user()->canCreateProjects())
                <div style="font-size:.82rem; margin-top:.4rem;">
                    <a href="{{ route('projects.create') }}" style="color:var(--c-accent1);">Erstes Projekt anlegen</a>
                </div>
            @endif
        </div>
    @else
        {{-- Kachelansicht --}}
        <div style="padding:1.5rem; display:grid; grid-template-columns:repeat(auto-fill, minmax(280px,1fr)); gap:1.25rem;">
            @foreach($projects as $project)
            <div style="border:1px solid #E2E8F0; border-radius:10px; padding:1.25rem; background:#fff; border-top: 3px solid {{ $project->is_active ? 'var(--c-accent1)' : '#CBD5E1' }}; transition: box-shadow .15s;"
                 onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                 onmouseout="this.style.boxShadow='none'">

                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:.75rem;">
                    <div style="font-size:1rem; font-weight:700; color:#1E293B;">{{ $project->name }}</div>
                    @if($project->is_active)
                        <span class="badge badge-green">Aktiv</span>
                    @else
                        <span class="badge badge-gray">Inaktiv</span>
                    @endif
                </div>

                @if($project->description)
                    <div style="font-size:.82rem; color:#64748B; margin-bottom:1rem; line-height:1.5;">
                        {{ Str::limit($project->description, 100) }}
                    </div>
                @else
                    <div style="font-size:.82rem; color:#CBD5E1; margin-bottom:1rem;">Keine Beschreibung</div>
                @endif

                <div style="display:flex; align-items:center; justify-content:space-between; padding-top:.75rem; border-top:1px solid #F1F5F9;">
                    <div style="font-size:.75rem; color:#94A3B8;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:.25rem;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{ $project->creator->username ?? '–' }}
                        &nbsp;&bull;&nbsp;
                        {{ $project->created_at->format('d.m.Y') }}
                    </div>
                    <a href="#" class="btn btn-ghost btn-sm">
                        Öffnen
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
