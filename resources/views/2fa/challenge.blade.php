<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReviGuard &mdash; Zwei-Faktor-Authentifizierung</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --c-primary:    #0D1B2A;
            --c-secondary:  #1E40AF;
            --c-accent1:    #06B6D4;
            --c-accent2:    #F59E0B;
            --c-neutral:    #F1F5F9;
            --c-surface:    #1A2B3C;
            --c-border:     rgba(6,182,212,.25);
            --c-text-muted: #94A3B8;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--c-primary);
        }

        .page {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ---- Left: Challenge Panel ---- */
        .challenge-panel {
            width: 33.333%;
            min-width: 380px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            background: var(--c-primary);
            border-right: 1px solid var(--c-border);
            position: relative;
            z-index: 2;
            overflow-y: auto;
        }

        .challenge-panel::after {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0;
            width: 1px;
            background: linear-gradient(to bottom,
                transparent,
                var(--c-accent1) 30%,
                var(--c-accent1) 70%,
                transparent);
            opacity: .5;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--c-accent1), var(--c-secondary));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .logo-icon svg { width: 24px; height: 24px; }
        .logo-text .brand { font-size: 1.3rem; font-weight: 700; color: #fff; letter-spacing: .03em; }
        .logo-text .sub   { font-size: .7rem; color: var(--c-accent1); letter-spacing: .12em; text-transform: uppercase; }

        .challenge-title {
            font-size: 1.5rem; font-weight: 700; color: #fff; margin-bottom: .4rem;
        }

        .challenge-subtitle {
            font-size: .875rem; color: var(--c-text-muted); margin-bottom: 2rem;
        }

        .user-chip {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .4rem .85rem;
            background: rgba(6,182,212,.1);
            border: 1px solid rgba(6,182,212,.25);
            border-radius: 999px;
            margin-bottom: 2rem;
        }

        .user-chip svg { width: 14px; height: 14px; color: var(--c-accent1); }
        .user-chip span { font-size: .82rem; color: var(--c-accent1); font-weight: 600; }

        /* Divider between methods */
        .method-divider {
            display: flex; align-items: center; gap: 1rem;
            margin: 1.75rem 0;
            color: var(--c-text-muted); font-size: .78rem;
        }

        .method-divider::before,
        .method-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--c-border);
        }

        /* Section heading */
        .method-heading {
            font-size: .72rem; font-weight: 700;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--c-text-muted); margin-bottom: 1rem;
        }

        /* Form */
        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: .75rem; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            color: var(--c-text-muted); margin-bottom: .45rem;
        }

        .input-wrap { position: relative; }

        .input-wrap .icon {
            position: absolute; left: .875rem; top: 50%;
            transform: translateY(-50%);
            color: var(--c-text-muted); pointer-events: none; display: flex;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: .75rem .875rem .75rem 2.6rem;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 8px;
            color: #fff;
            font-size: 1.1rem;
            letter-spacing: .2em;
            text-align: center;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: var(--c-accent1);
            box-shadow: 0 0 0 3px rgba(6,182,212,.15);
        }

        .input-error input {
            border-color: #EF4444;
        }

        .error-msg {
            margin-top: .35rem; font-size: .78rem; color: #EF4444;
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
            border: none; border-radius: 8px;
            color: #fff; font-size: .9rem; font-weight: 600;
            letter-spacing: .04em; cursor: pointer;
            transition: opacity .2s, transform .1s;
            position: relative; overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--c-accent1), var(--c-secondary));
            opacity: 0; transition: opacity .3s;
        }

        .btn-primary:hover::before { opacity: 1; }
        .btn-primary:active { transform: scale(.98); }
        .btn-primary span { position: relative; z-index: 1; }

        .btn-webauthn {
            width: 100%;
            padding: .85rem;
            background: rgba(245,158,11,.12);
            border: 1px solid rgba(245,158,11,.35);
            border-radius: 8px;
            color: var(--c-accent2); font-size: .9rem; font-weight: 600;
            letter-spacing: .04em; cursor: pointer;
            transition: background .2s, border-color .2s;
            display: flex; align-items: center; justify-content: center; gap: .6rem;
        }

        .btn-webauthn:hover {
            background: rgba(245,158,11,.2);
            border-color: rgba(245,158,11,.55);
        }

        .btn-webauthn svg { width: 18px; height: 18px; }

        .btn-webauthn:disabled {
            opacity: .5; cursor: not-allowed;
        }

        /* Back link */
        .back-link {
            display: inline-flex; align-items: center; gap: .4rem;
            margin-top: 1.75rem;
            font-size: .8rem; color: var(--c-text-muted); text-decoration: none;
            transition: color .15s;
        }

        .back-link:hover { color: var(--c-accent1); }
        .back-link svg { width: 14px; height: 14px; }

        /* Global error banner */
        .alert-error {
            padding: .75rem 1rem;
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 8px;
            color: #FCA5A5;
            font-size: .82rem;
            margin-bottom: 1.25rem;
        }

        /* ---- Right: Visual Panel ---- */
        .visual-panel {
            flex: 1; position: relative; overflow: hidden;
            background: var(--c-surface);
        }

        .visual-panel::before {
            content: '';
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cg fill='none' stroke='%2306B6D4' stroke-width='.4' opacity='.18'%3E%3Crect x='10' y='10' width='8' height='8' rx='1'/%3E%3Crect x='60' y='10' width='8' height='8' rx='1'/%3E%3Crect x='10' y='60' width='8' height='8' rx='1'/%3E%3Crect x='60' y='60' width='8' height='8' rx='1'/%3E%3Crect x='35' y='35' width='10' height='10' rx='2'/%3E%3Cline x1='18' y1='14' x2='40' y2='14'/%3E%3Cline x1='40' y1='14' x2='40' y2='35'/%3E%3Cline x1='62' y1='14' x2='50' y2='14'/%3E%3Cline x1='50' y1='14' x2='50' y2='35'/%3E%3Cline x1='14' y1='18' x2='14' y2='40'/%3E%3Cline x1='14' y1='40' x2='35' y2='40'/%3E%3Cline x1='14' y1='62' x2='14' y2='50'/%3E%3Cline x1='14' y1='50' x2='35' y2='50'/%3E%3Cline x1='66' y1='18' x2='66' y2='40'/%3E%3Cline x1='66' y1='40' x2='45' y2='40'/%3E%3Cline x1='66' y1='62' x2='66' y2='50'/%3E%3Cline x1='66' y1='50' x2='45' y2='50'/%3E%3Cline x1='18' y1='64' x2='40' y2='64'/%3E%3Cline x1='40' y1='64' x2='40' y2='45'/%3E%3Cline x1='62' y1='64' x2='50' y2='64'/%3E%3Cline x1='50' y1='64' x2='50' y2='45'/%3E%3Ccircle cx='14' cy='14' r='2'/%3E%3Ccircle cx='66' cy='14' r='2'/%3E%3Ccircle cx='14' cy='66' r='2'/%3E%3Ccircle cx='66' cy='66' r='2'/%3E%3Ccircle cx='40' cy='14' r='1.5'/%3E%3Ccircle cx='14' cy='40' r='1.5'/%3E%3Ccircle cx='66' cy='40' r='1.5'/%3E%3Ccircle cx='40' cy='66' r='1.5'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 80px 80px;
        }

        .visual-panel::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(
                135deg,
                rgba(13,27,42,.95) 0%,
                rgba(30,64,175,.6) 40%,
                rgba(6,182,212,.3) 70%,
                rgba(245,158,11,.15) 100%
            );
        }

        .visual-content {
            position: relative; z-index: 2; height: 100%;
            display: flex; flex-direction: column;
            justify-content: center; align-items: flex-start;
            padding: 4rem;
        }

        .shield-wrap { margin-bottom: 2.5rem; }

        .shield-wrap svg {
            width: 80px; height: 80px;
            filter: drop-shadow(0 0 20px rgba(6,182,212,.5));
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { filter: drop-shadow(0 0 15px rgba(6,182,212,.4)); }
            50%       { filter: drop-shadow(0 0 35px rgba(6,182,212,.8)); }
        }

        .visual-headline {
            font-size: 2.2rem; font-weight: 800; color: #fff;
            line-height: 1.15; margin-bottom: 1rem; max-width: 420px;
        }

        .visual-headline span {
            background: linear-gradient(90deg, var(--c-accent1), var(--c-accent2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .visual-desc {
            font-size: 1rem; color: rgba(255,255,255,.65);
            max-width: 380px; line-height: 1.7; margin-bottom: 2.5rem;
        }

        @media (max-width: 768px) {
            .page { flex-direction: column; }
            .challenge-panel { width: 100%; min-width: unset; border-right: none; }
            .visual-panel { display: none; }
        }
    </style>
</head>
<body>
<div class="page">

    <div class="challenge-panel">

        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <div class="logo-text">
                <div class="brand">ReviGuard</div>
                <div class="sub">Revision Management</div>
            </div>
        </div>

        <h1 class="challenge-title">Zwei-Faktor-Verifizierung</h1>
        <p class="challenge-subtitle">Bitte bestätigen Sie Ihre Identität.</p>

        <div class="user-chip">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span>{{ $user->username }}</span>
        </div>

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        {{-- ── TOTP Section ── --}}
        @if($hasTotp)
            <div class="method-heading">Authenticator-App (TOTP)</div>

            <form method="POST" action="{{ route('2fa.totp.verify') }}">
                @csrf
                <div class="form-group {{ $errors->has('code') ? 'input-error' : '' }}">
                    <label for="code">6-stelliger Code</label>
                    <div class="input-wrap">
                        <span class="icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            id="code"
                            name="code"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="000000"
                            autocomplete="one-time-code"
                            autofocus
                            value="{{ old('code') }}"
                        >
                    </div>
                    @error('code')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">
                    <span>Code bestätigen</span>
                </button>
            </form>
        @endif

        {{-- ── Divider if both methods ── --}}
        @if($hasTotp && $hasWebAuthn)
            <div class="method-divider">oder</div>
        @endif

        {{-- ── WebAuthn Section ── --}}
        @if($hasWebAuthn)
            <div class="method-heading">YubiKey / Hardware-Token</div>

            <div id="webauthn-error" class="alert-error" style="display:none;"></div>

            <button type="button" class="btn-webauthn" id="webauthn-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                </svg>
                <span>YubiKey verwenden</span>
            </button>
        @endif

        <a href="{{ route('login') }}" class="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Zurück zur Anmeldung
        </a>

    </div>

    <div class="visual-panel">
        <div class="visual-content">
            <div class="shield-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9 12l2 2 4-4" stroke="#06B6D4" stroke-width="1.5"/>
                </svg>
            </div>
            <h2 class="visual-headline">
                Sicherer<br>
                <span>Zwei-Faktor-Schutz</span>
            </h2>
            <p class="visual-desc">
                Ein zweiter Faktor schützt Ihr Konto auch dann, wenn Ihr Passwort
                kompromittiert wurde. Bitte bestätigen Sie Ihre Identität.
            </p>
        </div>
    </div>

</div>

@if($hasWebAuthn)
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
        // fallback: read from cookie
        const cookie = document.cookie.split(';').find(r => /^\s*XSRF-TOKEN\s*=/.test(r));
        if (cookie) {
            const val = cookie.split('=')[1].trim().replaceAll('%3D', '');
            return val;
        }
        return '';
    }

    function showWebAuthnError(msg) {
        const el = document.getElementById('webauthn-error');
        el.textContent = msg;
        el.style.display = 'block';
    }

    document.getElementById('webauthn-btn').addEventListener('click', async function() {
        const btn = this;
        btn.disabled = true;
        btn.querySelector('span').textContent = 'Bitte warten…';
        document.getElementById('webauthn-error').style.display = 'none';

        try {
            // Step 1: Get assertion options
            const optRes = await fetch('{{ route('2fa.webauthn.options') }}', {
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
                throw new Error(err.message || 'Fehler beim Laden der Challenge.');
            }

            const optJson = await optRes.json();
            const publicKey = optJson.publicKey ?? optJson;

            // Convert base64url fields to ArrayBuffer
            publicKey.challenge = base64urlToBuffer(publicKey.challenge);

            if (publicKey.allowCredentials) {
                publicKey.allowCredentials = publicKey.allowCredentials.map(cred => ({
                    ...cred,
                    id: base64urlToBuffer(cred.id),
                }));
            }

            // Step 2: Browser prompt
            const credential = await navigator.credentials.get({ publicKey });

            // Step 3: Encode result
            const payload = {
                id: credential.id,
                type: credential.type,
                rawId: bufferToBase64url(credential.rawId),
                authenticatorAttachment: credential.authenticatorAttachment ?? null,
                clientExtensionResults: credential.getClientExtensionResults(),
                response: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    authenticatorData: bufferToBase64url(credential.response.authenticatorData),
                    signature: bufferToBase64url(credential.response.signature),
                },
            };

            if (credential.response.userHandle) {
                payload.response.userHandle = bufferToBase64url(credential.response.userHandle);
            }

            // Step 4: Verify
            const verifyRes = await fetch('{{ route('2fa.webauthn.verify') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (verifyRes.status === 204 || verifyRes.ok) {
                window.location = '{{ route('dashboard') }}';
                return;
            }

            const errData = await verifyRes.json().catch(() => ({}));
            throw new Error(errData.error || 'Verifizierung fehlgeschlagen.');

        } catch (e) {
            if (e.name === 'NotAllowedError') {
                showWebAuthnError('Vorgang abgebrochen oder kein YubiKey erkannt.');
            } else {
                showWebAuthnError(e.message || 'Ein Fehler ist aufgetreten.');
            }
            btn.disabled = false;
            btn.querySelector('span').textContent = 'YubiKey verwenden';
        }
    });
</script>
@endif

</body>
</html>
