@extends('layouts.app')

@section('title', 'Einstellungen')
@section('breadcrumb', 'Administration &rsaquo; Einstellungen')

@section('content')

@php $activeTab = request('tab', 'system'); @endphp

@if(session('success'))
    <div class="alert alert-success">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- ── Tab Bar ── --}}
<div class="tab-bar">
    <button type="button"
            class="tab-btn {{ $activeTab === 'system' ? 'tab-active' : '' }}"
            onclick="switchTab('system')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
        </svg>
        System
    </button>
    <button type="button"
            class="tab-btn {{ $activeTab === 'sicherheit' ? 'tab-active' : '' }}"
            onclick="switchTab('sicherheit')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
        </svg>
        Sicherheit
    </button>
    <button type="button"
            class="tab-btn {{ $activeTab === 'system-admins' ? 'tab-active' : '' }}"
            onclick="switchTab('system-admins')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        System-Admins
    </button>
</div>


{{-- ════════════════════════════════════════════════════
     TAB 1: System (Platzhalter)
═════════════════════════════════════════════════════ --}}
<div id="tab-system" class="tab-panel {{ $activeTab !== 'system' ? 'tab-hidden' : '' }}">
    <div class="card">
        <div class="card-header">
            <h2>Systemeinstellungen</h2>
        </div>
        <div class="card-body" style="text-align:center; padding:4rem; color:#94A3B8;">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#CBD5E1" stroke-width="1.5" style="margin-bottom:1rem;">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93A10 10 0 0 0 4.93 19.07M4.93 4.93a10 10 0 0 0 14.14 14.14"/>
            </svg>
            <div style="font-size:.95rem; font-weight:600; color:#64748B;">Einstellungen</div>
            <div style="font-size:.82rem; margin-top:.4rem;">Dieser Bereich wird in einer späteren Version implementiert.</div>
        </div>
    </div>
</div>{{-- /tab-system --}}


{{-- ════════════════════════════════════════════════════
     TAB 2: Sicherheit (2FA-Richtlinie)
═════════════════════════════════════════════════════ --}}
<div id="tab-sicherheit" class="tab-panel {{ $activeTab !== 'sicherheit' ? 'tab-hidden' : '' }}">
    <div class="card">
        <div class="card-header">
            <h2>
                <span style="display:inline-flex; align-items:center; gap:.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 12 11 14 15 10"/>
                    </svg>
                    Globale 2FA-Richtlinie
                </span>
            </h2>
        </div>
        <div class="card-body">

            <div style="background:rgba(6,182,212,.07); border:1px solid rgba(6,182,212,.25);
                        border-radius:8px; padding:.875rem 1rem; margin-bottom:1.75rem;">
                <p style="font-size:.83rem; color:#0E7490; line-height:1.6;">
                    <strong>Hinweis:</strong> Diese Richtlinie gilt für <em>alle</em> Benutzer des Systems.
                    Wenn eine Pflicht gesetzt ist und ein Benutzer die entsprechende Methode noch nicht
                    eingerichtet hat, wird er nach dem Login direkt zur 2FA-Einrichtung weitergeleitet.
                    Benutzer mit individuell aktiviertem 2FA müssen es unabhängig von dieser Richtlinie durchlaufen.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.2fa-policy.save') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Richtlinie</label>
                    <div style="display:flex; flex-direction:column; gap:.75rem; margin-top:.25rem;">

                        @foreach(['none' => ['Keine Pflicht', 'Jeder Benutzer entscheidet selbst, ob er 2FA aktiviert.'],
                                  'any'  => ['2FA erforderlich (TOTP oder YubiKey)', 'Alle Benutzer müssen mindestens eine 2FA-Methode nutzen.'],
                                  'totp' => ['TOTP erforderlich', 'Alle Benutzer müssen eine Authenticator-App (TOTP) einrichten und nutzen.'],
                                  'webauthn' => ['YubiKey erforderlich', 'Alle Benutzer müssen einen Hardware-Sicherheitsschlüssel (WebAuthn) registrieren und nutzen.']]
                                  as $value => [$label, $desc])
                        <label style="display:flex; align-items:flex-start; gap:.75rem; cursor:pointer;
                                      padding:.85rem 1rem; border-radius:8px;
                                      border:1px solid {{ $policy === $value ? 'var(--c-accent1)' : '#E2E8F0' }};
                                      background:{{ $policy === $value ? 'rgba(6,182,212,.06)' : '#fff' }};">
                            <input type="radio" name="policy" value="{{ $value }}"
                                   {{ $policy === $value ? 'checked' : '' }}
                                   style="margin-top:.15rem; accent-color:var(--c-accent1);">
                            <div>
                                <div style="font-size:.875rem; font-weight:600; color:#1E293B; margin-bottom:.2rem;">{{ $label }}</div>
                                <div style="font-size:.8rem; color:#64748B;">{{ $desc }}</div>
                            </div>
                        </label>
                        @endforeach

                    </div>
                    @error('policy')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-top:.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Richtlinie speichern
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>{{-- /tab-sicherheit --}}


{{-- ════════════════════════════════════════════════════
     TAB 3: System-Admins
═════════════════════════════════════════════════════ --}}
<div id="tab-system-admins" class="tab-panel {{ $activeTab !== 'system-admins' ? 'tab-hidden' : '' }}">

    <div class="card">
        <div class="card-header">
            <h2>System-Administratoren</h2>
            <span class="badge badge-amber">Nur Ansicht &amp; Verwaltung</span>
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>Benutzername</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Status</th>
                    <th style="text-align:right;">Aktionen</th>
                </tr>
            </thead>
            <tbody>
            @forelse($admins as $admin)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:.6rem;">
                            <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#1E40AF,#06B6D4); display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; color:#fff; flex-shrink:0;">
                                {{ strtoupper(substr($admin->username, 0, 2)) }}
                            </div>
                            <strong>{{ $admin->username }}</strong>
                            <span class="badge badge-cyan" style="font-size:.65rem;">System-Admin</span>
                        </div>
                    </td>
                    <td>{{ $admin->name }}</td>
                    <td style="color:#64748B;">{{ $admin->email }}</td>
                    <td>
                        @if($admin->is_active)
                            <span class="badge badge-green">Aktiv</span>
                        @else
                            <span class="badge badge-red">Deaktiviert</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:.4rem; justify-content:flex-end;">
                            <form method="POST" action="{{ route('admin.system-admins.toggle', $admin) }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm {{ $admin->is_active ? 'btn-amber' : 'btn-cyan' }}"
                                    onclick="return confirm('{{ $admin->is_active ? 'Wirklich deaktivieren?' : 'Wirklich aktivieren?' }}')">
                                    {{ $admin->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                                </button>
                            </form>
                            <button class="btn btn-ghost btn-sm"
                                onclick="openResetModal({{ $admin->id }}, '{{ $admin->username }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                Passwort
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#94A3B8; padding:3rem;">Keine System-Admins vorhanden.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:1rem; padding:.75rem 1rem; background:rgba(245,158,11,.07); border:1px solid rgba(245,158,11,.2); border-radius:8px; font-size:.82rem; color:#92400E; display:flex; align-items:center; gap:.5rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        System-Admins können nicht gelöscht werden. Nur Aktivierung/Deaktivierung und Passwort-Reset sind möglich.
    </div>

</div>{{-- /tab-system-admins --}}


{{-- Passwort-Reset Modal (System-Admins) --}}
<div class="modal-backdrop" id="resetModal">
    <div class="modal">
        <h3>Passwort zurücksetzen</h3>
        <p style="font-size:.85rem; color:#64748B; margin-bottom:1rem;">
            Neues Passwort für System-Admin <strong id="modalUsername"></strong> setzen.
        </p>
        <form id="resetForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Neues Passwort</label>
                <input type="password" name="password" class="form-control" placeholder="Mind. 8 Zeichen" required>
            </div>
            <div class="form-group">
                <label class="form-label">Passwort bestätigen</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Passwort wiederholen" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeResetModal()">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Zurücksetzen</button>
            </div>
        </form>
    </div>
</div>


<style>
.tab-bar {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #E2E8F0;
    margin-bottom: 1.75rem;
}
.tab-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .65rem 1.25rem;
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    font-size: .875rem;
    font-weight: 600;
    color: #64748B;
    cursor: pointer;
    transition: color .15s, border-color .15s;
}
.tab-btn:hover { color: var(--c-accent1); }
.tab-active { color: var(--c-accent1) !important; border-bottom-color: var(--c-accent1) !important; }
.tab-hidden { display: none; }
</style>

@push('scripts')
<script>
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

function openResetModal(adminId, username) {
    document.getElementById('modalUsername').textContent = username;
    document.getElementById('resetForm').action = '/admin/system-admins/' + adminId + '/reset-password';
    document.getElementById('resetModal').classList.add('open');
}
function closeResetModal() {
    document.getElementById('resetModal').classList.remove('open');
}
document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
});
</script>
@endpush

@endsection
