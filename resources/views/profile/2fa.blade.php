@extends('layouts.app')

@section('title', 'Zwei-Faktor-Authentifizierung')

@section('breadcrumb')
    2FA
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

{{-- ── Section 1: TOTP ─────────────────────────────────────────────── --}}
<div class="card" style="max-width: 680px; margin-bottom: 1.5rem;">
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
            <p style="font-size: .875rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.6;">
                TOTP ist seit <strong>{{ $user->totp_enabled_at->format('d.m.Y H:i') }} Uhr</strong> aktiv.
                Sie können TOTP hier deaktivieren. Sie benötigen dazu Ihr aktuelles Passwort.
            </p>

            @if($errors->has('password'))
                <div class="alert alert-error" style="margin-bottom: 1rem;">{{ $errors->first('password') }}</div>
            @endif

            <form method="POST" action="{{ route('profile.2fa.totp.disable') }}">
                @csrf
                <div class="form-group" style="max-width: 320px;">
                    <label class="form-label" for="disable_password">Passwort zur Bestätigung</label>
                    <input type="password" id="disable_password" name="password" class="form-control" autocomplete="current-password" required>
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
            <p style="font-size: .875rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.6;">
                Schützen Sie Ihr Konto mit einer Authenticator-App (Google Authenticator, Authy, Microsoft Authenticator u.&nbsp;a.).
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

{{-- ── Section 2: YubiKey / WebAuthn ──────────────────────────────── --}}
<div class="card" style="max-width: 680px;">
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
            <table class="tbl" style="margin-bottom: 1.5rem;">
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
                            <form method="POST" action="{{ route('webauthn.credentials.update', $cred->id) }}"
                                  style="display: flex; gap: .5rem; align-items: center;">
                                @csrf
                                @method('PATCH')
                                <input type="text" name="alias" class="form-control"
                                       value="{{ $cred->alias ?? '' }}"
                                       placeholder="YubiKey benennen…"
                                       style="max-width: 200px; padding: .35rem .65rem; font-size: .82rem;">
                                <button type="submit" class="btn btn-ghost btn-sm">Speichern</button>
                            </form>
                        </td>
                        <td style="font-size: .82rem; color: #64748B;">
                            {{ $cred->created_at->format('d.m.Y') }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('webauthn.credentials.destroy', $cred->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('YubiKey wirklich entfernen?')">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14H6L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
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
            <p style="font-size: .875rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.6;">
                Noch kein YubiKey oder Hardware-Token registriert. Registrieren Sie einen
                physischen Sicherheitsschlüssel für passwortloses und phishing-resistentes 2FA.
            </p>
        @endif

        {{-- Register new key --}}
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 1.25rem;">
            <p style="font-size: .82rem; font-weight: 600; color: #475569; margin-bottom: .85rem;">
                Neuen YubiKey registrieren
            </p>

            <div id="webauthn-register-error" class="alert alert-error" style="display:none; margin-bottom:.85rem;"></div>
            <div id="webauthn-register-success" class="alert alert-success" style="display:none; margin-bottom:.85rem;"></div>

            <div style="display: flex; gap: .65rem; align-items: flex-end; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 160px;">
                    <label class="form-label" for="key-alias" style="margin-bottom: .35rem;">
                        Name (optional)
                    </label>
                    <input type="text" id="key-alias" class="form-control"
                           placeholder="z. B. YubiKey 5 NFC"
                           maxlength="60">
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

@push('scripts')
<script>
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
        if (input) return input.value;
        return '';
    }

    document.getElementById('register-webauthn-btn').addEventListener('click', async function () {
        const btn = this;
        const alias = document.getElementById('key-alias').value.trim();
        const errEl = document.getElementById('webauthn-register-error');
        const okEl  = document.getElementById('webauthn-register-success');

        errEl.style.display = 'none';
        okEl.style.display  = 'none';

        btn.disabled = true;
        btn.querySelector('span').textContent = 'Bitte warten…';

        try {
            // Step 1: get attestation options
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

            // Convert base64url fields
            publicKey.challenge = base64urlToBuffer(publicKey.challenge);

            if (publicKey.user && publicKey.user.id) {
                publicKey.user = {
                    ...publicKey.user,
                    id: base64urlToBuffer(publicKey.user.id),
                };
            }

            if (publicKey.excludeCredentials) {
                publicKey.excludeCredentials = publicKey.excludeCredentials.map(cred => ({
                    ...cred,
                    id: base64urlToBuffer(cred.id),
                }));
            }

            // Step 2: browser prompt
            const credential = await navigator.credentials.create({ publicKey });

            // Step 3: encode result
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

            // Step 4: save
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

            setTimeout(() => window.location.reload(), 1200);

        } catch (e) {
            if (e.name === 'NotAllowedError') {
                errEl.textContent = 'Vorgang abgebrochen oder kein Sicherheitsschlüssel erkannt.';
            } else {
                errEl.textContent = e.message || 'Ein Fehler ist aufgetreten.';
            }
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.querySelector('span').textContent = 'YubiKey registrieren';
        }
    });
</script>
@endpush

@endsection
