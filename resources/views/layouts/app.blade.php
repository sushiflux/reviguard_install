<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReviGuard &mdash; @yield('title', 'Dashboard')</title>
    <style>
        /* ============================================================
           Farbschema
           #0D1B2A  Hauptfarbe     – Deep Navy
           #1E40AF  Sekundärfarbe  – Corporate Blue
           #06B6D4  Akzentfarbe 1  – Cyan
           #F59E0B  Akzentfarbe 2  – Amber
           #F1F5F9  Neutral
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --c-primary:   #0D1B2A;
            --c-secondary: #1E40AF;
            --c-accent1:   #06B6D4;
            --c-accent2:   #F59E0B;
            --c-neutral:   #F1F5F9;
            --c-surface:   #1A2B3C;
            --c-border:    rgba(6,182,212,.18);
            --c-muted:     #94A3B8;
            --sidebar-w:   260px;
        }

        html, body { height: 100%; font-family: 'Segoe UI', system-ui, sans-serif; background: var(--c-neutral); color: #1E293B; }

        /* ── Sidebar ─────────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--c-primary);
            display: flex; flex-direction: column;
            border-right: 1px solid var(--c-border);
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex; align-items: center; gap: .75rem;
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0;
        }

        .sidebar-logo .logo-icon {
            width: 36px; height: 36px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--c-accent1), var(--c-secondary));
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }

        .sidebar-logo .logo-icon svg { width: 20px; height: 20px; }
        .sidebar-logo .brand { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .sidebar-logo .sub   { font-size: .65rem; color: var(--c-accent1); letter-spacing: .1em; text-transform: uppercase; }

        /* Nav */
        .sidebar-nav { flex: 1; padding: 1rem 0; }

        .nav-section {
            padding: .5rem 1.25rem .25rem;
            font-size: .65rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: #334155;
        }

        .nav-item {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.25rem;
            color: #94A3B8;
            text-decoration: none;
            font-size: .875rem;
            border-left: 3px solid transparent;
            transition: all .15s;
            position: relative;
        }

        .nav-item:hover { color: #fff; background: rgba(255,255,255,.05); }

        .nav-item.active {
            color: var(--c-accent1);
            background: rgba(6,182,212,.08);
            border-left-color: var(--c-accent1);
        }

        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--c-border);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex; align-items: center; gap: .6rem;
            margin-bottom: .75rem;
        }

        .sidebar-user .avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .sidebar-user .uname  { font-size: .8rem; font-weight: 600; color: #fff; }
        .sidebar-user .uroles { font-size: .68rem; color: var(--c-muted); }

        .btn-logout {
            display: flex; align-items: center; gap: .5rem;
            width: 100%; padding: .5rem .75rem;
            background: rgba(239,68,68,.1);
            border: 1px solid rgba(239,68,68,.2);
            border-radius: 6px;
            color: #FCA5A5; font-size: .78rem; cursor: pointer;
            transition: background .15s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.2); }
        .btn-logout svg   { width: 14px; height: 14px; }

        /* ── Main ─────────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .topbar {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            display: flex; align-items: center;
            padding: 0 2rem;
            gap: 1rem;
        }

        .topbar .page-title { font-size: 1rem; font-weight: 600; color: #1E293B; }
        .topbar .breadcrumb  { font-size: .8rem; color: var(--c-muted); margin-left: auto; }
        .topbar .breadcrumb a { color: var(--c-accent1); text-decoration: none; }

        .content { flex: 1; padding: 2rem; }

        /* ── Cards ──────────────────────────────────────────────── */
        .card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }

        .card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #E2E8F0;
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h2 { font-size: .95rem; font-weight: 700; color: #1E293B; }
        .card-body { padding: 1.5rem; }

        /* ── Table ──────────────────────────────────────────────── */
        .tbl { width: 100%; border-collapse: collapse; font-size: .85rem; }
        .tbl th {
            padding: .75rem 1rem; text-align: left;
            font-size: .72rem; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; color: var(--c-muted);
            border-bottom: 1px solid #E2E8F0; background: #F8FAFC;
        }
        .tbl td { padding: .75rem 1rem; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .tbl tbody tr:hover { background: #F8FAFC; }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ─────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: .2rem .6rem; border-radius: 999px;
            font-size: .72rem; font-weight: 600;
        }
        .badge-cyan   { background: rgba(6,182,212,.1);  color: #0891B2; }
        .badge-blue   { background: rgba(30,64,175,.1);  color: #1D4ED8; }
        .badge-amber  { background: rgba(245,158,11,.1); color: #D97706; }
        .badge-green  { background: rgba(34,197,94,.1);  color: #16A34A; }
        .badge-red    { background: rgba(239,68,68,.1);  color: #DC2626; }
        .badge-gray   { background: rgba(100,116,139,.1);color: #64748B; }

        /* ── Buttons ────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .45rem .9rem; border-radius: 6px;
            font-size: .8rem; font-weight: 600; cursor: pointer;
            border: none; text-decoration: none; transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn svg   { width: 14px; height: 14px; }

        .btn-primary { background: var(--c-secondary); color: #fff; }
        .btn-cyan    { background: var(--c-accent1);   color: #fff; }
        .btn-amber   { background: var(--c-accent2);   color: #fff; }
        .btn-danger  { background: #EF4444;            color: #fff; }
        .btn-ghost   {
            background: transparent; color: var(--c-muted);
            border: 1px solid #E2E8F0;
        }
        .btn-ghost:hover { color: #1E293B; border-color: #94A3B8; }
        .btn-sm { padding: .3rem .65rem; font-size: .75rem; }

        /* ── Forms ──────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block; font-size: .75rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: #475569; margin-bottom: .4rem;
        }
        .form-control {
            width: 100%; padding: .6rem .875rem;
            border: 1px solid #CBD5E1; border-radius: 7px;
            font-size: .875rem; color: #1E293B;
            background: #fff; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            border-color: var(--c-accent1);
            box-shadow: 0 0 0 3px rgba(6,182,212,.12);
        }
        .form-error { margin-top: .3rem; font-size: .78rem; color: #DC2626; }

        /* ── Alert ──────────────────────────────────────────────── */
        .alert {
            padding: .75rem 1rem; border-radius: 8px;
            font-size: .85rem; margin-bottom: 1.25rem;
            border-left: 3px solid;
        }
        .alert-success { background: rgba(34,197,94,.08);  border-color: #16A34A; color: #15803D; }
        .alert-error   { background: rgba(239,68,68,.08);  border-color: #DC2626; color: #B91C1C; }

        /* ── Modal ──────────────────────────────────────────────── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 200;
            align-items: center; justify-content: center;
        }
        .modal-backdrop.open { display: flex; }

        .modal {
            background: #fff; border-radius: 12px;
            padding: 1.75rem; width: 400px; max-width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
        .modal h3 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; }
        .modal-footer { display: flex; gap: .75rem; justify-content: flex-end; margin-top: 1.25rem; }

        /* ── Rollen-Checkbox mit Tooltip ───────────────────────── */
        .role-checkbox-label {
            display: flex; align-items: center; gap: .4rem;
            padding: .4rem .75rem;
            border: 1px solid #E2E8F0; border-radius: 6px;
            cursor: pointer; font-size: .83rem; font-weight: 500; color: #475569;
            position: relative;
        }

        .role-info-wrap {
            position: relative;
            display: inline-flex; align-items: center;
            margin-left: .1rem;
        }

        .role-info-icon {
            width: 14px; height: 14px;
            color: #94A3B8;
            cursor: help;
            transition: color .15s;
            flex-shrink: 0;
        }

        .role-info-wrap:hover .role-info-icon { color: var(--c-accent1); }

        .role-tooltip {
            display: none;
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0; transform: none;
            background: #1E293B;
            color: #F1F5F9;
            font-size: .75rem; font-weight: 400;
            padding: .4rem .7rem;
            border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,.25);
            pointer-events: none;
            z-index: 50;
        }

        .role-tooltip::after {
            content: '';
            position: absolute;
            top: 100%; left: .6rem;
            border: 5px solid transparent;
            border-top-color: #1E293B;
        }

        .role-info-wrap:hover .role-tooltip { display: block; }

        /* ── Responsive ─────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main    { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ============================================================
     SIDEBAR
============================================================ --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
        </div>
        <div>
            <div class="brand">ReviGuard</div>
            <div class="sub">Revision Mgmt</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Allgemein</div>

        <a href="{{ route('dashboard') }}"
           class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-section" style="margin-top:.75rem;">Administration</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Benutzer
        </a>

        <a href="{{ route('admin.permissions.index') }}"
           class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <line x1="3" y1="9" x2="21" y2="9"/>
                <line x1="3" y1="15" x2="21" y2="15"/>
                <line x1="9" y1="3" x2="9" y2="21"/>
                <line x1="15" y1="3" x2="15" y2="21"/>
            </svg>
            Berechtigungsmatrix
        </a>

        <a href="{{ route('admin.system-admins.index') }}"
           class="nav-item {{ request()->routeIs('admin.system-admins.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            System-Admins
        </a>

        <a href="{{ route('admin.settings') }}"
           class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93A10 10 0 0 0 4.93 19.07M4.93 4.93a10 10 0 0 0 14.14 14.14"/>
                <path d="M12 2v2m0 18v2M2 12h2m18 0h2m-4.93-7.07-1.41 1.41M6.34 17.66l-1.41 1.41M17.66 17.66l-1.41-1.41M6.34 6.34 4.93 4.93"/>
            </svg>
            Einstellungen
        </a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->username, 0, 2)) }}</div>
            <div>
                <div class="uname">{{ auth()->user()->username }}</div>
                <div class="uroles">
                    {{ auth()->user()->roles->pluck('display_name')->implode(', ') ?: '–' }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Abmelden
            </button>
        </form>
    </div>
</aside>

{{-- ============================================================
     MAIN
============================================================ --}}
<div class="main">
    <div class="topbar">
        <span class="page-title">@yield('title', 'Dashboard')</span>
        <span class="breadcrumb">
            <a href="{{ route('dashboard') }}">ReviGuard</a>
            @hasSection('breadcrumb') &rsaquo; @yield('breadcrumb') @endif
        </span>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
