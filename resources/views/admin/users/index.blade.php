@extends('layouts.app')

@section('title', 'Benutzer')
@section('breadcrumb', 'Benutzer')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Benutzer</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
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
                                'administrator'      => 'badge-blue',
                                'projektleiter_admin'=> 'badge-amber',
                                'developer'          => 'badge-cyan',
                                'system_admin'       => 'badge-red',
                                default              => 'badge-gray',
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Bearbeiten
                        </a>

                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-amber' : 'btn-cyan' }}">
                                {{ $user->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                            </button>
                        </form>

                        <button class="btn btn-ghost btn-sm"
                            onclick="openResetModal({{ $user->id }}, '{{ $user->username }}')">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
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

{{-- Passwort-Reset Modal --}}
<div class="modal-backdrop" id="resetModal">
    <div class="modal">
        <h3>Passwort zurücksetzen</h3>
        <p style="font-size:.85rem; color:#64748B; margin-bottom:1rem;">
            Neues Passwort für <strong id="modalUsername"></strong> setzen.
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
function openResetModal(userId, username) {
    document.getElementById('modalUsername').textContent = username;
    document.getElementById('resetForm').action = '/admin/users/' + userId + '/reset-password';
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
