@extends('layouts.app')

@section('title', 'Neue Revision')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo;
<a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo;
Neue Revision
@endsection

@section('content')
<style>
    [data-theme="dark"] .type-add-btn[data-type="update"]      { background: rgba(59,130,246,.18)  !important; color: #93C5FD !important; border-color: rgba(59,130,246,.4)  !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="change"]      { background: rgba(234,179,8,.18)   !important; color: #FCD34D !important; border-color: rgba(234,179,8,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="fix"]         { background: rgba(239,68,68,.18)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="release"]     { background: rgba(34,197,94,.18)   !important; color: #4ADE80 !important; border-color: rgba(34,197,94,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="hotfix"]      { background: rgba(239,68,68,.18)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="deaktiviert"] { background: rgba(100,116,139,.18) !important; color: #94A3B8 !important; border-color: rgba(100,116,139,.4) !important; opacity:1 !important; }
    [data-theme="dark"] .type-add-btn[data-type="broken"]      { background: rgba(15,23,42,.6)     !important; color: #CBD5E1 !important; border-color: rgba(71,85,105,.7)   !important; opacity:1 !important; }

    .type-add-btn { position: relative; overflow: visible !important; display: inline-flex; align-items: center; gap: .35rem; }
    .type-hint-icon {
        position: relative;
        display: flex; align-items: center;
        opacity: .55; transition: opacity .15s;
        pointer-events: none;
    }
    .type-hint-tooltip {
        position: absolute;
        bottom: calc(100% + 7px);
        left: -4px;
        transform: none;
        background: var(--c-primary);
        color: #CBD5E1;
        font-size: .72rem; font-weight: 400; line-height: 1.5;
        padding: .5rem .75rem;
        border-radius: 7px;
        width: 210px;
        text-align: left;
        z-index: 200;
        pointer-events: none;
        box-shadow: 0 4px 16px rgba(0,0,0,.25);
        border: 1px solid rgba(6,182,212,.2);
        white-space: normal;
        opacity: 0;
        visibility: hidden;
        transition: opacity .2s, visibility .2s;
        transition-delay: 0s;
    }
    .type-hint-tooltip::before {
        content: '';
        position: absolute;
        bottom: -10px; left: 5px;
        border: 5px solid transparent;
        border-top-color: var(--c-primary);
    }
    .type-add-btn:hover .type-hint-icon { opacity: 1; }
    .type-add-btn:hover .type-hint-tooltip {
        opacity: 1;
        visibility: visible;
        transition-delay: .7s;
    }
</style>
<div class="card">
    <div class="card-header">
        <h2>Neue Revision anlegen</h2>
        <div style="display:flex; align-items:center; gap:.75rem;">
            <span style="font-size:.8rem; color:#94A3B8;">Version:</span>
            <span style="font-size:.95rem; font-weight:700; color:var(--c-accent1);">v{{ $nextVersion }}</span>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost btn-sm">Abbrechen</a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('revisions.store', $project) }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Titel *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required autofocus
                       placeholder="Kurze Zusammenfassung dieser Revision…">
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Typ-Badges zum Anklicken --}}
            <div class="form-group">
                <label class="form-label">Typ hinzufügen</label>
                <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.35rem;">
                    @foreach([
                        'release'     => ['label' => 'Release',        'bg' => '#DCFCE7', 'color' => '#15803D', 'border' => '#86EFAC', 'hint' => 'Für neue Versionen oder größere Veröffentlichungen'],
                        'update'      => ['label' => 'Update',         'bg' => '#DBEAFE', 'color' => '#1D4ED8', 'border' => '#93C5FD', 'hint' => 'Für Verbesserungen oder Erweiterungen vorhandener Funktionen'],
                        'change'      => ['label' => 'Änderung',       'bg' => '#FEF3C7', 'color' => '#B45309', 'border' => '#FCD34D', 'hint' => 'Für Änderungen am bestehenden Verhalten, Design oder Ablauf'],
                        'fix'         => ['label' => 'Fehlerbehebung', 'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5', 'hint' => 'Für behobene Fehler oder Korrekturen'],
                        'hotfix'      => ['label' => 'Hotfix',         'bg' => '#FEE2E2', 'color' => '#DC2626', 'border' => '#FCA5A5', 'hint' => 'Für dringende Korrekturen, die sofort eingespielt werden müssen'],
                        'deaktiviert' => ['label' => 'Deaktiviert',    'bg' => '#F1F5F9', 'color' => '#64748B', 'border' => '#CBD5E1', 'hint' => 'Für Funktionen oder Bereiche, die vorübergehend deaktiviert wurden'],
                        'broken'      => ['label' => 'Broken',         'bg' => '#1E293B', 'color' => '#F1F5F9', 'border' => '#475569', 'hint' => 'Für bekannte, noch nicht behobene Fehler oder ausgefallene Funktionen'],
                    ] as $typeKey => $cfg)
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
                <div style="font-size:.75rem; color:var(--t-text-sub); margin-top:.4rem;">Klicke einen Typ an, um einen Inhaltseintrag hinzuzufügen. Mehrere Typen möglich.</div>
            </div>

            {{-- Einträge --}}
            <div id="entries-container" style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1rem;"></div>

            <div id="empty-hint" style="padding:1.5rem; border:2px dashed var(--t-border); border-radius:8px;
                                         text-align:center; color:var(--t-text-sub); font-size:.85rem; margin-bottom:1rem;">
                Klicke oben auf einen Typ, um Inhalt hinzuzufügen.
            </div>

            @error('entries')<div class="form-error" style="margin-bottom:.75rem;">{{ $message }}</div>@enderror

            <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                <a href="{{ route('projects.show', $project) }}" class="btn btn-ghost">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Revision anlegen</button>
            </div>
        </form>
    </div>
</div>

<script>
let entryIndex = 0;

document.querySelectorAll('.type-add-btn').forEach(btn => {
    btn.addEventListener('mouseover', () => btn.style.opacity = '.75');
    btn.addEventListener('mouseout',  () => btn.style.opacity = '1');
    btn.addEventListener('click', () => addEntry(btn.dataset.type, btn.dataset.label, btn.dataset.bg, btn.dataset.color, btn.dataset.border));
});

function addEntry(type, label, bg, color, border) {
    document.getElementById('empty-hint').style.display = 'none';
    const idx = entryIndex++;
    const div = document.createElement('div');
    div.className = 'entry-row';
    div.style.cssText = `display:flex; gap:.75rem; align-items:flex-start; padding:.85rem 1rem; background:var(--t-surface2); border:1px solid var(--t-border); border-radius:8px; border-left:3px solid ${border};`;
    div.innerHTML = `
        <div style="padding-top:.3rem; flex-shrink:0; min-width:110px;">
            <span class="rev-type-badge" data-type="${type}"
                  style="display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px;
                         font-size:.72rem; font-weight:600; background:${bg}; color:${color}; border:1px solid ${border};">${label}</span>
        </div>
        <input type="hidden" name="entries[${idx}][type]" value="${type}">
        <textarea name="entries[${idx}][content]" rows="3" required
                  style="flex:1; padding:.5rem .75rem; border:1px solid var(--t-input-border); border-radius:6px;
                         font-size:.875rem; color:var(--t-input-text); background:var(--t-input-bg); resize:vertical; outline:none; font-family:inherit;"
                  placeholder="${label}: Beschreibe hier die Änderungen…"
                  onfocus="this.style.borderColor='var(--c-accent1)'"
                  onblur="this.style.borderColor='var(--t-input-border)'"></textarea>
        <button type="button" onclick="removeEntry(this)"
                style="padding:.3rem .45rem; background:none; border:1px solid var(--t-border); border-radius:5px;
                       cursor:pointer; color:var(--t-text-sub); flex-shrink:0; line-height:1;"
                onmouseover="this.style.color='#DC2626'; this.style.borderColor='#FCA5A5';"
                onmouseout="this.style.color='var(--t-text-sub)'; this.style.borderColor='var(--t-border)';">✕</button>
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
