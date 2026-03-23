@extends('layouts.app')

@section('title', 'Revision ersetzen')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo;
<a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo;
Revision ersetzen
@endsection

@section('content')

<style>
    .replace-warn-banner {
        background: #FFFBEB;
        border: 1px solid #FCD34D;
        color: #92400E;
    }
    [data-theme="dark"] .replace-warn-banner {
        background: rgba(245,158,11,.1);
        border-color: rgba(245,158,11,.4);
        color: #FCD34D;
    }
    [data-theme="dark"] .existing-rev-header {
        background: var(--t-surface2) !important;
    }
    /* Type-add-btn dark mode */
    [data-theme="dark"] .type-add-btn[data-type="update"]      { background: rgba(59,130,246,.18)  !important; color: #93C5FD !important; border-color: rgba(59,130,246,.4)  !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="change"]      { background: rgba(234,179,8,.18)   !important; color: #FCD34D !important; border-color: rgba(234,179,8,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="fix"]         { background: rgba(239,68,68,.18)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="release"]     { background: rgba(34,197,94,.18)   !important; color: #4ADE80 !important; border-color: rgba(34,197,94,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="hotfix"]      { background: rgba(239,68,68,.18)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="deaktiviert"] { background: rgba(100,116,139,.18) !important; color: #94A3B8 !important; border-color: rgba(100,116,139,.4) !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="broken"]      { background: rgba(15,23,42,.6)     !important; color: #CBD5E1 !important; border-color: rgba(71,85,105,.7)   !important; opacity:1 !important; }

    .type-add-btn { position: relative; overflow: visible !important; display: inline-flex; align-items: center; gap: .35rem; }
    .type-hint-icon { position: relative; display: flex; align-items: center; opacity: .55; transition: opacity .15s; pointer-events: none; }
    .type-hint-tooltip {
        position: absolute;
        bottom: calc(100% + 7px); left: -4px; transform: none;
        background: var(--c-primary); color: #CBD5E1;
        font-size: .72rem; font-weight: 400; line-height: 1.5;
        padding: .5rem .75rem; border-radius: 7px;
        width: 210px; text-align: left; white-space: normal;
        z-index: 200; pointer-events: none;
        box-shadow: 0 4px 16px rgba(0,0,0,.25);
        border: 1px solid rgba(6,182,212,.2);
        opacity: 0; visibility: hidden;
        transition: opacity .2s, visibility .2s;
        transition-delay: 0s;
    }
    .type-hint-tooltip::before {
        content: ''; position: absolute;
        bottom: -10px; left: 5px;
        border: 5px solid transparent;
        border-top-color: var(--c-primary);
    }
    .type-add-btn:hover .type-hint-icon { opacity: 1; }
    .type-add-btn:hover .type-hint-tooltip {
        opacity: 1; visibility: visible;
        transition-delay: .7s;
    }
</style>

@php
$typeConfig = [
    'release'     => ['label' => 'Release',        'bg' => '#DCFCE7', 'color' => '#15803D', 'border' => '#86EFAC', 'hint' => 'Für neue Versionen oder größere Veröffentlichungen'],
    'update'      => ['label' => 'Update',         'bg' => '#DBEAFE', 'color' => '#1D4ED8', 'border' => '#93C5FD', 'hint' => 'Für Verbesserungen oder Erweiterungen vorhandener Funktionen'],
    'change'      => ['label' => 'Änderung',       'bg' => '#FEF3C7', 'color' => '#B45309', 'border' => '#FCD34D', 'hint' => 'Für Änderungen am bestehenden Verhalten, Design oder Ablauf'],
    'fix'         => ['label' => 'Fehlerbehebung', 'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5', 'hint' => 'Für behobene Fehler oder Korrekturen'],
    'hotfix'      => ['label' => 'Hotfix',         'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5', 'hint' => 'Für dringende Korrekturen, die sofort eingespielt werden müssen'],
    'deaktiviert' => ['label' => 'Deaktiviert',    'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1', 'hint' => 'Für Funktionen oder Bereiche, die vorübergehend deaktiviert wurden'],
    'broken'      => ['label' => 'Broken',         'bg' => '#1E293B', 'color' => '#F1F5F9', 'border' => '#475569', 'hint' => 'Für bekannte, noch nicht behobene Fehler oder ausgefallene Funktionen'],
];
@endphp

{{-- Hinweis --}}
<div class="replace-warn-banner" style="border-radius:8px; padding:.85rem 1.25rem; margin-bottom:1.25rem; font-size:.875rem; display:flex; align-items:flex-start; gap:.6rem;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    Die bestehende Revision <strong>v{{ $revision->version }}</strong> wird als ersetzt markiert und ist nicht mehr aktiv.
    Übernommene Einträge können als <strong>gestrichen</strong> markiert, aber nicht gelöscht werden — für eine lückenlose Nachvollziehbarkeit.
</div>

{{-- Bestehende Revision (readonly) --}}
<div class="card" style="margin-bottom:1.5rem; border-left:3px solid var(--t-input-border);">
    <div class="card-header existing-rev-header" style="background:#F8FAFC;">
        <h2 style="font-size:.85rem; color:var(--t-text-sub); text-transform:uppercase; letter-spacing:.06em;">Bestehende Revision — v{{ $revision->version }}</h2>
        <span style="font-size:.82rem; color:var(--t-text-sub);">{{ $revision->created_at->format('d.m.Y H:i') }}</span>
    </div>
    <div class="card-body">
        <div style="font-weight:600; color:var(--t-text-muted); margin-bottom:.75rem;">{{ $revision->title }}</div>
        <div style="display:flex; flex-direction:column; gap:.5rem;">
            @foreach($revision->entries as $entry)
            @php $tc = $typeConfig[$entry['type']] ?? ['label' => $entry['type'], 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
            <div style="display:flex; gap:.75rem; align-items:flex-start;">
                <span class="rev-type-badge" data-type="{{ $entry['type'] }}"
                      style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                             font-size:.72rem; font-weight:600; flex-shrink:0; margin-top:.1rem;
                             background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                    {{ $tc['label'] }}
                </span>
                <span style="font-size:.85rem; color:var(--t-text-muted); line-height:1.6;">{{ $entry['content'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Neue Revision --}}
<div class="card">
    <div class="card-header">
        <h2>Neue Revision (Ersatz)</h2>
        <div style="display:flex; align-items:center; gap:.75rem;">
            <span style="font-size:.8rem; color:var(--t-text-sub);">Version:</span>
            <span style="font-size:.95rem; font-weight:700; color:var(--c-accent1);">v{{ $nextVersion }}</span>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Abbrechen</a>
        </div>
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

            {{-- Typ-Badges --}}
            <div class="form-group">
                <label class="form-label">Typ hinzufügen</label>
                <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.35rem;">
                    @foreach($typeConfig as $typeKey => $cfg)
                    <button type="button" class="type-add-btn"
                            data-type="{{ $typeKey }}" data-label="{{ $cfg['label'] }}"
                            data-bg="{{ $cfg['bg'] }}" data-color="{{ $cfg['color'] }}" data-border="{{ $cfg['border'] }}"
                            style="padding:.3rem .75rem; border-radius:6px; border:1px solid {{ $cfg['border'] }};
                                   background:{{ $cfg['bg'] }}; cursor:pointer; font-size:.78rem; font-weight:600;
                                   color:{{ $cfg['color'] }}; transition:opacity .15s;">
                        + {{ $cfg['label'] }}
                        <span class="type-hint-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span class="type-hint-tooltip">{{ $cfg['hint'] }}</span>
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>

            <div id="entries-container" style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1rem;"></div>
            <div id="empty-hint" style="padding:1.5rem; border:2px dashed var(--t-border); border-radius:8px;
                                         text-align:center; color:var(--t-text-sub); font-size:.85rem; margin-bottom:1rem;">
                Klicke oben auf einen Typ, um Inhalt hinzuzufügen.
            </div>

            @error('entries')<div class="form-error" style="margin-bottom:.75rem;">{{ $message }}</div>@enderror

            <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Revision ersetzen</button>
            </div>
        </form>
    </div>
</div>

<script>
let entryIndex = 0;
const predecessorVersion = '{{ $revision->version }}';
const oldEntries = @json($revision->entries);
const typeConfig  = @json($typeConfig);

window.addEventListener('DOMContentLoaded', () => {
    // Pre-fill with inherited entries (not deletable, only strikeable)
    oldEntries.forEach(e => {
        const tc = typeConfig[e.type] ?? {label: e.type, bg: '#F1F5F9', color: '#64748B', border: '#CBD5E1'};
        addEntry(e.type, tc.label, tc.bg, tc.color, tc.border, e.content, true);
    });

    document.querySelectorAll('.type-add-btn').forEach(btn => {
        btn.addEventListener('mouseover', () => btn.style.opacity = '.75');
        btn.addEventListener('mouseout',  () => btn.style.opacity = '1');
        btn.addEventListener('click', () => addEntry(btn.dataset.type, btn.dataset.label, btn.dataset.bg, btn.dataset.color, btn.dataset.border));
    });
});

function addEntry(type, label, bg, color, border, prefill = '', isInherited = false) {
    document.getElementById('empty-hint').style.display = 'none';
    const idx = entryIndex++;
    const div = document.createElement('div');
    div.className = 'entry-row';
    div.style.cssText = `display:flex; gap:.75rem; align-items:flex-start; padding:.85rem 1rem; background:var(--t-surface2); border:1px solid var(--t-border); border-radius:8px; border-left:3px solid ${border};`;
    div.dataset.origBorder = border;

    const inheritedLabel = isInherited
        ? `<span style="font-size:.68rem; color:var(--t-text-sub); display:block; margin-top:.3rem; white-space:nowrap;">aus v${predecessorVersion}</span>`
        : '';

    const actionBtn = isInherited
        ? `<input type="hidden" name="entries[${idx}][removed]" value="0" class="removed-flag">
           <button type="button" onclick="toggleRemoved(this)"
                   style="padding:.3rem .55rem; background:none; border:1px solid var(--t-border); border-radius:5px;
                          cursor:pointer; color:var(--t-text-sub); flex-shrink:0; font-size:.72rem; white-space:nowrap; line-height:1.4;"
                   onmouseover="if(this.dataset.removed!=='1'){this.style.color='var(--c-accent2)';this.style.borderColor='var(--c-accent2)';}"
                   onmouseout="if(this.dataset.removed!=='1'){this.style.color='var(--t-text-sub)';this.style.borderColor='var(--t-border)';}">
               Streichen
           </button>`
        : `<button type="button" onclick="removeEntry(this)"
                   style="padding:.3rem .45rem; background:none; border:1px solid var(--t-border); border-radius:5px;
                          cursor:pointer; color:var(--t-text-sub); flex-shrink:0; line-height:1;"
                   onmouseover="this.style.color='#DC2626'; this.style.borderColor='#FCA5A5';"
                   onmouseout="this.style.color='var(--t-text-sub)'; this.style.borderColor='var(--t-border)';">✕</button>`;

    div.innerHTML = `
        <div style="padding-top:.3rem; flex-shrink:0; min-width:110px;">
            <span class="rev-type-badge" data-type="${type}"
                  style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                         font-size:.72rem; font-weight:600; background:${bg}; color:${color}; border:1px solid ${border};">${label}</span>
            ${inheritedLabel}
        </div>
        <input type="hidden" name="entries[${idx}][type]" value="${type}">
        <textarea name="entries[${idx}][content]" rows="3" required
                  style="flex:1; padding:.5rem .75rem; border:1px solid var(--t-input-border); border-radius:6px;
                         font-size:.875rem; color:var(--t-input-text); background:var(--t-input-bg); resize:vertical; outline:none; font-family:inherit;"
                  placeholder="${label}: Beschreibe hier die Änderungen…"
                  onfocus="this.style.borderColor='var(--c-accent1)'"
                  onblur="this.style.borderColor='var(--t-input-border)'">${prefill}</textarea>
        ${actionBtn}
    `;
    document.getElementById('entries-container').appendChild(div);
}

function toggleRemoved(btn) {
    const row  = btn.closest('.entry-row');
    const flag = row.querySelector('.removed-flag');
    const ta   = row.querySelector('textarea');
    const isNowRemoved = flag.value !== '1';

    flag.value          = isNowRemoved ? '1' : '0';
    btn.dataset.removed = isNowRemoved ? '1' : '0';

    if (isNowRemoved) {
        row.style.background     = 'rgba(239,68,68,.07)';
        row.style.borderColor    = 'rgba(239,68,68,.2)';
        row.style.borderLeftColor= 'rgba(239,68,68,.45)';
        ta.style.textDecoration  = 'line-through';
        ta.style.textDecorationColor = 'rgba(239,68,68,.6)';
        ta.style.color           = 'var(--t-text-sub)';
    } else {
        row.style.background     = 'var(--t-surface2)';
        row.style.borderColor    = 'var(--t-border)';
        row.style.borderLeftColor= row.dataset.origBorder || 'var(--t-border)';
        ta.style.textDecoration  = '';
        ta.style.textDecorationColor = '';
        ta.style.color           = 'var(--t-input-text)';
    }

    btn.textContent       = isNowRemoved ? 'Wiederherstellen' : 'Streichen';
    btn.style.color       = isNowRemoved ? '#DC2626' : 'var(--t-text-sub)';
    btn.style.borderColor = isNowRemoved ? 'rgba(239,68,68,.4)' : 'var(--t-border)';

    const container = document.getElementById('entries-container');
    if (isNowRemoved) {
        container.appendChild(row);
    } else {
        const firstRemoved = [...container.querySelectorAll('.entry-row')]
            .find(r => r.querySelector('.removed-flag')?.value === '1');
        firstRemoved ? container.insertBefore(row, firstRemoved) : container.appendChild(row);
    }
}

function removeEntry(btn) {
    btn.closest('.entry-row').remove();
    if (!document.querySelector('.entry-row')) {
        document.getElementById('empty-hint').style.display = 'block';
    }
}
</script>
@endsection
