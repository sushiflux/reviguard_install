<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReviGuard &mdash; Login</title>
    <style>
        /* ============================================================
           Farbschema
           #0D1B2A  Hauptfarbe     – Deep Navy
           #1E40AF  Sekundärfarbe  – Corporate Blue (Komplementär)
           #06B6D4  Akzentfarbe 1  – Cyan
           #F59E0B  Akzentfarbe 2  – Amber / Gold
           #F1F5F9  Neutral        – Helles Background
        ============================================================ */

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

        /* ---- Layout ---- */
        .page {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ---- Left: Login Panel (1/3) ---- */
        .login-panel {
            width: 33.333%;
            min-width: 360px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2.5rem;
            background: var(--c-primary);
            border-right: 1px solid var(--c-border);
            position: relative;
            z-index: 2;
        }

        .login-panel::after {
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

        /* Header block: Logo links, Text rechts */
        .login-header {
            display: flex;
            align-items: stretch;
            gap: 1.25rem;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            width: 90px;
            flex-shrink: 0;
            border-radius: 14px;
            overflow: hidden;
        }

        .logo-icon img { width: 100%; height: 100%; object-fit: contain; display: block; }

        .header-text { display: flex; flex-direction: column; justify-content: space-between; }
        .header-text .brand { font-size: 1.4rem; font-weight: 700; color: #fff; letter-spacing: .03em; line-height: 1.2; }
        .header-text .sub   { font-size: .7rem; color: var(--c-accent1); letter-spacing: .12em; text-transform: uppercase; }

        /* Headline */
        .login-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: .25rem;
        }

        .login-subtitle {
            font-size: .82rem;
            color: var(--c-text-muted);
            margin-bottom: 0;
        }

        /* Form */
        .form-group { margin-bottom: 1.25rem; }

        label {
            display: block;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--c-text-muted);
            margin-bottom: .45rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: .875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--c-text-muted);
            pointer-events: none;
            display: flex;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: .75rem .875rem .75rem 2.6rem;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 8px;
            color: #fff;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder { color: #475569; }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--c-accent1);
            box-shadow: 0 0 0 3px rgba(6,182,212,.15);
        }

        .input-error input {
            border-color: #EF4444;
        }

        .error-msg {
            margin-top: .35rem;
            font-size: .78rem;
            color: #EF4444;
        }

        /* Remember me */
        .remember-row {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.75rem;
        }

        .remember-row input[type="checkbox"] {
            accent-color: var(--c-accent1);
            width: 15px; height: 15px;
            cursor: pointer;
        }

        .remember-row label {
            margin: 0;
            font-size: .8rem;
            text-transform: none;
            letter-spacing: 0;
            color: var(--c-text-muted);
            cursor: pointer;
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: .85rem;
            background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: .9rem;
            font-weight: 600;
            letter-spacing: .04em;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--c-accent1), var(--c-secondary));
            opacity: 0;
            transition: opacity .3s;
        }

        .btn-login:hover::before { opacity: 1; }
        .btn-login:active { transform: scale(.98); }
        .btn-login span { position: relative; z-index: 1; }

        /* Footer */
        .login-footer {
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--c-border);
        }

        .version-info {
            font-size: .72rem;
            color: #334155;
            text-align: center;
        }

        .version-info a {
            color: var(--c-accent1);
            text-decoration: none;
        }

        .version-info a:hover { text-decoration: underline; }

        /* ---- Right: Visual Panel (2/3) ---- */
        .visual-panel {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: var(--c-surface);
        }

        /* Circuit-board pattern via SVG data-uri */
        .visual-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80'%3E%3Cg fill='none' stroke='%2306B6D4' stroke-width='.4' opacity='.18'%3E%3Crect x='10' y='10' width='8' height='8' rx='1'/%3E%3Crect x='60' y='10' width='8' height='8' rx='1'/%3E%3Crect x='10' y='60' width='8' height='8' rx='1'/%3E%3Crect x='60' y='60' width='8' height='8' rx='1'/%3E%3Crect x='35' y='35' width='10' height='10' rx='2'/%3E%3Cline x1='18' y1='14' x2='40' y2='14'/%3E%3Cline x1='40' y1='14' x2='40' y2='35'/%3E%3Cline x1='62' y1='14' x2='50' y2='14'/%3E%3Cline x1='50' y1='14' x2='50' y2='35'/%3E%3Cline x1='14' y1='18' x2='14' y2='40'/%3E%3Cline x1='14' y1='40' x2='35' y2='40'/%3E%3Cline x1='14' y1='62' x2='14' y2='50'/%3E%3Cline x1='14' y1='50' x2='35' y2='50'/%3E%3Cline x1='66' y1='18' x2='66' y2='40'/%3E%3Cline x1='66' y1='40' x2='45' y2='40'/%3E%3Cline x1='66' y1='62' x2='66' y2='50'/%3E%3Cline x1='66' y1='50' x2='45' y2='50'/%3E%3Cline x1='18' y1='64' x2='40' y2='64'/%3E%3Cline x1='40' y1='64' x2='40' y2='45'/%3E%3Cline x1='62' y1='64' x2='50' y2='64'/%3E%3Cline x1='50' y1='64' x2='50' y2='45'/%3E%3Ccircle cx='14' cy='14' r='2'/%3E%3Ccircle cx='66' cy='14' r='2'/%3E%3Ccircle cx='14' cy='66' r='2'/%3E%3Ccircle cx='66' cy='66' r='2'/%3E%3Ccircle cx='40' cy='14' r='1.5'/%3E%3Ccircle cx='14' cy='40' r='1.5'/%3E%3Ccircle cx='66' cy='40' r='1.5'/%3E%3Ccircle cx='40' cy='66' r='1.5'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 80px 80px;
        }

        /* Gradient overlay */
        .visual-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                135deg,
                rgba(13,27,42,.95) 0%,
                rgba(30,64,175,.6) 40%,
                rgba(6,182,212,.3) 70%,
                rgba(245,158,11,.15) 100%
            );
        }

        .visual-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 4rem;
        }

        /* Animated shield icon */
        .shield-wrap {
            margin-bottom: 2.5rem;
        }

        .shield-wrap svg {
            width: 80px;
            height: 80px;
            filter: drop-shadow(0 0 20px rgba(6,182,212,.5));
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { filter: drop-shadow(0 0 15px rgba(6,182,212,.4)); }
            50%       { filter: drop-shadow(0 0 35px rgba(6,182,212,.8)); }
        }

        .visual-headline {
            font-size: 2.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1rem;
            max-width: 480px;
        }

        .visual-headline span {
            background: linear-gradient(90deg, var(--c-accent1), var(--c-accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .visual-desc {
            font-size: 1rem;
            color: rgba(255,255,255,.65);
            max-width: 420px;
            line-height: 1.7;
            margin-bottom: 3rem;
        }

        /* Feature badges */
        .badges {
            display: flex;
            flex-direction: column;
            gap: .75rem;
        }

        .badge {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1rem;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(6,182,212,.2);
            border-radius: 8px;
            backdrop-filter: blur(4px);
            width: fit-content;
        }

        .badge-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-dot.cyan   { background: var(--c-accent1); box-shadow: 0 0 6px var(--c-accent1); }
        .badge-dot.amber  { background: var(--c-accent2); box-shadow: 0 0 6px var(--c-accent2); }
        .badge-dot.blue   { background: #60A5FA;          box-shadow: 0 0 6px #60A5FA; }

        .badge span {
            font-size: .8rem;
            color: rgba(255,255,255,.8);
            font-weight: 500;
        }

        /* Responsive: unterhalb 768px panels übereinander */
        @media (max-width: 768px) {
            .page { flex-direction: column; }
            .login-panel { width: 100%; min-width: unset; border-right: none; border-bottom: 1px solid var(--c-border); padding: 2rem 1.5rem; }
            .visual-panel { display: none; }
        }
    </style>
</head>
<body>
<div class="page">

    {{-- ============================================================
         LEFT — Login-Formular (1/3)
    ============================================================ --}}
    <div class="login-panel">

        <div class="login-header">
            <div class="logo-icon">
                <img src="{{ asset('logo.png') }}" alt="ReviGuard Logo">
            </div>
            <div class="header-text">
                <div class="brand">ReviGuard</div>
                <div class="sub">Revision Management</div>
                <h1 class="login-title">Willkommen zurück</h1>
                <p class="login-subtitle">Bitte melden Sie sich mit Ihren Zugangsdaten an.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            {{-- Benutzername --}}
            <div class="form-group {{ $errors->has('username') ? 'input-error' : '' }}">
                <label for="username">Benutzername</label>
                <div class="input-wrap">
                    <span class="icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Benutzername eingeben"
                        autocomplete="username"
                        autofocus
                    >
                </div>
                @error('username')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Passwort --}}
            <div class="form-group {{ $errors->has('password') ? 'input-error' : '' }}">
                <label for="password">Passwort</label>
                <div class="input-wrap">
                    <span class="icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Passwort eingeben"
                        autocomplete="current-password"
                    >
                </div>
                @error('password')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Angemeldet bleiben</label>
            </div>

            <button type="submit" class="btn-login">
                <span>Anmelden</span>
            </button>
        </form>

        <div class="login-footer">
            <div class="version-info">
                ReviGuard &nbsp;&bull;&nbsp; v{{ env('APP_VERSION', '0.1.0') }}
            </div>
            <div class="version-info" style="margin-top: .4rem;">
                &copy; {{ date('Y') }} ReviGuard. Alle Rechte vorbehalten.
            </div>
        </div>
    </div>

    {{-- ============================================================
         RIGHT — Visual (2/3)
    ============================================================ --}}
    <div class="visual-panel">
        <div class="visual-content">

            <div class="shield-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9 12l2 2 4-4" stroke="#06B6D4" stroke-width="1.5"/>
                </svg>
            </div>

            <h2 class="visual-headline">
                Revisionssichere<br>
                <span>Systemdokumentation</span>
            </h2>

            <p class="visual-desc">
                Dokumentieren, verwalten und prüfen Sie alle Systemänderungen
                lückenlos und manipulationssicher. Transparenz auf jeder Ebene.
            </p>

            <div class="badges">
                <div class="badge">
                    <div class="badge-dot cyan"></div>
                    <span>Revisionssichere Änderungshistorie</span>
                </div>
                <div class="badge">
                    <div class="badge-dot blue"></div>
                    <span>Rollenbasierte Zugriffskontrolle</span>
                </div>
                <div class="badge">
                    <div class="badge-dot amber"></div>
                    <span>Vollständiges Versions-Changelog</span>
                </div>
            </div>

        </div>
    </div>

</div>
</body>
</html>
