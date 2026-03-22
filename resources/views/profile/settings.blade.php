@extends('layouts.app')

@section('title', 'Einstellungen')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; Einstellungen
@endsection

@section('content')

@php
    $user       = auth()->user();
    $activeTab  = request('tab', 'darstellung');
@endphp

{{-- Flash messages --}}
@if(session('success'))
<div class="alert alert-success">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- ── Tab Bar ── --}}
<div class="tab-bar">
    <button type="button"
            class="tab-btn {{ $activeTab === 'darstellung' ? 'tab-active' : '' }}"
            onclick="switchTab('darstellung')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Darstellung
    </button>
    <button type="button"
            class="tab-btn {{ $activeTab === 'passwort' ? 'tab-active' : '' }}"
            onclick="switchTab('passwort')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        Passwort
    </button>
    <button type="button"
            class="tab-btn {{ $activeTab === '2fa' ? 'tab-active' : '' }}"
            onclick="switchTab('2fa')">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 12 11 14 15 10"/>
        </svg>
        2FA
    </button>
</div>

{{-- ════════════════════════════════════════════════════
     TAB 1: Darstellung
═════════════════════════════════════════════════════ --}}
<div id="tab-darstellung" class="tab-panel {{ $activeTab !== 'darstellung' ? 'tab-hidden' : '' }}">

    <form method="POST" action="{{ route('profile.settings.save') }}">
        @csrf

        {{-- Dashboard --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header"><h2>Dashboard</h2></div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Standard-Ansicht</label>
                    <div style="display:flex; gap:.75rem; margin-top:.5rem;">
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="dashboard_view" value="tile"
                                   {{ $user->dashboard_view === 'tile' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->dashboard_view === 'tile' ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                                    <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                </svg>
                                <span>Kacheln</span>
                            </div>
                        </label>
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="dashboard_view" value="list"
                                   {{ $user->dashboard_view === 'list' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->dashboard_view === 'list' ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/>
                                    <line x1="3" y1="18" x2="21" y2="18"/>
                                </svg>
                                <span>Liste</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revisionen --}}
        <div class="card" style="margin-bottom:1.25rem;">
            <div class="card-header"><h2>Revisionen</h2></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:1.5rem;">

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Standard-Ansicht</label>
                    <div style="display:flex; gap:.75rem; margin-top:.5rem;">
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="revision_view" value="journal"
                                   {{ $user->revision_view === 'journal' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->revision_view === 'journal' ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                                    <line x1="8" y1="18" x2="21" y2="18"/>
                                    <circle cx="3" cy="6" r="1.5" fill="currentColor" stroke="none"/>
                                    <circle cx="3" cy="12" r="1.5" fill="currentColor" stroke="none"/>
                                    <circle cx="3" cy="18" r="1.5" fill="currentColor" stroke="none"/>
                                </svg>
                                <span>Journal</span>
                            </div>
                        </label>
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="revision_view" value="list"
                                   {{ $user->revision_view === 'list' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->revision_view === 'list' ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/>
                                    <line x1="3" y1="18" x2="21" y2="18"/>
                                </svg>
                                <span>Liste</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Vorgänger-Revisionen</label>
                    <div style="display:flex; gap:.75rem; margin-top:.5rem;">
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="predecessors_expanded" value="0"
                                   {{ !$user->predecessors_expanded ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ !$user->predecessors_expanded ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <polyline points="9 18 15 12 9 6"/>
                                </svg>
                                <span>Eingeklappt</span>
                            </div>
                        </label>
                        <label style="flex:1; cursor:pointer;">
                            <input type="radio" name="predecessors_expanded" value="1"
                                   {{ $user->predecessors_expanded ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->predecessors_expanded ? 'pref-active' : '' }}">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                                <span>Ausgeklappt</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <div style="display:flex; justify-content:flex-end;">
            <button type="submit" class="btn btn-primary">Einstellungen speichern</button>
        </div>
    </form>

</div>{{-- /tab-darstellung --}}


{{-- ════════════════════════════════════════════════════
     TAB 2: Passwort
═════════════════════════════════════════════════════ --}}
<div id="tab-passwort" class="tab-panel {{ $activeTab !== 'passwort' ? 'tab-hidden' : '' }}">

    <div class="card">
        <div class="card-header"><h2>Passwort ändern</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Aktuelles Passwort *</label>
                    <input type="password" name="current_password" class="form-control"
                           required autocomplete="current-password">
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Neues Passwort *</label>
                    <input type="password" name="password" class="form-control"
                           required autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Neues Passwort bestätigen *</label>
                    <input type="password" name="password_confirmation" class="form-control"
                           required autocomplete="new-password">
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:.5rem;">
                    <button type="submit" class="btn btn-primary">Passwort speichern</button>
                </div>
            </form>
        </div>
    </div>

</div>{{-- /tab-passwort --}}


{{-- ════════════════════════════════════════════════════
     TAB 3: 2FA
═════════════════════════════════════════════════════ --}}
<div id="tab-2fa" class="tab-panel {{ $activeTab !== '2fa' ? 'tab-hidden' : '' }}">

    {{-- ── TOTP ── --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <h2>
                <span style="display:inline-flex; align-items:center; gap:.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Authenticator-App (TOTP)
                </span>
            </h2>
            @if($user->hasTotpEnabled())
                <span class="badge badge-green">Aktiv</span>
            @else
                <span class="badge badge-gray">Nicht eingerichtet</span>
            @endif
        </div>
        <div class="card-body">

            @if($user->hasTotpEnabled())
                <p style="font-size:.875rem; color:#475569; margin-bottom:1.25rem; line-height:1.6;">
                    TOTP ist seit <strong>{{ $user->totp_enabled_at->format('d.m.Y H:i') }} Uhr</strong> aktiv.
                    Zur Deaktivierung wird Ihr aktuelles Passwort benötigt.
                </p>

                @if($errors->has('password') && $activeTab === 'sicherheit')
                    <div class="alert alert-error" style="margin-bottom:1rem;">{{ $errors->first('password') }}</div>
                @endif

                <form method="POST" action="{{ route('profile.2fa.totp.disable') }}">
                    @csrf
                    <div class="form-group" style="max-width:320px;">
                        <label class="form-label" for="disable_password">Passwort zur Bestätigung</label>
                        <input type="password" id="disable_password" name="password"
                               class="form-control" autocomplete="current-password" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('TOTP wirklich deaktivieren?')">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        TOTP deaktivieren
                    </button>
                </form>

            @else
                <p style="font-size:.875rem; color:#475569; margin-bottom:1.25rem; line-height:1.6;">
                    Schützen Sie Ihr Konto mit einer Authenticator-App
                    (Google Authenticator, Authy, Microsoft Authenticator u.&nbsp;a.).
                    Bei jeder Anmeldung wird ein zeitbasierter 6-stelliger Code abgefragt.
                </p>
                <a href="{{ route('profile.2fa.totp.setup') }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    TOTP einrichten
                </a>
            @endif

        </div>
    </div>

    {{-- ── WebAuthn / YubiKey ── --}}
    <div class="card">
        <div class="card-header">
            <h2>
                <span style="display:inline-flex; align-items:center; gap:.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                    </svg>
                    YubiKey / Hardware-Token (WebAuthn)
                </span>
            </h2>
            @if($credentials->count() > 0)
                <span class="badge badge-green">{{ $credentials->count() }} registriert</span>
            @else
                <span class="badge badge-gray">Keine</span>
            @endif
        </div>
        <div class="card-body">

            @if($credentials->count() > 0)
                <table class="tbl" style="margin-bottom:1.5rem;">
                    <thead>
                        <tr>
                            <th>Name / Alias</th>
                            <th>Registriert am</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($credentials as $cred)
                        <tr>
                            <td>
                                <form method="POST"
                                      action="{{ route('webauthn.credentials.update', $cred->id) }}"
                                      style="display:flex; gap:.5rem; align-items:center;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="alias" class="form-control"
                                           value="{{ $cred->alias ?? '' }}"
                                           placeholder="YubiKey benennen…"
                                           style="max-width:200px; padding:.35rem .65rem; font-size:.82rem;">
                                    <button type="submit" class="btn btn-ghost btn-sm">Speichern</button>
                                </form>
                            </td>
                            <td style="font-size:.82rem; color:#64748B;">
                                {{ $cred->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('webauthn.credentials.destroy', $cred->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('YubiKey wirklich entfernen?')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14H6L5 6"/>
                                            <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
                                        </svg>
                                        Entfernen
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="font-size:.875rem; color:#475569; margin-bottom:1.25rem; line-height:1.6;">
                    Noch kein YubiKey oder Hardware-Token registriert.
                    Registrieren Sie einen physischen Sicherheitsschlüssel für phishing-resistentes 2FA.
                </p>
            @endif

            {{-- Register new key --}}
            <div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:8px; padding:1.25rem;">
                <p style="font-size:.82rem; font-weight:600; color:#475569; margin-bottom:.85rem;">
                    Neuen YubiKey registrieren
                </p>

                <div id="webauthn-register-error" class="alert alert-error" style="display:none; margin-bottom:.85rem;"></div>
                <div id="webauthn-register-success" class="alert alert-success" style="display:none; margin-bottom:.85rem;"></div>

                <div style="display:flex; gap:.65rem; align-items:flex-end; flex-wrap:wrap;">
                    <div style="flex:1; min-width:160px;">
                        <label class="form-label" for="key-alias" style="margin-bottom:.35rem;">Name (optional)</label>
                        <input type="text" id="key-alias" class="form-control"
                               placeholder="z. B. YubiKey 5 NFC" maxlength="60">
                    </div>
                    <button type="button" class="btn btn-amber" id="register-webauthn-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                        <span>YubiKey registrieren</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>{{-- /tab-2fa --}}


<style>
/* ── Tabs ── */
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

.tab-active {
    color: var(--c-accent1) !important;
    border-bottom-color: var(--c-accent1) !important;
}

.tab-hidden { display: none; }

/* ── Preference tiles ── */
.pref-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .5rem;
    padding: 1rem;
    border: 2px solid #E2E8F0;
    border-radius: 10px;
    background: #F8FAFC;
    color: #64748B;
    font-size: .85rem;
    font-weight: 600;
    transition: all .15s;
    text-align: center;
}
.pref-option:hover  { border-color: var(--c-accent1); color: var(--c-accent1); background: #ECFEFF; }
.pref-active        { border-color: var(--c-accent1) !important; background: #ECFEFF !important; color: var(--c-accent1) !important; }
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

    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', name);
    history.replaceState(null, '', url);
}

// ── Preference radio tiles ─────────────────────────────────
document.querySelectorAll('.pref-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll(`input[name="${radio.name}"]`).forEach(r => {
            r.nextElementSibling.classList.remove('pref-active');
        });
        radio.nextElementSibling.classList.add('pref-active');
    });
});

// ── WebAuthn Registration ──────────────────────────────────
function bufferToBase64url(buffer) {
    const bytes = new Uint8Array(buffer);
    let str = '';
    for (const byte of bytes) str += String.fromCharCode(byte);
    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
}

function base64urlToBuffer(base64url) {
    const base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
    const bin = atob(base64);
    const buf = new ArrayBuffer(bin.length);
    const bytes = new Uint8Array(buf);
    for (let i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
    return buf;
}

function getCsrfToken() {
    const meta = document.head.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.content;
    const input = document.querySelector('input[name="_token"]');
    return input ? input.value : '';
}

document.getElementById('register-webauthn-btn').addEventListener('click', async function () {
    const btn   = this;
    const alias = document.getElementById('key-alias').value.trim();
    const errEl = document.getElementById('webauthn-register-error');
    const okEl  = document.getElementById('webauthn-register-success');

    errEl.style.display = 'none';
    okEl.style.display  = 'none';
    btn.disabled = true;
    btn.querySelector('span').textContent = 'Bitte warten…';

    try {
        const optRes = await fetch('{{ route('webauthn.register.options') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({}),
        });

        if (!optRes.ok) {
            const err = await optRes.json().catch(() => ({}));
            throw new Error(err.message || 'Fehler beim Laden der Registrierungs-Challenge.');
        }

        const optJson = await optRes.json();
        const publicKey = optJson.publicKey ?? optJson;

        publicKey.challenge = base64urlToBuffer(publicKey.challenge);

        if (publicKey.user?.id) {
            publicKey.user = { ...publicKey.user, id: base64urlToBuffer(publicKey.user.id) };
        }

        if (publicKey.excludeCredentials) {
            publicKey.excludeCredentials = publicKey.excludeCredentials.map(c => ({
                ...c, id: base64urlToBuffer(c.id),
            }));
        }

        const credential = await navigator.credentials.create({ publicKey });

        const payload = {
            id: credential.id,
            type: credential.type,
            rawId: bufferToBase64url(credential.rawId),
            authenticatorAttachment: credential.authenticatorAttachment ?? null,
            clientExtensionResults: credential.getClientExtensionResults(),
            alias: alias,
            response: {
                clientDataJSON:    bufferToBase64url(credential.response.clientDataJSON),
                attestationObject: bufferToBase64url(credential.response.attestationObject),
            },
        };

        const saveRes = await fetch('{{ route('webauthn.register') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        if (!saveRes.ok) {
            const err = await saveRes.json().catch(() => ({}));
            throw new Error(err.message || 'Registrierung fehlgeschlagen.');
        }

        okEl.textContent = 'YubiKey erfolgreich registriert! Seite wird neu geladen…';
        okEl.style.display = 'block';
        setTimeout(() => window.location = '{{ route('profile.settings') }}?tab=2fa', 1200);

    } catch (e) {
        errEl.textContent = e.name === 'NotAllowedError'
            ? 'Vorgang abgebrochen oder kein Sicherheitsschlüssel erkannt.'
            : (e.message || 'Ein Fehler ist aufgetreten.');
        errEl.style.display = 'block';
        btn.disabled = false;
        btn.querySelector('span').textContent = 'YubiKey registrieren';
    }
});
</script>
@endpush

@endsection
