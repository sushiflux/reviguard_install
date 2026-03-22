@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Toolbar --}}
<div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem; flex-wrap:wrap;">

    {{-- Suche --}}
    <div style="position:relative; flex:1; min-width:180px; max-width:360px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2"
             style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%); pointer-events:none;">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input id="search" type="text" placeholder="Projekte suchen…"
               style="width:100%; padding:.5rem .75rem .5rem 2.25rem; border:1px solid #E2E8F0; border-radius:6px; font-size:.875rem; outline:none; box-sizing:border-box;"
               oninput="filterProjects()">
    </div>

    {{-- Spacer --}}
    <div style="flex:1;"></div>

    {{-- Sortierung --}}
    <div style="display:flex; gap:.375rem;">
        <button id="btn-az" onclick="setSort('az')" class="btn btn-ghost btn-sm" style="font-size:.8rem;">A → Z</button>
        <button id="btn-za" onclick="setSort('za')" class="btn btn-ghost btn-sm" style="font-size:.8rem;">Z → A</button>
    </div>

    {{-- View-Toggle --}}
    <div style="display:flex; gap:.25rem; border:1px solid #E2E8F0; border-radius:6px; padding:2px; background:#F8FAFC;">
        <button id="btn-tile" onclick="setView('tile')" title="Kachelansicht"
                style="padding:.35rem .55rem; border:none; border-radius:5px; cursor:pointer; background:transparent; display:flex; align-items:center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
        </button>
        <button id="btn-list" onclick="setView('list')" title="Listenansicht"
                style="padding:.35rem .55rem; border:none; border-radius:5px; cursor:pointer; background:transparent; display:flex; align-items:center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Neues Projekt --}}
    @if(auth()->user()->canCreateProjects())
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Neues Projekt
        </a>
    @endif
</div>

{{-- Kein Projekt --}}
@if($projects->isEmpty())
    <div class="card">
        <div class="card-body" style="text-align:center; padding:4rem; color:#94A3B8;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
            <div style="font-weight:600; color:#64748B;">Keine Projekte verfügbar</div>
            @if(auth()->user()->canCreateProjects())
                <div style="font-size:.82rem; margin-top:.5rem;">
                    <a href="{{ route('projects.create') }}" style="color:var(--c-accent1);">Erstes Projekt anlegen</a>
                </div>
            @endif
        </div>
    </div>
@else

    {{-- Keine Treffer --}}
    <div id="no-results" style="display:none; text-align:center; padding:3rem; color:#94A3B8; font-size:.875rem;">
        Keine Projekte gefunden.
    </div>

    {{-- Kachelansicht --}}
    <div id="view-tile" style="display:grid; grid-template-columns:repeat(5,1fr); gap:1.1rem;">
        @foreach($projects as $project)
        <div class="proj-card"
             data-name="{{ strtolower($project->name) }}"
             style="border:1px solid #E2E8F0; border-radius:10px; background:#fff;
                    border-top:3px solid {{ $project->is_active ? 'var(--c-accent1)' : '#CBD5E1' }};
                    transition:box-shadow .15s; display:flex; flex-direction:column;"
             onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
             onmouseout="this.style.boxShadow='none'">
            <div style="padding:1.1rem 1.1rem .75rem;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:.6rem;">
                    <div style="font-size:.95rem; font-weight:700; color:#1E293B; line-height:1.3;">{{ $project->name }}</div>
                    @if($project->is_active)
                        <span class="badge badge-green" style="margin-left:.5rem; flex-shrink:0;">Aktiv</span>
                    @else
                        <span class="badge badge-gray" style="margin-left:.5rem; flex-shrink:0;">Inaktiv</span>
                    @endif
                </div>
                <div style="font-size:.78rem; color:#64748B; line-height:1.5; flex:1;">
                    {{ $project->description ? Str::limit($project->description, 80) : '–' }}
                </div>
            </div>
            <div style="margin-top:auto; padding:.65rem 1.1rem; border-top:1px solid #F1F5F9;
                        display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:.72rem; color:#94A3B8;">
                    {{ $project->created_at->format('d.m.Y') }}
                </div>
                <div style="display:flex; gap:.35rem;">
                    <a href="#" class="btn btn-ghost btn-sm" style="padding:.25rem .6rem; font-size:.75rem;">Öffnen</a>
                    @if(auth()->user()->canCreateProjects())
                    <button onclick="confirmDelete({{ $project->id }}, '{{ addslashes($project->name) }}')"
                            class="btn btn-sm" style="padding:.25rem .6rem; font-size:.75rem; background:rgba(239,68,68,.08); color:#DC2626; border:1px solid rgba(239,68,68,.2);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Listenansicht --}}
    <div id="view-list" style="display:none;">
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Projektname</th>
                        <th>Beschreibung</th>
                        <th>Status</th>
                        <th>Erstellt</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="list-body">
                    @foreach($projects as $project)
                    <tr class="proj-row" data-name="{{ strtolower($project->name) }}">
                        <td style="font-weight:600; color:#1E293B;">{{ $project->name }}</td>
                        <td style="color:#64748B; font-size:.85rem;">{{ $project->description ? Str::limit($project->description, 80) : '–' }}</td>
                        <td>
                            @if($project->is_active)
                                <span class="badge badge-green">Aktiv</span>
                            @else
                                <span class="badge badge-gray">Inaktiv</span>
                            @endif
                        </td>
                        <td style="font-size:.82rem; color:#94A3B8; white-space:nowrap;">{{ $project->created_at->format('d.m.Y') }}</td>
                        <td style="text-align:right; white-space:nowrap;">
                            <a href="#" class="btn btn-ghost btn-sm">Öffnen</a>
                            @if(auth()->user()->canCreateProjects())
                            <button onclick="confirmDelete({{ $project->id }}, '{{ addslashes($project->name) }}')"
                                    class="btn btn-sm" style="margin-left:.35rem; background:rgba(239,68,68,.08); color:#DC2626; border:1px solid rgba(239,68,68,.2);">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                                </svg>
                                Löschen
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endif

<script>
let currentSort = '{{ auth()->user()->dashboard_sort }}';
let currentView = '{{ auth()->user()->dashboard_view }}';
const prefUrl   = '{{ route('dashboard.preferences') }}';
const csrfToken = '{{ csrf_token() }}';

function savePreferences(key, val) {
    fetch(prefUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ [key]: val }),
    });
}

function setSort(s) {
    currentSort = s;
    savePreferences('sort', s);
    applySort();
    updateSortButtons();
}

function setView(v) {
    currentView = v;
    savePreferences('view', v);
    applyView();
    updateViewButtons();
}

function filterProjects() {
    const q = document.getElementById('search').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.proj-card');
    const rows  = document.querySelectorAll('.proj-row');
    let visible = 0;

    cards.forEach(el => {
        const match = !q || el.dataset.name.includes(q);
        el.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    rows.forEach(el => {
        const match = !q || el.dataset.name.includes(q);
        el.style.display = match ? '' : 'none';
    });

    const nr = document.getElementById('no-results');
    if (nr) nr.style.display = (visible === 0 && q) ? 'block' : 'none';
}

function applySort() {
    // Kacheln
    const tileGrid = document.getElementById('view-tile');
    if (tileGrid) {
        const cards = [...tileGrid.querySelectorAll('.proj-card')];
        cards.sort((a, b) => currentSort === 'az'
            ? a.dataset.name.localeCompare(b.dataset.name)
            : b.dataset.name.localeCompare(a.dataset.name));
        cards.forEach(c => tileGrid.appendChild(c));
    }
    // Liste
    const listBody = document.getElementById('list-body');
    if (listBody) {
        const rows = [...listBody.querySelectorAll('.proj-row')];
        rows.sort((a, b) => currentSort === 'az'
            ? a.dataset.name.localeCompare(b.dataset.name)
            : b.dataset.name.localeCompare(a.dataset.name));
        rows.forEach(r => listBody.appendChild(r));
    }
}

function applyView() {
    const tile = document.getElementById('view-tile');
    const list = document.getElementById('view-list');
    if (!tile || !list) return;
    tile.style.display = currentView === 'tile' ? 'grid' : 'none';
    list.style.display = currentView === 'list' ? 'block' : 'none';
}

function updateSortButtons() {
    document.getElementById('btn-az')?.style && (
        document.getElementById('btn-az').style.background = currentSort === 'az' ? 'var(--c-accent1)' : '',
        document.getElementById('btn-az').style.color      = currentSort === 'az' ? '#fff' : '',
        document.getElementById('btn-za').style.background = currentSort === 'za' ? 'var(--c-accent1)' : '',
        document.getElementById('btn-za').style.color      = currentSort === 'za' ? '#fff' : ''
    );
}

function updateViewButtons() {
    ['tile','list'].forEach(v => {
        const btn = document.getElementById('btn-'+v);
        if (btn) {
            btn.style.background = currentView === v ? '#fff' : 'transparent';
            btn.style.boxShadow  = currentView === v ? '0 1px 3px rgba(0,0,0,.1)' : 'none';
            btn.style.color      = currentView === v ? 'var(--c-secondary)' : '#94A3B8';
        }
    });
}

// Init
applySort();
applyView();
updateSortButtons();
updateViewButtons();

@if(auth()->user()->canCreateProjects())
function confirmDelete(id, name) {
    document.getElementById('deleteProjectName').textContent = name;
    document.getElementById('deleteForm').action = '/projects/' + id;
    document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
}
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
@endif
</script>

@if(auth()->user()->canCreateProjects())
<div class="modal-backdrop" id="deleteModal">
    <div class="modal">
        <h3>Projekt löschen</h3>
        <p style="font-size:.875rem; color:#64748B; margin-bottom:1.25rem;">
            Soll das Projekt <strong id="deleteProjectName"></strong> wirklich gelöscht werden? Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">Abbrechen</button>
                <button type="submit" class="btn btn-danger">Löschen</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
