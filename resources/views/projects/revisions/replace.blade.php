@extends('layouts.app')

@section('title', 'Revision ersetzen')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo;
<a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo;
Revision ersetzen
@endsection

@section('content')

@php
$typeConfig = [
    'update'  => ['label' => 'Update', 'bg' => '#DBEAFE', 'color' => '#1D4ED8', 'border' => '#93C5FD'],
    'change'  => ['label' => 'Änderung',       'bg' => '#FEF3C7', 'color' => '#B45309', 'border' => '#FCD34D'],
    'fix'     => ['label' => 'Fehlerbehebung', 'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5'],
    'release' => ['label' => 'Release',        'bg' => '#DCFCE7', 'color' => '#15803D', 'border' => '#86EFAC'],
    'hotfix'  => ['label' => 'Hotfix',         'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5'],
];
@endphp

{{-- Hinweis --}}
<div style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; padding:.85rem 1.25rem; margin-bottom:1.25rem; color:#92400E; font-size:.875rem; display:flex; align-items:flex-start; gap:.6rem;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0; margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    Die bestehende Revision <strong>v{{ $revision->version }}</strong> wird als ersetzt markiert und ist nicht mehr aktiv.
</div>

{{-- Bestehende Revision (readonly) --}}
<div class="card" style="margin-bottom:1.5rem; border-left:3px solid #CBD5E1;">
    <div class="card-header" style="background:#F8FAFC;">
        <h2 style="font-size:.85rem; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em;">Bestehende Revision — v{{ $revision->version }}</h2>
        <span style="font-size:.82rem; color:#94A3B8;">{{ $revision->created_at->format('d.m.Y H:i') }}</span>
    </div>
    <div class="card-body">
        <div style="font-weight:600; color:#64748B; margin-bottom:.75rem;">{{ $revision->title }}</div>
        <div style="display:flex; flex-direction:column; gap:.5rem;">
            @foreach($revision->entries as $entry)
            @php $tc = $typeConfig[$entry['type']] ?? ['label' => $entry['type'], 'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1']; @endphp
            <div style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                             font-size:.72rem; font-weight:600; flex-shrink:0; margin-top:.1rem;
                             background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border:1px solid {{ $tc['border'] }};">
                    {{ $tc['label'] }}
                </span>
                <span style="font-size:.85rem; color:#64748B; line-height:1.6;">{{ $entry['content'] }}</span>
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
            <span style="font-size:.8rem; color:#94A3B8;">Version:</span>
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
                            style="padding:.3rem .85rem; border-radius:6px; border:1px solid {{ $cfg['border'] }};
                                   background:{{ $cfg['bg'] }}; cursor:pointer; font-size:.78rem; font-weight:600;
                                   color:{{ $cfg['color'] }}; transition:opacity .15s;">
                        + {{ $cfg['label'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div id="entries-container" style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1rem;"></div>
            <div id="empty-hint" style="padding:1.5rem; border:2px dashed #E2E8F0; border-radius:8px;
                                         text-align:center; color:#94A3B8; font-size:.85rem; margin-bottom:1rem;">
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

// Pre-fill with old revision entries
const oldEntries = @json($revision->entries);
const typeConfig  = @json($typeConfig);

window.addEventListener('DOMContentLoaded', () => {
    oldEntries.forEach(e => {
        const tc = typeConfig[e.type] ?? {label: e.type, bg: '#F1F5F9', color: '#64748B', border: '#CBD5E1'};
        addEntry(e.type, tc.label, tc.bg, tc.color, tc.border, e.content);
    });

    document.querySelectorAll('.type-add-btn').forEach(btn => {
        btn.addEventListener('mouseover', () => btn.style.opacity = '.75');
        btn.addEventListener('mouseout',  () => btn.style.opacity = '1');
        btn.addEventListener('click', () => addEntry(btn.dataset.type, btn.dataset.label, btn.dataset.bg, btn.dataset.color, btn.dataset.border));
    });
});

function addEntry(type, label, bg, color, border, prefill = '') {
    document.getElementById('empty-hint').style.display = 'none';
    const idx = entryIndex++;
    const div = document.createElement('div');
    div.className = 'entry-row';
    div.style.cssText = `display:flex; gap:.75rem; align-items:flex-start; padding:.85rem 1rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; border-left:3px solid ${border};`;
    div.innerHTML = `
        <div style="padding-top:.3rem; flex-shrink:0; min-width:110px;">
            <span style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                         font-size:.72rem; font-weight:600; background:${bg}; color:${color}; border:1px solid ${border};">${label}</span>
        </div>
        <input type="hidden" name="entries[${idx}][type]" value="${type}">
        <textarea name="entries[${idx}][content]" rows="3" required
                  style="flex:1; padding:.5rem .75rem; border:1px solid #CBD5E1; border-radius:6px;
                         font-size:.875rem; color:#1E293B; resize:vertical; outline:none; font-family:inherit;"
                  placeholder="${label}: Beschreibe hier die Änderungen…"
                  onfocus="this.style.borderColor='var(--c-accent1)'"
                  onblur="this.style.borderColor='#CBD5E1'">${prefill}</textarea>
        <button type="button" onclick="removeEntry(this)"
                style="padding:.3rem .45rem; background:none; border:1px solid #E2E8F0; border-radius:5px;
                       cursor:pointer; color:#94A3B8; flex-shrink:0; line-height:1;"
                onmouseover="this.style.color='#DC2626'; this.style.borderColor='#FCA5A5';"
                onmouseout="this.style.color='#94A3B8'; this.style.borderColor='#E2E8F0';">✕</button>
    `;
    document.getElementById('entries-container').appendChild(div);
}

function removeEntry(btn) {
    btn.closest('.entry-row').remove();
    if (!document.querySelector('.entry-row')) {
        document.getElementById('empty-hint').style.display = 'block';
    }
}
</script>
@endsection
