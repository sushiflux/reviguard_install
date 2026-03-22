@extends('layouts.app')

@section('title', 'Neue Revision')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo;
<a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a> &rsaquo;
Neue Revision
@endsection

@section('content')
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
                        'update'  => ['label' => 'Aktualisierung', 'class' => 'badge-blue'],
                        'change'  => ['label' => 'Änderung',       'class' => 'badge-amber'],
                        'fix'     => ['label' => 'Fehlerbehebung', 'class' => 'badge-red'],
                        'release' => ['label' => 'Release',        'class' => 'badge-green'],
                        'hotfix'  => ['label' => 'Hotfix',         'class' => 'badge-red'],
                    ] as $typeKey => $cfg)
                    <button type="button" class="type-add-btn"
                            data-type="{{ $typeKey }}" data-label="{{ $cfg['label'] }}" data-badge="{{ $cfg['class'] }}"
                            style="padding:.3rem .85rem; border-radius:999px; border:2px dashed #CBD5E1;
                                   background:transparent; cursor:pointer; font-size:.78rem; font-weight:600; color:#475569;">
                        + {{ $cfg['label'] }}
                    </button>
                    @endforeach
                </div>
                <div style="font-size:.75rem; color:#94A3B8; margin-top:.4rem;">Klicke einen Typ an, um einen Inhaltseintrag hinzuzufügen. Mehrere Typen möglich.</div>
            </div>

            {{-- Einträge --}}
            <div id="entries-container" style="display:flex; flex-direction:column; gap:.75rem; margin-bottom:1rem;"></div>

            <div id="empty-hint" style="padding:1.5rem; border:2px dashed #E2E8F0; border-radius:8px;
                                         text-align:center; color:#94A3B8; font-size:.85rem; margin-bottom:1rem;">
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
    btn.addEventListener('mouseover', () => { btn.style.borderColor = 'var(--c-accent1)'; btn.style.color = 'var(--c-accent1)'; });
    btn.addEventListener('mouseout',  () => { btn.style.borderColor = '#CBD5E1'; btn.style.color = '#475569'; });
    btn.addEventListener('click', () => addEntry(btn.dataset.type, btn.dataset.label, btn.dataset.badge));
});

function addEntry(type, label, badgeClass) {
    document.getElementById('empty-hint').style.display = 'none';
    const idx = entryIndex++;
    const div = document.createElement('div');
    div.className = 'entry-row';
    div.style.cssText = 'display:flex; gap:.75rem; align-items:flex-start; padding:.85rem 1rem; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; border-left:3px solid var(--c-accent1);';
    div.innerHTML = `
        <div style="padding-top:.3rem; flex-shrink:0; min-width:110px;">
            <span class="badge ${badgeClass}">${label}</span>
        </div>
        <input type="hidden" name="entries[${idx}][type]" value="${type}">
        <textarea name="entries[${idx}][content]" rows="3" required
                  style="flex:1; padding:.5rem .75rem; border:1px solid #CBD5E1; border-radius:6px;
                         font-size:.875rem; color:#1E293B; resize:vertical; outline:none; font-family:inherit;"
                  placeholder="${label}: Beschreibe hier die Änderungen…"
                  onfocus="this.style.borderColor='var(--c-accent1)'"
                  onblur="this.style.borderColor='#CBD5E1'"></textarea>
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
