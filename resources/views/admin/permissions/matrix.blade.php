@extends('layouts.app')

@section('title', 'Berechtigungsmatrix')
@section('breadcrumb', 'Berechtigungsmatrix')

@push('styles')
<style>
    .matrix-wrap { overflow-x: auto; }

    .matrix-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: .82rem;
    }

    /* Sticky User-Spalte links */
    .matrix-table .col-user {
        position: sticky; left: 0; z-index: 2;
        background: #fff;
        min-width: 200px;
        border-right: 2px solid #E2E8F0;
    }
    .matrix-table thead .col-user { background: #F8FAFC; z-index: 3; }

    .matrix-table th {
        padding: .65rem .9rem;
        text-align: center;
        font-size: .7rem; font-weight: 700;
        letter-spacing: .06em; text-transform: uppercase;
        color: var(--c-muted);
        background: #F8FAFC;
        border-bottom: 2px solid #E2E8F0;
        white-space: nowrap;
    }

    .matrix-table th.col-user { text-align: left; }

    .matrix-table td {
        padding: .5rem .75rem;
        border-bottom: 1px solid #F1F5F9;
        text-align: center;
        vertical-align: middle;
    }

    .matrix-table tbody tr:hover td { background: #F8FAFC; }
    .matrix-table tbody tr:hover td.col-user { background: #F1F5F9; }

    /* User-Zelle */
    .user-cell {
        display: flex; align-items: center; gap: .6rem;
        text-align: left;
    }

    .user-avatar {
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
        display: flex; align-items: center; justify-content: center;
        font-size: .7rem; font-weight: 700; color: #fff;
    }

    .user-info .uname  { font-weight: 600; color: #1E293B; }
    .user-info .groles { font-size: .7rem; color: var(--c-muted); margin-top: .1rem; }

    /* Rollen-Zelle */
    .role-cell {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .3rem .65rem;
        border-radius: 999px;
        font-size: .72rem; font-weight: 600;
        cursor: pointer;
        transition: transform .1s, box-shadow .1s;
        white-space: nowrap;
        border: 1px solid transparent;
    }
    .role-cell:hover { transform: scale(1.05); box-shadow: 0 2px 8px rgba(0,0,0,.1); }

    .role-projektleiter_admin { background: rgba(245,158,11,.15); color: #B45309; border-color: rgba(245,158,11,.3); }
    .role-projektleiter       { background: rgba(30,64,175,.12);  color: #1D4ED8; border-color: rgba(30,64,175,.2); }
    .role-editor              { background: rgba(6,182,212,.12);  color: #0891B2; border-color: rgba(6,182,212,.2); }
    .role-viewer              { background: rgba(100,116,139,.1); color: #475569; border-color: rgba(100,116,139,.2); }

    /* Leere Zelle */
    .empty-cell {
        width: 28px; height: 28px;
        border-radius: 50%;
        border: 1.5px dashed #CBD5E1;
        cursor: pointer;
        margin: 0 auto;
        display: flex; align-items: center; justify-content: center;
        color: #CBD5E1;
        transition: border-color .15s, color .15s;
    }
    .empty-cell:hover { border-color: var(--c-accent1); color: var(--c-accent1); }

    /* Projekt-Header */
    .proj-header {
        max-width: 120px;
        white-space: normal;
        word-break: break-word;
        text-align: center;
    }

    /* Legende */
    .legend {
        display: flex; flex-wrap: wrap; gap: .5rem;
        margin-bottom: 1.25rem;
    }

    .legend-item {
        display: flex; align-items: center; gap: .4rem;
        padding: .25rem .6rem;
        background: #F8FAFC; border: 1px solid #E2E8F0;
        border-radius: 6px; font-size: .75rem; color: #475569;
    }

    .legend-dot {
        width: 10px; height: 10px; border-radius: 50%;
    }

    /* Modal */
    .role-option {
        display: flex; align-items: center;
        padding: .65rem .875rem;
        border-radius: 8px;
        border: 1.5px solid #E2E8F0;
        cursor: pointer;
        margin-bottom: .5rem;
        transition: border-color .15s, background .15s;
        gap: .75rem;
    }
    .role-option:hover        { border-color: var(--c-accent1); background: rgba(6,182,212,.04); }
    .role-option.selected     { border-color: var(--c-accent1); background: rgba(6,182,212,.08); }
    .role-option .role-name   { font-weight: 600; font-size: .875rem; color: #1E293B; }
    .role-option .role-desc   { font-size: .75rem; color: var(--c-muted); margin-top: .1rem; }
    .role-option .role-icon   { font-size: 1.1rem; }
</style>
@endpush

@section('content')

{{-- Legende --}}
<div class="legend">
    @foreach($projectRoles as $r)
    <div class="legend-item">
        <div class="legend-dot role-{{ $r->name }}" style="
            @if($r->name === 'projektleiter_admin') background:#F59E0B;
            @elseif($r->name === 'projektleiter')   background:#1E40AF;
            @elseif($r->name === 'editor')          background:#06B6D4;
            @else                                   background:#94A3B8;
            @endif
        "></div>
        {{ $r->display_name }}
    </div>
    @endforeach
    <div class="legend-item" style="margin-left:auto;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Zelle anklicken um Berechtigung zu vergeben / ändern
    </div>
</div>

@if($projects->isEmpty())
<div class="card">
    <div class="card-body" style="text-align:center; padding:4rem; color:#94A3B8;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
        </svg>
        <div style="font-weight:600; color:#64748B;">Noch keine Projekte vorhanden</div>
        <div style="font-size:.82rem; margin-top:.4rem;">Erstelle zuerst Projekte um die Berechtigungsmatrix zu nutzen.</div>
    </div>
</div>
@else

<div class="card">
    <div class="card-header">
        <h2>Berechtigungsmatrix</h2>
        <span style="font-size:.78rem; color:var(--c-muted);">
            {{ $users->count() }} User &times; {{ $projects->count() }} Projekte
        </span>
    </div>

    <div class="matrix-wrap">
        <table class="matrix-table">
            <thead>
                <tr>
                    <th class="col-user">Benutzer</th>
                    @foreach($projects as $project)
                    <th>
                        <div class="proj-header">
                            {{ $project->name }}
                            @if(!$project->is_active)
                                <div style="font-size:.65rem; color:#EF4444; font-weight:600;">inaktiv</div>
                            @endif
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td class="col-user">
                        <div class="user-cell">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->username, 0, 2)) }}
                            </div>
                            <div class="user-info">
                                <div class="uname">{{ $user->username }}</div>
                                @if($user->roles->isNotEmpty())
                                <div class="groles">
                                    {{ $user->roles->pluck('display_name')->implode(', ') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    @foreach($projects as $project)
                    <td>
                        @php $assignedRole = $matrix[$user->id][$project->id] ?? null; @endphp

                        @if($assignedRole)
                            <div class="role-cell role-{{ $assignedRole->name }}"
                                onclick="openModal({{ $user->id }}, '{{ $user->username }}', {{ $project->id }}, '{{ $project->name }}', {{ $assignedRole->id }})">
                                {{ $assignedRole->display_name }}
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                            </div>
                        @else
                            <div class="empty-cell"
                                onclick="openModal({{ $user->id }}, '{{ $user->username }}', {{ $project->id }}, '{{ $project->name }}', null)"
                                title="Berechtigung vergeben">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </div>
                        @endif
                    </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $projects->count() + 1 }}" style="text-align:center; color:#94A3B8; padding:3rem;">
                        Keine Benutzer vorhanden.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif

{{-- Modal --}}
<div class="modal-backdrop" id="matrixModal">
    <div class="modal" style="width:460px;">
        <h3 id="modalTitle">Berechtigung vergeben</h3>
        <p id="modalSubtitle" style="font-size:.83rem; color:#64748B; margin-bottom:1.25rem;"></p>

        <div id="roleOptions"></div>

        <div id="revokeRow" style="display:none; margin-bottom:.75rem;">
            <button class="btn btn-danger btn-sm" onclick="revokeRole()" style="width:100%;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                Berechtigung entfernen
            </button>
        </div>

        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal()">Abbrechen</button>
            <button class="btn btn-primary" id="saveBtn" onclick="saveRole()">Speichern</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const projectRoles = @json($projectRoles);
const roleDescriptions = {
    'projektleiter_admin': 'Zugriff auf alle Projekte',
    'projektleiter':       'Kann alle Revisionen im Projekt ersetzen',
    'editor':              'Kann eigene Revisionen hinzufügen und ersetzen',
    'viewer':              'Nur lesender Zugriff',
};
const roleIcons = {
    'projektleiter_admin': '★',
    'projektleiter':       '◆',
    'editor':              '✎',
    'viewer':              '◉',
};

let state = { userId: null, projectId: null, selectedRoleId: null, currentRoleId: null };

function openModal(userId, username, projectId, projectName, currentRoleId) {
    state = { userId, projectId, selectedRoleId: currentRoleId, currentRoleId };

    document.getElementById('modalTitle').textContent =
        currentRoleId ? 'Berechtigung ändern' : 'Berechtigung vergeben';
    document.getElementById('modalSubtitle').textContent =
        `${username}  →  ${projectName}`;

    document.getElementById('revokeRow').style.display = currentRoleId ? 'block' : 'none';

    const container = document.getElementById('roleOptions');
    container.innerHTML = '';

    projectRoles.forEach(role => {
        const div = document.createElement('div');
        div.className = 'role-option' + (role.id === currentRoleId ? ' selected' : '');
        div.dataset.roleId = role.id;
        div.innerHTML = `
            <span class="role-icon">${roleIcons[role.name] || '●'}</span>
            <div>
                <div class="role-name">${role.display_name}</div>
                <div class="role-desc">${roleDescriptions[role.name] || role.description || ''}</div>
            </div>`;
        div.addEventListener('click', () => selectRole(role.id, div));
        container.appendChild(div);
    });

    document.getElementById('matrixModal').classList.add('open');
}

function selectRole(roleId, el) {
    state.selectedRoleId = roleId;
    document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
}

function closeModal() {
    document.getElementById('matrixModal').classList.remove('open');
}

async function saveRole() {
    if (!state.selectedRoleId) return;

    const res = await fetch('{{ route("admin.permissions.assign") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            user_id:    state.userId,
            project_id: state.projectId,
            role_id:    state.selectedRoleId,
        }),
    });

    if (res.ok) {
        closeModal();
        window.location.reload();
    }
}

async function revokeRole() {
    if (!confirm('Berechtigung wirklich entfernen?')) return;

    const res = await fetch('{{ route("admin.permissions.revoke") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            user_id:    state.userId,
            project_id: state.projectId,
        }),
    });

    if (res.ok) {
        closeModal();
        window.location.reload();
    }
}

document.getElementById('matrixModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endpush
