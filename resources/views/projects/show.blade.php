@extends('layouts.app')

@section('title', $project->name)

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; {{ $project->name }}
@endsection

@section('content')

@php
$typeConfig = [
    'update'  => ['label' => 'Aktualisierung', 'bg' => '#DBEAFE', 'color' => '#1D4ED8', 'border' => '#93C5FD'],
    'change'  => ['label' => 'Änderung',       'bg' => '#FEF3C7', 'color' => '#B45309', 'border' => '#FCD34D'],
    'fix'     => ['label' => 'Fehlerbehebung', 'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5'],
    'release' => ['label' => 'Release',        'bg' => '#DCFCE7', 'color' => '#15803D', 'border' => '#86EFAC'],
    'hotfix'  => ['label' => 'Hotfix',         'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5'],
];
@endphp

{{-- Projekt-Header --}}
<div class="card" style="margin-bottom:1.5rem;">
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
    <div style="padding:.85rem 1.5rem; font-size:.875rem; color:#64748B; line-height:1.6;">
        {{ $project->description }}
    </div>
    @endif
</div>

@if(session('success'))
<div style="background:#ECFDF5; border:1px solid #6EE7B7; border-radius:8px; padding:.85rem 1.25rem; margin-bottom:1.25rem; color:#065F46; font-size:.875rem; display:flex; align-items:center; gap:.6rem;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Journal --}}
@if($revisions->isEmpty())
    <div class="card">
        <div style="text-align:center; padding:4rem; color:#94A3B8;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <div style="font-weight:600; color:#64748B;">Noch keine Revisionen vorhanden</div>
            @if($canEdit)
                <div style="font-size:.82rem; margin-top:.5rem;">
                    <a href="{{ route('revisions.create', $project) }}" style="color:var(--c-accent1);">Erste Revision anlegen</a>
                </div>
            @endif
        </div>
    </div>
@else
    <div style="display:flex; flex-direction:column; gap:0;">

        @foreach($revisions as $revision)
        <div style="display:flex; gap:0; align-items:stretch;">

            {{-- Linke Spalte: Linie + Dot --}}
            <div style="width:40px; flex-shrink:0; display:flex; flex-direction:column; align-items:center;">
                {{-- Linie oben (nicht beim ersten) --}}
                <div style="width:2px; flex:0 0 1.25rem;
                            background:{{ $loop->first ? 'transparent' : ($loop->first ? 'var(--c-accent1)' : '#E2E8F0') }};"></div>
                {{-- Dot --}}
                <div style="width:12px; height:12px; border-radius:50%; flex-shrink:0;
                            background:{{ $loop->first ? 'var(--c-accent1)' : '#fff' }};
                            border:2px solid {{ $loop->first ? 'var(--c-accent1)' : '#CBD5E1' }};
                            box-shadow:{{ $loop->first ? '0 0 0 3px rgba(6,182,212,.15)' : 'none' }};
                            z-index:1;"></div>
                {{-- Linie unten (nicht beim letzten) --}}
                @if(!$loop->last)
                <div style="width:2px; flex:1; min-height:1rem; background:#E2E8F0;"></div>
                @endif
            </div>

            {{-- Eintrag --}}
            <div style="flex:1; padding-bottom:1.5rem; padding-left:.75rem; padding-top:.5rem;">
            <div style="background:#fff; border:1px solid #E2E8F0; border-radius:10px;
                        {{ $loop->first ? 'border-left:3px solid var(--c-accent1);' : '' }}
                        box-shadow:{{ $loop->first ? '0 2px 8px rgba(0,0,0,.06)' : 'none' }};">

                {{-- Header --}}
                <div style="padding:.85rem 1.25rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; border-bottom:1px solid #F1F5F9;">
                    <div style="display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;">
                        {{-- Version --}}
                        <span style="font-size:.75rem; font-weight:700; color:#fff;
                                     background:{{ $loop->first ? 'var(--c-accent1)' : '#94A3B8' }};
                                     padding:.2rem .6rem; border-radius:5px; letter-spacing:.04em;">
                            v{{ $revision->version ?: '–' }}
                        </span>

                        {{-- Typ-Badges --}}
                        @foreach($revision->typesList as $type)
                            @php $tc = $typeConfig[$type] ?? ['label' => $type, 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
                            <span style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                                         font-size:.72rem; font-weight:600;
                                         background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                                {{ $tc['label'] }}
                            </span>
                        @endforeach

                        {{-- Titel --}}
                        <span style="font-size:.9rem; font-weight:700; color:#1E293B;">{{ $revision->title }}</span>
                    </div>

                    <div style="display:flex; align-items:center; gap:.75rem; flex-shrink:0;">
                        <div style="text-align:right;">
                            <div style="font-size:.75rem; color:#94A3B8;">{{ $revision->created_at->format('d.m.Y H:i') }}</div>
                            @if($revision->author)
                            <div style="font-size:.72rem; color:#CBD5E1;">
                                {{ $revision->author->vorname }} {{ $revision->author->nachname }}
                            </div>
                            @endif
                        </div>
                        @if($canEdit)
                            <a href="{{ route('revisions.replace', [$project, $revision]) }}"
                               class="btn btn-ghost btn-sm" style="white-space:nowrap;">Ersetzen</a>
                        @endif
                    </div>
                </div>

                {{-- Inhalts-Einträge --}}
                <div style="padding:.85rem 1.25rem; display:flex; flex-direction:column; gap:.6rem;">
                    @foreach($revision->entries as $entry)
                        @php $tc = $typeConfig[$entry['type']] ?? ['label' => $entry['type'], 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
                        <div style="display:flex; gap:.75rem; align-items:flex-start;">
                            <span style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                                         font-size:.7rem; font-weight:600; flex-shrink:0; margin-top:.1rem;
                                         background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                                {{ $tc['label'] }}
                            </span>
                            <span style="font-size:.875rem; color:#475569; line-height:1.65;">{{ $entry['content'] }}</span>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>{{-- /entry --}}
        </div>{{-- /row --}}
        @endforeach
    </div>
@endif

@endsection
