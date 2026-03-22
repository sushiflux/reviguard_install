@extends('layouts.app')

@section('title', 'System-Admins')
@section('breadcrumb', 'System-Admins')

@section('content')
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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

{{-- Passwort-Reset Modal --}}
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
@endsection

@push('scripts')
<script>
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
