@extends('layouts.app')

@section('title', 'Benutzer & Berechtigungen')
@section('breadcrumb', 'Administration &rsaquo; Benutzer & Berechtigungen')

@push('styles')
<style>
    /* ── Role badges (matrix) ── */
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

    /* ── User list grid ── */
    .mu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        gap: .75rem;
        padding: 1.25rem;
    }
    .mu-card {
        display: flex; align-items: center; gap: .75rem;
        padding: .875rem 1rem;
        border: 1.5px solid #E2E8F0; border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s, box-shadow .15s;
    }
    .mu-card:hover { border-color: var(--c-accent1); box-shadow: 0 2px 10px rgba(6,182,212,.12); }
    .mu-info    { flex: 1; min-width: 0; }
    .mu-name    { font-weight: 700; font-size: .9rem; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mu-sub     { font-size: .75rem; color: var(--c-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mu-avatar  {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 700; color: #fff;
    }

    /* ── User detail view ── */
    .detail-header-avatar {
        width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
        background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
        display: flex; align-items: center; justify-content: center;
        font-size: .9rem; font-weight: 700; color: #fff;
    }
    .detail-split    { display: flex; min-height: 420px; }
    .detail-projects { flex: 0 0 340px; border-right: 1.5px solid #E2E8F0; overflow-y: auto; max-height: 560px; }
    .detail-picker   { flex: 1; padding: 1.5rem; min-width: 0; display: flex; flex-direction: column; }

    .detail-section-label {
        padding: .75rem 1.25rem;
        font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase;
        color: var(--c-muted); background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        position: sticky; top: 0; z-index: 1;
    }

    .proj-row {
        display: flex; align-items: center; gap: .6rem;
        padding: .75rem 1.25rem;
        border-bottom: 1px solid #F1F5F9;
        cursor: pointer;
        transition: background .12s;
    }
    .proj-row:hover { background: #F8FAFC; }
    .proj-row.selected {
        background: rgba(6,182,212,.06);
        border-left: 3px solid var(--c-accent1);
        padding-left: calc(1.25rem - 3px);
    }
    .proj-row-name {
        flex: 1; font-size: .875rem; font-weight: 500; color: #334155;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* ── Role picker ── */
    .picker-empty {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        color: #94A3B8; font-size: .875rem; font-weight: 500;
        gap: .5rem; text-align: center; padding: 2rem;
    }
    .role-option {
        display: flex; align-items: center;
        padding: .65rem .875rem; border-radius: 8px;
        border: 1.5px solid #E2E8F0; cursor: pointer;
        margin-bottom: .5rem; gap: .75rem;
        transition: border-color .15s, background .15s;
    }
    .role-option:hover    { border-color: var(--c-accent1); background: rgba(6,182,212,.04); }
    .role-option.selected { border-color: var(--c-accent1); background: rgba(6,182,212,.08); }
    .role-option .role-name { font-weight: 600; font-size: .875rem; color: #1E293B; }
    .role-option .role-desc { font-size: .75rem; color: var(--c-muted); margin-top: .1rem; }
    .role-option .role-dot  { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
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

    {{-- ── View 1: User List ── --}}
    <div id="matrix-user-list">
        <div class="card">
            <div class="card-header">
                <h2>Benutzer</h2>
                <span style="font-size:.78rem; color:var(--c-muted);">Benutzer auswählen um Projektrollen zu verwalten</span>
            </div>
            @if($users->isEmpty())
                <div style="padding:3rem; text-align:center; color:#94A3B8;">Keine Benutzer vorhanden.</div>
            @else
            <div class="mu-grid">
                @foreach($users as $u)
                @php $assignedCount = count($matrix[$u->id] ?? []); @endphp
                <div class="mu-card" onclick="selectMatrixUser({{ $u->id }})">
                    <div class="mu-avatar">{{ strtoupper(substr($u->username, 0, 2)) }}</div>
                    <div class="mu-info">
                        <div class="mu-name">{{ $u->username }}</div>
                        <div class="mu-sub">{{ $u->name }}</div>
                    </div>
                    @if($assignedCount > 0)
                        <span class="badge badge-cyan">{{ $assignedCount }} {{ $assignedCount === 1 ? 'Projekt' : 'Projekte' }}</span>
                    @else
                        <span style="font-size:.72rem; color:#CBD5E1;">Keine</span>
                    @endif
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2" style="flex-shrink:0;">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── View 2: User Detail ── --}}
    <div id="matrix-detail" style="display:none;">

        <div style="margin-bottom:1rem;">
            <button class="btn btn-ghost btn-sm" onclick="backToUserList()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Alle Benutzer
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <div style="display:flex; align-items:center; gap:.875rem;">
                    <div class="detail-header-avatar" id="detail-avatar"></div>
                    <div>
                        <div style="font-weight:700; font-size:1rem; color:#1E293B;" id="detail-username"></div>
                        <div style="font-size:.78rem; color:var(--c-muted); margin-top:.15rem;" id="detail-userroles"></div>
                    </div>
                </div>
            </div>

            @if($projects->isEmpty())
                <div style="padding:3rem; text-align:center; color:#94A3B8;">
                    <div style="font-weight:600; color:#64748B; margin-bottom:.4rem;">Keine Projekte vorhanden</div>
                    <div style="font-size:.82rem;">Erstelle zuerst Projekte um Rollen zu vergeben.</div>
                </div>
            @else
            <div class="detail-split">

                {{-- Left: Project list --}}
                <div class="detail-projects">
                    <div class="detail-section-label">Projekt auswählen</div>
                    @foreach($projects as $proj)
                    <div class="proj-row" id="prow-{{ $proj->id }}"
                         onclick="selectProject({{ $proj->id }}, '{{ addslashes($proj->name) }}')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" style="flex-shrink:0;">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        </svg>
                        <span class="proj-row-name">{{ $proj->name }}</span>
                        <div id="prole-{{ $proj->id }}" style="flex-shrink:0;"></div>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="2" style="flex-shrink:0; margin-left:auto;">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                    @endforeach
                </div>

                {{-- Right: Role picker --}}
                <div class="detail-picker">
                    <div id="picker-empty" class="picker-empty">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.2">
                            <path d="M9 11l3 3L22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <div style="font-weight:600; color:#64748B;">Projekt auswählen</div>
                        <div style="font-size:.8rem;">Klicke links auf ein Projekt um eine Rolle zu vergeben</div>
                    </div>

                    <div id="picker-content" style="display:none;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--c-muted); margin-bottom:1rem;" id="picker-title"></div>
                        <div id="picker-roles"></div>
                        <div id="picker-revoke" style="display:none; margin-top:.625rem;">
                            <button class="btn btn-danger btn-sm" onclick="revokeProjectRole()" style="width:100%;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                </svg>
                                Berechtigung entfernen
                            </button>
                        </div>
                        <div style="margin-top:1rem;">
                            <button class="btn btn-primary" onclick="saveProjectRole()" style="width:100%;">Speichern</button>
                        </div>
                    </div>
                </div>

            </div>
            @endif
        </div>
    </div>

</div>{{-- /tab-matrix --}}


{{-- ── Modals ── --}}
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

// ── Matrix Data ────────────────────────────────────────────
@php
    $usersJson    = $users->map(fn($u) => ['id' => $u->id, 'username' => $u->username, 'name' => $u->name, 'roles' => $u->roles->pluck('display_name')->values()]);
    $projectsJson = $projects->map(fn($p) => ['id' => $p->id, 'name' => $p->name]);
@endphp
const matrixData      = @json($matrix);
const allProjectRoles = @json($projectRoles);
const allProjects     = @json($projectsJson);
const allUsersData    = @json($usersJson);

const roleColors = {
    'projektleiter_admin': { bg: 'rgba(245,158,11,.15)',  color: '#B45309', border: 'rgba(245,158,11,.3)' },
    'projektleiter':       { bg: 'rgba(30,64,175,.12)',   color: '#1D4ED8', border: 'rgba(30,64,175,.2)'  },
    'developer':           { bg: 'rgba(139,92,246,.12)',  color: '#6D28D9', border: 'rgba(139,92,246,.2)' },
    'editor':              { bg: 'rgba(234,179,8,.15)',   color: '#A16207', border: 'rgba(234,179,8,.3)'  },
    'viewer':              { bg: 'rgba(34,197,94,.15)',   color: '#15803D', border: 'rgba(34,197,94,.3)'  },
};
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

let currentUserId    = null;
let currentProjectId = null;
let selectedRoleId   = null;
let currentUserMatrix = {};

function rolePillHtml(role) {
    const c = roleColors[role.name] || { bg: 'rgba(100,116,139,.1)', color: '#475569', border: 'rgba(100,116,139,.2)' };
    return `<span class="role-pill" style="background:${c.bg};color:${c.color};border-color:${c.border};">${role.display_name}</span>`;
}

// ── View switching ─────────────────────────────────────────
function selectMatrixUser(userId) {
    currentUserId     = userId;
    currentProjectId  = null;
    selectedRoleId    = null;
    currentUserMatrix = matrixData[userId] || {};

    const user = allUsersData.find(u => u.id === userId);
    document.getElementById('detail-avatar').textContent   = user.username.substring(0, 2).toUpperCase();
    document.getElementById('detail-username').textContent = user.username + '  ·  ' + user.name;
    document.getElementById('detail-userroles').textContent =
        user.roles.length ? user.roles.join(', ') : 'Keine globalen Rollen';

    // Populate project role badges & reset selection
    allProjects.forEach(p => {
        const roleEl = document.getElementById('prole-' + p.id);
        if (roleEl) {
            const r = currentUserMatrix[p.id];
            roleEl.innerHTML = r ? rolePillHtml(r) : '';
        }
        const rowEl = document.getElementById('prow-' + p.id);
        if (rowEl) rowEl.classList.remove('selected');
    });

    // Reset picker to empty state
    document.getElementById('picker-empty').style.display   = 'flex';
    document.getElementById('picker-content').style.display = 'none';

    document.getElementById('matrix-user-list').style.display = 'none';
    document.getElementById('matrix-detail').style.display    = 'block';
}

function backToUserList() {
    currentUserId    = null;
    currentProjectId = null;
    selectedRoleId   = null;
    document.getElementById('matrix-detail').style.display    = 'none';
    document.getElementById('matrix-user-list').style.display = 'block';
}

// ── Project selection (right-side role picker) ─────────────
function selectProject(projectId, projectName) {
    currentProjectId = projectId;

    // Highlight selected project row
    allProjects.forEach(p => {
        const row = document.getElementById('prow-' + p.id);
        if (row) row.classList.toggle('selected', p.id === projectId);
    });

    const currentRole = currentUserMatrix[projectId] || null;
    selectedRoleId    = currentRole ? currentRole.id : null;

    // Build role options
    const container = document.getElementById('picker-roles');
    container.innerHTML = '';
    allProjectRoles.forEach(role => {
        const isSelected = currentRole && currentRole.id === role.id;
        const dotColor   = roleDotColors[role.name] || '#94A3B8';
        const div = document.createElement('div');
        div.className   = 'role-option' + (isSelected ? ' selected' : '');
        div.dataset.rid = role.id;
        div.innerHTML   = `
            <div class="role-dot" style="background:${dotColor};"></div>
            <div>
                <div class="role-name">${role.display_name}</div>
                <div class="role-desc">${roleDescriptions[role.name] || ''}</div>
            </div>`;
        div.addEventListener('click', () => {
            selectedRoleId = role.id;
            document.querySelectorAll('#picker-roles .role-option').forEach(o => o.classList.remove('selected'));
            div.classList.add('selected');
        });
        container.appendChild(div);
    });

    // Revoke button
    document.getElementById('picker-revoke').style.display = currentRole ? 'block' : 'none';

    document.getElementById('picker-title').textContent    = projectName;
    document.getElementById('picker-empty').style.display   = 'none';
    document.getElementById('picker-content').style.display = 'block';
}

// ── Save / Revoke ──────────────────────────────────────────
async function saveProjectRole() {
    if (!selectedRoleId || !currentProjectId || !currentUserId) return;
    const res = await fetch('{{ route("admin.permissions.assign") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ user_id: currentUserId, project_id: currentProjectId, role_id: selectedRoleId }),
    });
    if (res.ok) window.location.reload();
}

async function revokeProjectRole() {
    if (!confirm('Berechtigung wirklich entfernen?')) return;
    const res = await fetch('{{ route("admin.permissions.revoke") }}', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ user_id: currentUserId, project_id: currentProjectId }),
    });
    if (res.ok) window.location.reload();
}
</script>
@endpush

@endsection
