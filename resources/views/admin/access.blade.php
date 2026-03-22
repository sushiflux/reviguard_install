@extends('layouts.app')

@section('title', 'Benutzer & Berechtigungen')
@section('breadcrumb', 'Administration &rsaquo; Benutzer & Berechtigungen')

@push('styles')
<style>
    /* ── Role pills ── */
    .role-pill {
        display: inline-flex; align-items: center;
        padding: .2rem .6rem; border-radius: 999px;
        font-size: .72rem; font-weight: 600;
        border: 1px solid transparent; white-space: nowrap;
    }
    .role-projektleiter_admin { background: rgba(245,158,11,.15); color: #B45309; border-color: rgba(245,158,11,.3); }
    .role-projektleiter       { background: rgba(30,64,175,.12);  color: #1D4ED8; border-color: rgba(30,64,175,.2); }
    .role-developer           { background: rgba(139,92,246,.12); color: #6D28D9; border-color: rgba(139,92,246,.2); }
    .role-editor              { background: rgba(234,179,8,.15);  color: #A16207; border-color: rgba(234,179,8,.3); }
    .role-viewer              { background: rgba(34,197,94,.15);  color: #15803D; border-color: rgba(34,197,94,.3); }

    /* ── Accordion list ── */
    .acc-list { border-top: 1px solid #F1F5F9; }

    .acc-header {
        display: flex; align-items: center; gap: .75rem;
        padding: .875rem 1.25rem; cursor: pointer;
        transition: background .12s;
        border-bottom: 1px solid #F1F5F9;
    }
    .acc-header:hover   { background: #F8FAFC; }
    .acc-header.is-open { background: #F1F5F9; border-bottom-color: #E2E8F0; }

    .acc-avatar {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 700; color: #fff;
    }
    .acc-uname     { font-weight: 700; font-size: .9rem; color: #1E293B; }
    .acc-usub      { font-size: .75rem; color: var(--c-muted); margin-top: .05rem; }
    .acc-info      { flex: 1; min-width: 0; }
    .acc-count     { font-size: .75rem; color: var(--c-muted); white-space: nowrap; }
    .acc-chevron   { flex-shrink: 0; color: #CBD5E1; transition: transform .2s; }
    .acc-header.is-open .acc-chevron { transform: rotate(90deg); color: var(--c-accent1); }

    /* ── Accordion body ── */
    .acc-body {
        display: none;
        background: #FAFCFF;
        border-bottom: 1px solid #E2E8F0;
    }
    .acc-body.is-open { display: block; }

    .acc-body-inner { padding: .75rem 1.25rem .75rem 4rem; }

    /* ── Project assignment rows ── */
    .proj-assign-row {
        display: flex; align-items: center; gap: .75rem;
        padding: .5rem 0;
        border-bottom: 1px solid #F1F5F9;
    }
    .proj-assign-row:last-child { border-bottom: none; }
    .proj-assign-name {
        flex: 1; font-size: .875rem; color: #334155;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        display: flex; align-items: center; gap: .5rem;
    }
    .proj-assign-name svg { flex-shrink: 0; }
    .proj-assign-role { min-width: 110px; text-align: right; }
    .proj-none { font-size: .78rem; color: #CBD5E1; }

    /* ── Role modal options ── */
    .role-option {
        display: flex; align-items: center;
        padding: .65rem .875rem; border-radius: 8px;
        border: 1.5px solid #E2E8F0; cursor: pointer;
        margin-bottom: .5rem; gap: .75rem;
        transition: border-color .15s, background .15s;
    }
    .role-option:hover    { border-color: var(--c-accent1); background: rgba(6,182,212,.04); }
    .role-option.selected { border-color: var(--c-accent1); background: rgba(6,182,212,.08); }
    .role-option .ro-name { font-weight: 600; font-size: .875rem; color: #1E293B; }
    .role-option .ro-desc { font-size: .75rem; color: var(--c-muted); margin-top: .1rem; }
    .role-option .ro-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
</style>
@endpush

@section('content')

@php $activeTab = request('tab', 'benutzer'); @endphp

@if(session('success'))
    <div class="alert alert-success">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- ── Tab Bar ── --}}
<div class="tab-bar">
    <button type="button"
            class="tab-btn {{ $activeTab === 'benutzer' ? 'tab-active' : '' }}"
            onclick="switchTab('benutzer')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Benutzer
    </button>
    <button type="button"
            class="tab-btn {{ $activeTab === 'matrix' ? 'tab-active' : '' }}"
            onclick="switchTab('matrix')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/>
            <line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/>
        </svg>
        Berechtigungsmatrix
    </button>
</div>


{{-- ════════════════════════════════════════════════════
     TAB 1: Benutzer
═════════════════════════════════════════════════════ --}}
<div id="tab-benutzer" class="tab-panel {{ $activeTab !== 'benutzer' ? 'tab-hidden' : '' }}">

    <div class="card">
        <div class="card-header">
            <h2>Benutzer</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Neuer Benutzer
            </a>
        </div>

        <table class="tbl">
            <thead>
                <tr>
                    <th>Benutzername</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Rollen</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aktionen</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->username }}</strong></td>
                    <td>{{ $user->name }}</td>
                    <td style="color:#64748B;">{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $role)
                            @php
                                $badgeClass = match($role->name) {
                                    'administrator'       => 'badge-blue',
                                    'projektleiter_admin' => 'badge-amber',
                                    'developer'           => 'badge-cyan',
                                    'system_admin'        => 'badge-red',
                                    default               => 'badge-green',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}" style="margin-right:.2rem;">{{ $role->display_name }}</span>
                        @empty
                            <span style="color:#CBD5E1; font-size:.8rem;">–</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge badge-green">Aktiv</span>
                        @else
                            <span class="badge badge-red">Deaktiviert</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:.4rem; justify-content:flex-end; align-items:center;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Bearbeiten
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-amber' : 'btn-cyan' }}">
                                    {{ $user->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                                </button>
                            </form>
                            <button class="btn btn-ghost btn-sm"
                                onclick="openUserResetModal({{ $user->id }}, '{{ $user->username }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                Passwort
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:#94A3B8; padding:3rem;">Keine Benutzer vorhanden.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>{{-- /tab-benutzer --}}


{{-- ════════════════════════════════════════════════════
     TAB 2: Berechtigungsmatrix
═════════════════════════════════════════════════════ --}}
<div id="tab-matrix" class="tab-panel {{ $activeTab !== 'matrix' ? 'tab-hidden' : '' }}">

    <div class="card">
        <div class="card-header">
            <h2>Berechtigungsmatrix</h2>
            <span style="font-size:.78rem; color:var(--c-muted);">
                {{ $users->count() }} Benutzer &middot; {{ $projects->count() }} Projekte
            </span>
        </div>

        @if($users->isEmpty())
            <div style="padding:3rem; text-align:center; color:#94A3B8;">Keine Benutzer vorhanden.</div>
        @else

        <div class="acc-list">
            @foreach($users as $u)
            @php $assignedCount = count($matrix[$u->id] ?? []); @endphp

            {{-- Accordion header --}}
            <div class="acc-header" id="acc-hdr-{{ $u->id }}" onclick="toggleAcc({{ $u->id }})">
                <div class="acc-avatar">{{ strtoupper(substr($u->username, 0, 2)) }}</div>
                <div class="acc-info">
                    <div class="acc-uname">{{ $u->username }}</div>
                    <div class="acc-usub">{{ $u->name }}</div>
                </div>
                @if($u->roles->isNotEmpty())
                    <div style="display:flex; gap:.25rem; flex-wrap:wrap; justify-content:flex-end; max-width:220px;">
                        @foreach($u->roles as $gr)
                            <span class="badge badge-blue" style="font-size:.68rem;">{{ $gr->display_name }}</span>
                        @endforeach
                    </div>
                @endif
                <span class="acc-count">
                    {{ $assignedCount }}&thinsp;/&thinsp;{{ $projects->count() }} Projekte
                </span>
                <svg class="acc-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </div>

            {{-- Accordion body --}}
            <div class="acc-body" id="acc-body-{{ $u->id }}">
                <div class="acc-body-inner">
                    @if($projects->isEmpty())
                        <div style="padding:.5rem 0; color:#94A3B8; font-size:.85rem;">Keine Projekte vorhanden.</div>
                    @else
                    @foreach($projects as $proj)
                    @php $role = $matrix[$u->id][$proj->id] ?? null; @endphp
                    <div class="proj-assign-row">
                        <span class="proj-assign-name">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            </svg>
                            {{ $proj->name }}
                        </span>
                        <div class="proj-assign-role">
                            @if($role)
                                <span class="role-pill role-{{ $role->name }}">{{ $role->display_name }}</span>
                            @else
                                <span class="proj-none">Keine</span>
                            @endif
                        </div>
                        @if($role)
                            <button class="btn btn-ghost btn-sm"
                                onclick="openRoleModal({{ $u->id }}, '{{ addslashes($u->username) }}', {{ $proj->id }}, '{{ addslashes($proj->name) }}', {{ $role->id }})">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
                                </svg>
                                Ändern
                            </button>
                        @else
                            <button class="btn btn-ghost btn-sm"
                                onclick="openRoleModal({{ $u->id }}, '{{ addslashes($u->username) }}', {{ $proj->id }}, '{{ addslashes($proj->name) }}', null)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="13" height="13">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                                Vergeben
                            </button>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            @endforeach
        </div>

        @endif
    </div>

</div>{{-- /tab-matrix --}}


{{-- ── Modals ── --}}

{{-- Password Reset --}}
<div class="modal-backdrop" id="userResetModal">
    <div class="modal">
        <h3>Passwort zurücksetzen</h3>
        <p style="font-size:.85rem; color:#64748B; margin-bottom:1rem;">
            Neues Passwort für <strong id="userModalUsername"></strong> setzen.
        </p>
        <form id="userResetForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Neues Passwort</label>
                <input type="password" name="password" class="form-control" placeholder="Mind. 8 Zeichen" required>
            </div>
            <div class="form-group">
                <label class="form-label">Passwort bestätigen</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeUserResetModal()">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Zurücksetzen</button>
            </div>
        </form>
    </div>
</div>

{{-- Role Assignment --}}
<div class="modal-backdrop" id="roleModal">
    <div class="modal" style="width:460px;">
        <h3 id="roleModalTitle">Berechtigung vergeben</h3>
        <p id="roleModalSubtitle" style="font-size:.83rem; color:#64748B; margin-bottom:1.25rem;"></p>
        <div id="roleModalOptions"></div>
        <div id="roleModalRevoke" style="display:none; margin-bottom:.75rem;">
            <button class="btn btn-danger btn-sm" onclick="revokeRole()" style="width:100%;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                </svg>
                Berechtigung entfernen
            </button>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeRoleModal()">Abbrechen</button>
            <button class="btn btn-primary" onclick="saveRole()">Speichern</button>
        </div>
    </div>
</div>


<style>
.tab-bar {
    display: flex; gap: 0;
    border-bottom: 2px solid #E2E8F0;
    margin-bottom: 1.75rem;
}
.tab-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .65rem 1.25rem; background: none; border: none;
    border-bottom: 2px solid transparent; margin-bottom: -2px;
    font-size: .875rem; font-weight: 600; color: #64748B;
    cursor: pointer; transition: color .15s, border-color .15s;
}
.tab-btn:hover { color: var(--c-accent1); }
.tab-active { color: var(--c-accent1) !important; border-bottom-color: var(--c-accent1) !important; }
.tab-hidden { display: none; }
</style>

@push('scripts')
<script>
// ── Tab switching ──────────────────────────────────────────
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('tab-hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('tab-active'));
    document.getElementById('tab-' + name).classList.remove('tab-hidden');
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick').includes("'" + name + "'")) b.classList.add('tab-active');
    });
    const url = new URL(window.location);
    url.searchParams.set('tab', name);
    history.replaceState(null, '', url);
}

// ── Accordion ─────────────────────────────────────────────
function toggleAcc(userId) {
    const hdr  = document.getElementById('acc-hdr-'  + userId);
    const body = document.getElementById('acc-body-' + userId);
    const isOpen = body.classList.contains('is-open');

    // Close all
    document.querySelectorAll('.acc-body').forEach(b => b.classList.remove('is-open'));
    document.querySelectorAll('.acc-header').forEach(h => h.classList.remove('is-open'));

    if (!isOpen) {
        body.classList.add('is-open');
        hdr.classList.add('is-open');
    }
}

// ── User Password Reset Modal ──────────────────────────────
function openUserResetModal(userId, username) {
    document.getElementById('userModalUsername').textContent = username;
    document.getElementById('userResetForm').action = '/admin/users/' + userId + '/reset-password';
    document.getElementById('userResetModal').classList.add('open');
}
function closeUserResetModal() {
    document.getElementById('userResetModal').classList.remove('open');
}
document.getElementById('userResetModal').addEventListener('click', function(e) {
    if (e.target === this) closeUserResetModal();
});

// ── Role Assignment Modal ──────────────────────────────────
const projectRoles = @json($projectRoles);
const roleDotColors = {
    'projektleiter_admin': '#F59E0B',
    'projektleiter':       '#1E40AF',
    'developer':           '#8B5CF6',
    'editor':              '#EAB308',
    'viewer':              '#22C55E',
};
const roleDescriptions = {
    'projektleiter_admin': 'Zugriff auf alle Projekte (global)',
    'projektleiter':       'Kann alle Revisionen im Projekt ersetzen',
    'developer':           'Entwickler-Zugriff',
    'editor':              'Kann eigene Revisionen hinzufügen und ersetzen',
    'viewer':              'Nur lesender Zugriff',
};

let roleState = { userId: null, projectId: null, selectedRoleId: null, currentRoleId: null };

function openRoleModal(userId, username, projectId, projectName, currentRoleId) {
    roleState = { userId, projectId, selectedRoleId: currentRoleId, currentRoleId };
    document.getElementById('roleModalTitle').textContent    = currentRoleId ? 'Berechtigung ändern' : 'Berechtigung vergeben';
    document.getElementById('roleModalSubtitle').textContent = username + '  →  ' + projectName;
    document.getElementById('roleModalRevoke').style.display = currentRoleId ? 'block' : 'none';

    const container = document.getElementById('roleModalOptions');
    container.innerHTML = '';
    projectRoles.forEach(role => {
        const div = document.createElement('div');
        div.className = 'role-option' + (role.id === currentRoleId ? ' selected' : '');
        div.dataset.rid = role.id;
        div.innerHTML = `
            <div class="ro-dot" style="background:${roleDotColors[role.name] || '#94A3B8'};"></div>
            <div>
                <div class="ro-name">${role.display_name}</div>
                <div class="ro-desc">${roleDescriptions[role.name] || ''}</div>
            </div>`;
        div.addEventListener('click', () => {
            roleState.selectedRoleId = role.id;
            container.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
            div.classList.add('selected');
        });
        container.appendChild(div);
    });

    document.getElementById('roleModal').classList.add('open');
}
function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('open');
}
document.getElementById('roleModal').addEventListener('click', function(e) {
    if (e.target === this) closeRoleModal();
});

async function saveRole() {
    if (!roleState.selectedRoleId) return;
    const res = await fetch('{{ route("admin.permissions.assign") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ user_id: roleState.userId, project_id: roleState.projectId, role_id: roleState.selectedRoleId }),
    });
    if (res.ok) { closeRoleModal(); window.location.reload(); }
}

async function revokeRole() {
    if (!confirm('Berechtigung wirklich entfernen?')) return;
    const res = await fetch('{{ route("admin.permissions.revoke") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ user_id: roleState.userId, project_id: roleState.projectId }),
    });
    if (res.ok) { closeRoleModal(); window.location.reload(); }
}
</script>
@endpush

@endsection
