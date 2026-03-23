@extends('layouts.app')

@section('title', $project->name)

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; {{ $project->name }}
@endsection

@section('content')

@php
$typeConfig = [
    'update'   => ['label' => 'Update',         'bg' => 'rgba(59,130,246,.14)',  'color' => '#3B82F6', 'border' => 'rgba(59,130,246,.35)'],
    'change'   => ['label' => 'Änderung',       'bg' => 'rgba(234,179,8,.14)',   'color' => '#CA8A04', 'border' => 'rgba(234,179,8,.35)'],
    'fix'      => ['label' => 'Fehlerbehebung', 'bg' => 'rgba(239,68,68,.1)',    'color' => '#EF4444', 'border' => 'rgba(239,68,68,.35)'],
    'release'  => ['label' => 'Release',        'bg' => 'rgba(34,197,94,.13)',   'color' => '#22C55E', 'border' => 'rgba(34,197,94,.35)'],
    'hotfix'   => ['label' => 'Hotfix',         'bg' => 'rgba(239,68,68,.1)',    'color' => '#EF4444', 'border' => 'rgba(239,68,68,.35)'],
    'replaced' => ['label' => 'Ersetzt',        'bg' => 'rgba(148,163,184,.12)', 'color' => '#94A3B8', 'border' => 'rgba(148,163,184,.3)'],
];
$savedView            = auth()->user()->revision_view ?? 'journal';
$predExpanded         = auth()->user()->predecessors_expanded ?? false;
@endphp

{{-- Projekt-Header --}}
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">
        <div style="display:flex; align-items:center; gap:.75rem;">
            <h2 style="font-size:1.05rem; font-weight:700; color:var(--t-text);">{{ $project->name }}</h2>
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
    <div style="padding:.85rem 1.5rem; font-size:.875rem; color:var(--t-text-muted); line-height:1.6;">
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

{{-- Toolbar --}}
<div style="display:flex; flex-wrap:wrap; align-items:center; gap:.75rem; margin-bottom:1.25rem;">

    {{-- Search --}}
    <div style="flex:1; min-width:220px; position:relative;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2.5"
             style="position:absolute; left:.7rem; top:50%; transform:translateY(-50%); pointer-events:none;">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input id="rev-search" type="search" placeholder="Suche… oder *fix *neu *hotfix für ODER-Verknüpfung"
               style="width:100%; padding:.5rem .75rem .5rem 2.1rem; border:1px solid var(--t-border); border-radius:7px;
                      font-size:.85rem; color:var(--t-input-text); background:var(--t-input-bg); outline:none; box-sizing:border-box;"
               onfocus="this.style.borderColor='var(--c-accent1)'"
               onblur="this.style.borderColor=getComputedStyle(document.documentElement).getPropertyValue('--t-border').trim()">
    </div>

    {{-- Type filter badges --}}
    <div style="display:flex; flex-wrap:wrap; gap:.4rem;">
        <button type="button" class="type-filter-btn active-filter" data-type=""
                style="padding:.3rem .75rem; border-radius:6px; border:1px solid var(--t-border);
                       background:var(--t-surface2); font-size:.75rem; font-weight:600; color:var(--t-text-muted); cursor:pointer;">
            Alle
        </button>
        @foreach($typeConfig as $typeKey => $cfg)
        <button type="button" class="type-filter-btn" data-type="{{ $typeKey }}"
                style="padding:.3rem .75rem; border-radius:6px; border:1px solid {{ $cfg['border'] }};
                       background:var(--t-surface); font-size:.75rem; font-weight:600; color:{{ $cfg['color'] }}; cursor:pointer; opacity:.65;">
            {{ $cfg['label'] }}
        </button>
        @endforeach
    </div>

    {{-- View toggle --}}
    <div style="display:flex; border:1px solid var(--t-border); border-radius:7px; overflow:hidden; flex-shrink:0; background:var(--t-surface);">
        <button id="btn-journal" type="button" onclick="setView('journal')"
                style="padding:.45rem .85rem; border:none; cursor:pointer; font-size:.8rem; font-weight:600;
                       display:flex; align-items:center; gap:.4rem; transition:background .15s; background:transparent; color:var(--t-text-muted);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                <line x1="8" y1="18" x2="21" y2="18"/>
                <circle cx="3" cy="6" r="1.5" fill="currentColor" stroke="none"/>
                <circle cx="3" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                <circle cx="3" cy="18" r="1.5" fill="currentColor" stroke="none"/>
            </svg>
            Journal
        </button>
        <button id="btn-list" type="button" onclick="setView('list')"
                style="padding:.45rem .85rem; border:none; border-left:1px solid var(--t-border); cursor:pointer; font-size:.8rem; font-weight:600;
                       display:flex; align-items:center; gap:.4rem; transition:background .15s; background:transparent; color:var(--t-text-muted);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
            Liste
        </button>
    </div>
</div>

{{-- No-results hint --}}
<div id="no-results" style="display:none;">
    <div class="card">
        <div style="text-align:center; padding:3rem; color:#94A3B8; font-size:.875rem;">
            Keine Revisionen gefunden.
        </div>
    </div>
</div>

{{-- ============================== JOURNAL VIEW ============================== --}}
<div id="view-journal" style="flex-direction:column; gap:0; display:{{ $savedView === 'list' ? 'none' : 'flex' }};">

    @foreach($revisions as $revision)
    @php
        $entryTypes = array_unique(array_column($revision->entries, 'type'));
        $allContent = implode(' ', array_column($revision->entries, 'content'));
        $authorName = $revision->author ? trim($revision->author->vorname . ' ' . $revision->author->nachname) : '';

        // Build full predecessor chain: follow replaced_by_revision_id backwards
        $predecessorChain = [];
        $chainId = $revision->id;
        while (isset($replacedMap[$chainId])) {
            $pred = $replacedMap[$chainId];
            $predecessorChain[] = $pred;
            $chainId = $pred->id;
        }
        if (count($predecessorChain) > 0) { $entryTypes[] = 'replaced'; }
    @endphp
    <div class="rev-item" id="journal-rev-{{ $revision->id }}"
         data-version="{{ $revision->version }}"
         data-title="{{ strtolower($revision->title) }}"
         data-types="{{ implode(' ', $entryTypes) }}"
         data-author="{{ strtolower($authorName) }}"
         data-date="{{ $revision->created_at->format('d.m.Y') }}"
         data-content="{{ strtolower($allContent) }}"
         style="display:flex; gap:0; align-items:stretch;">

        {{-- Linke Spalte: Linie + Dot --}}
        <div style="width:40px; flex-shrink:0; display:flex; flex-direction:column; align-items:center;">
            <div class="tl-top"    style="width:2px; flex:1; background:var(--t-border);"></div>
            <div class="tl-dot"    style="width:12px; height:12px; border-radius:50%; flex-shrink:0;
                        background:var(--t-surface2); border:2px solid var(--t-border); box-shadow:none; z-index:1;"></div>
            <div class="tl-bottom" style="width:2px; flex:1; background:var(--t-border);"></div>
        </div>

        {{-- Eintrag --}}
        <div style="flex:1; padding-bottom:1.5rem; padding-left:.75rem; padding-top:.5rem;">
        <div class="rev-card" style="background:var(--t-surface); border:1px solid var(--t-border); border-radius:10px; box-shadow:none;">

            {{-- Header --}}
            <div style="padding:.85rem 1.25rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; border-bottom:1px solid var(--t-border2);">
                <div style="display:flex; align-items:center; gap:.5rem; flex-wrap:wrap;">
                    {{-- Version --}}
                    <span class="version-badge" style="font-size:.75rem; font-weight:700; color:#fff;
                                 background:#94A3B8;
                                 padding:.2rem .6rem; border-radius:5px; letter-spacing:.04em;">
                        v{{ $revision->version ?: '–' }}
                    </span>
                    {{-- Ersetzt-Icon --}}
                    @if(count($predecessorChain) > 0)
                        <span style="display:inline-flex; align-items:center; padding:.25rem .4rem; border-radius:6px;
                                     background:var(--t-surface2); border:1px solid var(--t-border);" title="Ersetzt">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5">
                                <polyline points="1 4 1 10 7 10"/>
                                <path d="M3.51 15a9 9 0 1 0 .49-4.78"/>
                            </svg>
                        </span>
                    @endif
                    {{-- Trennstrich --}}
                    <span style="width:1px; height:16px; background:var(--t-border); flex-shrink:0;"></span>
                    {{-- Typ-Badges --}}
                    @foreach($revision->typesList as $type)
                        @php $tc = $typeConfig[$type] ?? ['label' => $type, 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
                        <span class="rev-type-badge" data-type="{{ $type }}" style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                                     font-size:.72rem; font-weight:600;
                                     background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                            {{ $tc['label'] }}
                        </span>
                    @endforeach
                    {{-- Titel --}}
                    <span style="font-size:.9rem; font-weight:700; color:var(--t-text); margin-left:.25rem;">{{ $revision->title }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:.75rem; flex-shrink:0;">
                    @if(count($predecessorChain) > 0)
                    <button type="button"
                            onclick="togglePredecessor('pred-{{ $revision->id }}')"
                            title="Vorgänger-Revisionen anzeigen"
                            style="padding:.3rem .45rem; background:none; border:1px solid #E2E8F0; border-radius:5px;
                                   cursor:pointer; color:#94A3B8; flex-shrink:0; line-height:1; display:flex; align-items:center; gap:.3rem;
                                   font-size:.72rem; font-weight:600; transition:all .15s;"
                            onmouseover="this.style.color='#B45309'; this.style.borderColor='#FCD34D'; this.style.background='#FFFBEB';"
                            onmouseout="this.style.color='#94A3B8'; this.style.borderColor='#E2E8F0'; this.style.background='none';">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 .49-4.78"/>
                        </svg>
                        {{ count($predecessorChain) }} {{ count($predecessorChain) === 1 ? 'Vorgänger' : 'Vorgänger' }}
                    </button>
                    @endif
                    <div style="text-align:right;">
                        <div style="font-size:.75rem; color:var(--t-text-muted);">{{ $revision->created_at->format('d.m.Y H:i') }}</div>
                        @if($revision->author)
                        <div style="font-size:.72rem; color:var(--t-text-sub);">
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
                @foreach(collect($revision->entries)->sortBy(fn($e) => !empty($e['removed']) ? 1 : 0)->values() as $entry)
                    @php
                        $tc = $typeConfig[$entry['type']] ?? ['label' => $entry['type'], 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1'];
                        $isRemoved = !empty($entry['removed']);
                    @endphp
                    <div style="display:flex; gap:.75rem; align-items:flex-start;
                                {{ $isRemoved ? 'background:rgba(239,68,68,.07); border:1px solid rgba(239,68,68,.2); border-left:3px solid rgba(239,68,68,.45); border-radius:6px; padding:.35rem .6rem;' : '' }}">
                        <span class="rev-type-badge" data-type="{{ $entry['type'] }}" style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                                     font-size:.7rem; font-weight:600; flex-shrink:0; margin-top:.1rem;
                                     background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                            {{ $tc['label'] }}
                        </span>
                        <span style="font-size:.875rem; line-height:1.65; flex:1;
                                     color:var(--t-text-sub);
                                     {{ $isRemoved ? 'text-decoration:line-through; text-decoration-color:rgba(239,68,68,.6);' : 'color:var(--t-text-muted);' }}">{{ $entry['content'] }}</span>
                        @if($isRemoved)
                            <span style="flex-shrink:0; align-self:center; font-size:.68rem; font-weight:600; padding:.1rem .45rem; border-radius:4px;
                                         background:rgba(239,68,68,.1); color:#DC2626; border:1px solid rgba(239,68,68,.25); white-space:nowrap;">Gestrichen</span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Vorgänger-Kette (ausgeklappt) --}}
            @if(count($predecessorChain) > 0)
            <div id="pred-{{ $revision->id }}" style="display:{{ $predExpanded ? 'block' : 'none' }}; border-top:1px solid rgba(245,158,11,.25); border-radius:0 0 10px 10px; overflow:hidden; box-shadow:inset 3px 0 0 rgba(245,158,11,.55);">
                @foreach($predecessorChain as $pred)
                <div id="pred-entry-{{ $pred->id }}" style="background:rgba(245,158,11,.06); padding:.85rem 1.25rem;
                            {{ !$loop->last ? 'border-bottom:1px solid rgba(245,158,11,.2);' : '' }}">
                    {{-- Predecessor header --}}
                    <div style="display:flex; align-items:center; gap:.6rem; margin-bottom:.65rem; flex-wrap:wrap;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--c-accent2)" stroke-width="2.5" style="flex-shrink:0;">
                            <polyline points="1 4 1 10 7 10"/>
                            <path d="M3.51 15a9 9 0 1 0 .49-4.78"/>
                        </svg>
                        <span style="font-size:.72rem; font-weight:700; color:var(--c-accent2); text-transform:uppercase; letter-spacing:.05em;">
                            {{ $loop->first ? 'Direkte Vorgänger-Revision' : 'Ältere Vorgänger-Revision' }}
                        </span>
                        <span style="font-size:.72rem; font-weight:700; color:#fff; background:var(--c-accent2);
                                     padding:.15rem .5rem; border-radius:4px; letter-spacing:.04em;">
                            v{{ $pred->version }}
                        </span>
                        <span style="font-size:.75rem; color:var(--c-accent2); font-weight:600;">{{ $pred->title }}</span>
                        <span style="font-size:.72rem; color:var(--c-accent2); margin-left:auto;">
                            {{ $pred->created_at->format('d.m.Y H:i') }}
                            @if($pred->author)
                                &nbsp;· {{ $pred->author->vorname }} {{ $pred->author->nachname }}
                            @endif
                        </span>
                    </div>
                    {{-- Predecessor entries --}}
                    <div style="display:flex; flex-direction:column; gap:.5rem;">
                        @foreach(collect($pred->entries)->sortBy(fn($e) => !empty($e['removed']) ? 1 : 0)->values() as $entry)
                            @php
                                $tc = $typeConfig[$entry['type']] ?? ['label' => $entry['type'], 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1'];
                                $isRemoved = !empty($entry['removed']);
                            @endphp
                            <div style="display:flex; gap:.75rem; align-items:flex-start;
                                        {{ $isRemoved ? 'background:rgba(239,68,68,.07); border:1px solid rgba(239,68,68,.2); border-left:3px solid rgba(239,68,68,.45); border-radius:6px; padding:.35rem .6rem;' : '' }}">
                                <span class="rev-type-badge" data-type="{{ $entry['type'] }}" style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                                             font-size:.7rem; font-weight:600; flex-shrink:0; margin-top:.1rem;
                                             background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                                    {{ $tc['label'] }}
                                </span>
                                <span style="font-size:.85rem; line-height:1.65; flex:1;
                                             {{ $isRemoved ? 'text-decoration:line-through; text-decoration-color:rgba(239,68,68,.6); color:var(--t-text-sub);' : 'color:var(--t-text-muted);' }}">{{ $entry['content'] }}</span>
                                @if($isRemoved)
                                    <span style="flex-shrink:0; align-self:center; font-size:.68rem; font-weight:600; padding:.1rem .45rem; border-radius:4px;
                                                 background:rgba(239,68,68,.1); color:#DC2626; border:1px solid rgba(239,68,68,.25); white-space:nowrap;">Gestrichen</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
        </div>
    </div>{{-- /rev-item journal --}}
    @endforeach
</div>

{{-- ============================== LIST VIEW ============================== --}}
<div id="view-list" style="display:{{ $savedView === 'list' ? 'block' : 'none' }};">
    <div class="card" style="overflow:hidden;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Version / Typen</th>
                    <th>Titel</th>
                    <th>Erstellt am</th>
                    <th>Autor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="list-body">
                @foreach($revisions as $revision)
                @php
                    $entryTypes = array_unique(array_column($revision->entries, 'type'));
                    $allContent = implode(' ', array_column($revision->entries, 'content'));
                    $authorName = $revision->author ? trim($revision->author->vorname . ' ' . $revision->author->nachname) : '';
                    $predecessorChain = [];
                    $chainId = $revision->id;
                    while (isset($replacedMap[$chainId])) {
                        $pred = $replacedMap[$chainId];
                        $predecessorChain[] = $pred;
                        $chainId = $pred->id;
                    }
                    $hasPred = count($predecessorChain) > 0;
                    if ($hasPred) { $entryTypes[] = 'replaced'; }
                @endphp
                <tr class="rev-item"
                    data-version="{{ $revision->version }}"
                    data-title="{{ strtolower($revision->title) }}"
                    data-types="{{ implode(' ', $entryTypes) }}"
                    data-author="{{ strtolower($authorName) }}"
                    data-date="{{ $revision->created_at->format('d.m.Y') }}"
                    data-content="{{ strtolower($allContent) }}"
                    data-pred-row="pred-list-{{ $revision->id }}">
                    <td style="box-shadow:inset 3px 0 0 rgba(6,182,212,.6);">
                        <div style="display:flex; align-items:center; gap:.4rem; flex-wrap:wrap;">
                            <span class="version-badge" style="font-size:.75rem; font-weight:700; color:#fff; background:#94A3B8;
                                         padding:.2rem .55rem; border-radius:5px; letter-spacing:.04em;">
                                v{{ $revision->version ?: '–' }}
                            </span>
                            @if($hasPred)
                                <span style="display:inline-flex; align-items:center; padding:.25rem .4rem; border-radius:5px;
                                             background:var(--t-surface2); border:1px solid var(--t-border);" title="Ersetzt">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2.5">
                                        <polyline points="1 4 1 10 7 10"/>
                                        <path d="M3.51 15a9 9 0 1 0 .49-4.78"/>
                                    </svg>
                                </span>
                            @endif
                            <span style="width:1px; height:14px; background:var(--t-border); flex-shrink:0;"></span>
                            @foreach($revision->typesList as $type)
                                @php $tc = $typeConfig[$type] ?? ['label' => $type, 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
                                <span class="rev-type-badge" data-type="{{ $type }}" style="display:inline-flex; align-items:center; padding:.15rem .5rem; border-radius:5px;
                                             font-size:.7rem; font-weight:600;
                                             background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                                    {{ $tc['label'] }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <button type="button" onclick="jumpToRevision({{ $revision->id }})"
                                style="background:none; border:none; padding:0; cursor:pointer; font-weight:600;
                                       color:var(--t-text); font-size:.875rem; text-align:left; font-family:inherit;
                                       text-decoration:underline; text-decoration-color:transparent; transition:text-decoration-color .15s;"
                                onmouseover="this.style.textDecorationColor='#06B6D4'; this.style.color='var(--c-accent1)';"
                                onmouseout="this.style.textDecorationColor='transparent'; this.style.color='var(--t-text)';">
                            {{ $revision->title }}
                        </button>
                    </td>
                    <td style="font-size:.82rem; color:var(--t-text-muted);">{{ $revision->created_at->format('d.m.Y H:i') }}</td>
                    <td style="font-size:.82rem; color:var(--t-text-muted);">{{ $authorName ?: '–' }}</td>
                    <td style="white-space:nowrap;">
                        <div style="display:flex; align-items:center; gap:.5rem; justify-content:flex-end;">
                            @if($hasPred)
                                <button type="button"
                                        onclick="togglePredecessorList('pred-list-{{ $revision->id }}')"
                                        title="Vorgänger anzeigen"
                                        style="padding:.3rem .45rem; background:none; border:1px solid #E2E8F0; border-radius:5px;
                                               cursor:pointer; color:#94A3B8; line-height:1; display:flex; align-items:center; gap:.3rem;
                                               font-size:.72rem; font-weight:600; transition:all .15s;"
                                        onmouseover="this.style.color='#B45309'; this.style.borderColor='#FCD34D'; this.style.background='#FFFBEB';"
                                        onmouseout="this.style.color='#94A3B8'; this.style.borderColor='#E2E8F0'; this.style.background='none';">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <polyline points="1 4 1 10 7 10"/>
                                        <path d="M3.51 15a9 9 0 1 0 .49-4.78"/>
                                    </svg>
                                    {{ count($predecessorChain) }}
                                </button>
                            @endif
                            @if($canEdit)
                                <a href="{{ route('revisions.replace', [$project, $revision]) }}"
                                   class="btn btn-ghost btn-sm">Ersetzen</a>
                            @endif
                        </div>
                    </td>
                </tr>
                {{-- Vorgänger-Zeilen --}}
                @if($hasPred)
                @foreach($predecessorChain as $pred)
                @php
                    $predAuthor = $pred->author ? trim($pred->author->vorname . ' ' . $pred->author->nachname) : '';
                @endphp
                <tr class="pred-list-row pred-list-{{ $revision->id }}" style="display:{{ $predExpanded ? 'table-row' : 'none' }}; background:rgba(245,158,11,.07);">
                    <td style="box-shadow:inset 3px 0 0 rgba(245,158,11,.5);">
                        <div style="display:flex; align-items:center; gap:.4rem; flex-wrap:wrap; padding-left:.25rem;">
                            <span style="font-size:.75rem; font-weight:700; color:#fff; background:#B45309;
                                         padding:.2rem .55rem; border-radius:5px; letter-spacing:.04em;">
                                v{{ $pred->version }}
                            </span>
                            <span style="width:1px; height:14px; background:rgba(245,158,11,.5); flex-shrink:0;"></span>
                            @foreach($pred->typesList as $type)
                                @php $tc = $typeConfig[$type] ?? ['label' => $type, 'bg' => 'rgba(148,163,184,.12)', 'color' => '#94A3B8', 'border' => 'rgba(148,163,184,.3)']; @endphp
                                <span class="rev-type-badge" data-type="{{ $type }}" style="display:inline-flex; align-items:center; padding:.15rem .5rem; border-radius:5px;
                                             font-size:.7rem; font-weight:600;
                                             background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                                    {{ $tc['label'] }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <button type="button"
                                onclick="jumpToPredecessor({{ $revision->id }}, {{ $pred->id }})"
                                style="background:none; border:none; padding:0; cursor:pointer; font-weight:600;
                                       color:var(--c-accent2); font-size:.875rem; text-align:left; font-family:inherit;
                                       text-decoration:underline; text-decoration-color:transparent; transition:text-decoration-color .15s;"
                                onmouseover="this.style.textDecorationColor='var(--c-accent2)';"
                                onmouseout="this.style.textDecorationColor='transparent';">
                            {{ $pred->title }}
                        </button>
                    </td>
                    <td style="font-size:.82rem; color:var(--c-accent2);">{{ $pred->created_at->format('d.m.Y H:i') }}</td>
                    <td style="font-size:.82rem; color:var(--c-accent2);">{{ $predAuthor ?: '–' }}</td>
                    <td></td>
                </tr>
                @endforeach
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

<script>
const SAVE_URL  = '{{ route('dashboard.preferences') }}';
const CSRF      = '{{ csrf_token() }}';
let currentView  = '{{ $savedView }}';
const activeTypes = new Set();

// ---- Jump from list predecessor to journal predecessor panel ----
function jumpToPredecessor(parentId, predId) {
    setView('journal');
    setTimeout(() => {
        // open predecessor panel if closed
        const panel = document.getElementById('pred-' + parentId);
        if (panel && panel.style.display === 'none') panel.style.display = 'block';

        // scroll to and highlight the specific predecessor entry
        const entry = document.getElementById('pred-entry-' + predId);
        if (entry) {
            entry.scrollIntoView({ behavior: 'smooth', block: 'center' });
            entry.style.position = 'relative';
            const ov = document.createElement('div');
            ov.style.cssText = 'position:absolute;inset:0;background:rgba(245,158,11,.45);pointer-events:none;';
            ov.classList.add('rev-overlay-pulse');
            entry.appendChild(ov);
            setTimeout(() => { ov.remove(); entry.style.position = ''; }, 1600);
        }
    }, 150);
}

// ---- Jump from list to journal ----
function jumpToRevision(id) {
    setView('journal');
    const target = document.getElementById('journal-rev-' + id);
    if (!target) return;
    setTimeout(() => {
        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const card = target.querySelector('.rev-card');
        if (card) {
            const predPanel = document.getElementById('pred-' + id);
            const predVisible = predPanel && predPanel.style.display !== 'none';
            const predH = predVisible ? predPanel.offsetHeight : 0;
            const ov = document.createElement('div');
            const br = predVisible ? '10px 10px 0 0' : '10px';
            ov.style.cssText = 'position:absolute;top:0;left:0;right:0;background:rgba(6,182,212,.38);pointer-events:none;border-radius:' + br + ';';
            ov.style.height = 'calc(100% - ' + predH + 'px)';
            ov.classList.add('rev-overlay-pulse');
            card.style.position = 'relative';
            card.appendChild(ov);
            setTimeout(() => { ov.remove(); card.style.position = ''; }, 1000);
        }
    }, 150);
}

// ---- Predecessor toggle (journal panel) ----
function togglePredecessor(id) {
    const el = document.getElementById(id);
    const hidden = el.style.display === 'none' || el.style.display === '';
    el.style.display = hidden ? 'block' : 'none';
}

// ---- Predecessor toggle (list rows) ----
function togglePredecessorList(cls) {
    const rows = document.querySelectorAll('.' + cls);
    const hidden = rows.length > 0 && (rows[0].style.display === 'none' || rows[0].style.display === '');
    rows.forEach(row => { row.style.display = hidden ? 'table-row' : 'none'; });
}

// ---- View toggle ----
function setView(v) {
    currentView = v;
    document.getElementById('view-journal').style.display = (v === 'journal') ? '' : 'none';
    document.getElementById('view-list').style.display    = (v === 'list')    ? 'block' : 'none';

    const btnJ = document.getElementById('btn-journal');
    const btnL = document.getElementById('btn-list');
    btnJ.style.background = (v === 'journal') ? 'var(--c-accent1)' : 'transparent';
    btnJ.style.color      = (v === 'journal') ? '#fff' : 'var(--t-text)';
    btnL.style.background = (v === 'list')    ? 'var(--c-accent1)' : 'transparent';
    btnL.style.color      = (v === 'list')    ? '#fff' : 'var(--t-text)';

    fetch(SAVE_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF},
        body: JSON.stringify({revision_view: v})
    });
}

// ---- Type filter (multi-select) ----
document.querySelectorAll('.type-filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if (btn.dataset.type === '') {
            activeTypes.clear();
        } else {
            if (activeTypes.has(btn.dataset.type)) activeTypes.delete(btn.dataset.type);
            else activeTypes.add(btn.dataset.type);
        }
        document.querySelectorAll('.type-filter-btn').forEach(b => {
            const isAll    = b.dataset.type === '';
            const isActive = isAll ? activeTypes.size === 0 : activeTypes.has(b.dataset.type);
            b.classList.toggle('active-filter', isActive);
            b.style.opacity = isActive ? '1' : '.65';
        });
        applyFilter();
    });
});

// ---- Search ----
document.getElementById('rev-search').addEventListener('input', applyFilter);

function applyFilter() {
    const q = document.getElementById('rev-search').value.trim().toLowerCase();
    let visible = 0;

    // Journal items
    document.querySelectorAll('#view-journal .rev-item').forEach(item => {
        const show = matchItem(item, q);
        item.style.display = show ? 'flex' : 'none';
        if (show) visible++;
    });

    // List items
    document.querySelectorAll('#view-list .rev-item').forEach(item => {
        const show = matchItem(item, q);
        item.style.display = show ? '' : 'none';
        // hide open predecessor rows when parent is hidden
        if (!show && item.dataset.predRow) {
            document.querySelectorAll('.' + item.dataset.predRow)
                .forEach(r => r.style.display = 'none');
        }
    });

    document.getElementById('no-results').style.display = (visible === 0) ? 'block' : 'none';
    updateJournalTimeline();
    updateListFirstBadge();
}

function matchItem(item, q) {
    // Multi-type: item must have at least one of the selected types
    const matchType = activeTypes.size === 0 ||
        item.dataset.types.split(' ').some(t => activeTypes.has(t));

    let matchSearch = true;
    if (q) {
        const ver = item.dataset.version.toLowerCase();
        const fields = [
            ver,
            'v' + ver,
            item.dataset.title,
            item.dataset.types,
            item.dataset.author,
            item.dataset.date,
            item.dataset.content,
        ].join(' ');

        if (q.includes('*')) {
            // * as prefix/separator → OR logic: show item if ANY term matches
            const terms = q.split('*').map(t => t.trim()).filter(Boolean);
            matchSearch = terms.some(term => fields.includes(term));
        } else {
            // Normal: space-separated terms, ALL must match (AND)
            const terms = q.trim().split(/\s+/).filter(Boolean);
            matchSearch = terms.every(term => fields.includes(term));
        }
    }

    return matchType && matchSearch;
}

function updateListFirstBadge() {
    const rows = [...document.querySelectorAll('#view-list .rev-item')]
        .filter(el => el.style.display !== 'none');
    rows.forEach((row, i) => {
        const badge = row.querySelector('.version-badge');
        if (badge) badge.style.background = (i === 0) ? 'var(--c-accent1)' : '#94A3B8';
    });
}

function updateJournalTimeline() {
    const items = [...document.querySelectorAll('#view-journal .rev-item')]
        .filter(el => el.style.display !== 'none');

    items.forEach((item, i) => {
        const topLine    = item.querySelector('.tl-top');
        const dot        = item.querySelector('.tl-dot');
        const bottomLine = item.querySelector('.tl-bottom');
        const card       = item.querySelector('.rev-card');
        const badge      = item.querySelector('.version-badge');

        const isDark   = document.documentElement.dataset.theme === 'dark';
        const lineClr  = isDark ? '#06B6D4'             : '#94A3B8';
        const dotBg    = isDark ? '#0B1520'             : '#fff';
        const dotBdr   = isDark ? '#06B6D4'             : '#CBD5E1';
        const cardBdr  = isDark ? 'rgba(6,182,212,.35)' : '#E2E8F0';

        topLine.style.background    = (i === 0)              ? 'transparent' : lineClr;
        bottomLine.style.background = (i === items.length-1) ? 'transparent' : lineClr;

        if (i === 0) {
            dot.style.background  = 'var(--c-accent1)';
            dot.style.borderColor = 'var(--c-accent1)';
            dot.style.boxShadow   = '0 0 0 3px rgba(6,182,212,.25)';
            card.style.borderLeft = '';
            card.style.boxShadow  = 'inset 3px 0 0 var(--c-accent1), ' + (isDark ? '0 2px 12px rgba(6,182,212,.08)' : '0 2px 8px rgba(0,0,0,.06)');
            badge.style.background = 'var(--c-accent1)';
        } else {
            dot.style.background  = dotBg;
            dot.style.borderColor = dotBdr;
            dot.style.boxShadow   = 'none';
            card.style.borderLeft = '';
            card.style.boxShadow  = 'inset 3px 0 0 rgba(6,182,212,.45)';
            badge.style.background = '#94A3B8';
        }
    });
}

// ---- Init ----
window.addEventListener('DOMContentLoaded', () => {
    const v = currentView;
    const btnJ = document.getElementById('btn-journal');
    const btnL = document.getElementById('btn-list');
    btnJ.style.background = (v === 'journal') ? 'var(--c-accent1)' : 'transparent';
    btnJ.style.color      = (v === 'journal') ? '#fff' : 'var(--t-text)';
    btnL.style.background = (v === 'list')    ? 'var(--c-accent1)' : 'transparent';
    btnL.style.color      = (v === 'list')    ? '#fff' : 'var(--t-text)';
    updateJournalTimeline();
    updateListFirstBadge();
});
</script>

<style>
.type-filter-btn.active-filter {
    opacity: 1 !important;
    box-shadow: 0 0 0 2px currentColor;
}
@keyframes revHighlight {
    0%   { outline: 3px solid rgba(6,182,212,0);    outline-offset: 0px; }
    15%  { outline: 3px solid rgba(6,182,212,.7);   outline-offset: 3px; }
    60%  { outline: 3px solid rgba(6,182,212,.5);   outline-offset: 3px; }
    100% { outline: 3px solid rgba(6,182,212,0);    outline-offset: 0px; }
}
.rev-highlight {
    animation: revHighlight 1.6s ease-out forwards;
    border-radius: 10px;
}
@keyframes predHighlight {
    0%,100%      { background: #FFFBEB; }
    10%, 43%     { background: #FDE68A; }
    27%, 60%     { background: #FFFBEB; }
    76%          { background: #FDE68A; }
}
.pred-highlight {
    animation: predHighlight 1.5s ease-in-out forwards;
}
@keyframes revOverlayPulse {
    0%,100% { opacity: 0; }
    15%, 65% { opacity: 1; }
    40%, 90% { opacity: 0; }
}
.rev-overlay-pulse {
    animation: revOverlayPulse 0.9s ease-in-out forwards;
}
@keyframes revCardHighlight {
    0%   { background: rgba(6,182,212,0); }
    15%  { background: rgba(6,182,212,.18); }
    60%  { background: rgba(6,182,212,.08); }
    100% { background: rgba(6,182,212,0); }
}
.rev-card-highlight {
    animation: revCardHighlight 1.6s ease-out forwards;
}
</style>

@endsection
