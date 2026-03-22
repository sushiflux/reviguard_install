@extends('layouts.app')

@section('title', 'Meine Berechtigungen')
@section('breadcrumb', 'Meine Berechtigungen')

@section('content')
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; align-items:start;">

    {{-- Globale Rollen --}}
    <div class="card">
        <div class="card-header">
            <h2>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--c-accent1)" stroke-width="2" style="vertical-align:middle; margin-right:.4rem;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                Globale Rollen
            </h2>
        </div>
        <div class="card-body">
            @if($user->roles->isEmpty())
                <p style="color:#94A3B8; font-size:.875rem;">Keine globalen Rollen zugewiesen.</p>
            @else
                <div style="display:flex; flex-direction:column; gap:.75rem;">
                    @foreach($user->roles as $role)
                    <div style="display:flex; align-items:flex-start; gap:.75rem; padding:.75rem 1rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; border-left:3px solid var(--c-accent1);">
                        <div style="flex:1;">
                            <div style="font-size:.875rem; font-weight:700; color:#1E293B;">{{ $role->display_name }}</div>
                            @if($role->description)
                            <div style="font-size:.78rem; color:#64748B; margin-top:.2rem;">{{ $role->description }}</div>
                            @endif
                        </div>
                        <span class="badge badge-cyan">global</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Projektrollen --}}
    <div class="card">
        <div class="card-header">
            <h2>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--c-accent2)" stroke-width="2" style="vertical-align:middle; margin-right:.4rem;">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
                Projektrollen
            </h2>
        </div>
        <div class="card-body">
            @if($user->projectRoles->isEmpty())
                <p style="color:#94A3B8; font-size:.875rem;">Keine Projektrollen zugewiesen.</p>
            @else
                <div style="display:flex; flex-direction:column; gap:.75rem;">
                    @foreach($user->projectRoles as $pur)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:.75rem 1rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; border-left:3px solid var(--c-accent2);">
                        <div>
                            <div style="font-size:.875rem; font-weight:700; color:#1E293B;">
                                {{ $pur->project->name ?? '–' }}
                            </div>
                            @if($pur->project?->description)
                            <div style="font-size:.75rem; color:#94A3B8; margin-top:.1rem;">
                                {{ Str::limit($pur->project->description, 60) }}
                            </div>
                            @endif
                        </div>
                        <div style="text-align:right;">
                            @php
                                $roleColors = [
                                    'projektleiter' => 'badge-blue',
                                    'editor'        => 'badge-cyan',
                                    'viewer'        => 'badge-gray',
                                ];
                                $colorClass = $roleColors[$pur->role->name ?? ''] ?? 'badge-gray';
                            @endphp
                            <span class="badge {{ $colorClass }}">
                                {{ $pur->role->display_name ?? '–' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
