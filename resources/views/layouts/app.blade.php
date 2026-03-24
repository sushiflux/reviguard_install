<!DOCTYPE html>
<html lang="de" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReviGuard &mdash; @yield('title', 'Dashboard')</title>
    <style>
        /* ============================================================
           Farbschema (Markenfarben – unveränderlich)
           #0D1B2A  Hauptfarbe     – Deep Navy
           #1E40AF  Sekundärfarbe  – Corporate Blue
           #06B6D4  Akzentfarbe 1  – Cyan
           #F59E0B  Akzentfarbe 2  – Amber
           #F1F5F9  Neutral
        ============================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Markenfarben */
            --c-primary:   #0D1B2A;
            --c-secondary: #1E40AF;
            --c-accent1:   #06B6D4;
            --c-accent2:   #F59E0B;
            --c-neutral:   #F1F5F9;
            --c-surface:   #1A2B3C;
            --c-border:    rgba(6,182,212,.18);
            --c-muted:     #94A3B8;
            --sidebar-w:   260px;

            /* ── Light Mode (Standard) ── */
            --t-bg:            #F1F5F9;
            --t-surface:       #FFFFFF;
            --t-surface2:      #F8FAFC;
            --t-border:        #E2E8F0;
            --t-border2:       #F1F5F9;
            --t-text:          #1E293B;
            --t-text-muted:    #64748B;
            --t-text-sub:      #94A3B8;
            --t-input-bg:      #FFFFFF;
            --t-input-border:  #CBD5E1;
            --t-input-text:    #1E293B;
            --t-topbar-bg:     #FFFFFF;
            --t-topbar-border: #E2E8F0;
            --t-th-bg:         #F8FAFC;
            --t-tr-hover:      #F8FAFC;
            --t-modal-bg:      #FFFFFF;
            --t-label:         #475569;
            --t-toggle-bg:     #E2E8F0;
            --t-toggle-knob:   #FFFFFF;
        }

        /* ── Dark Mode ── */
        [data-theme="dark"] {
            --t-bg:            #0B1520;
            --t-surface:       #1A2B3C;
            --t-surface2:      #243447;
            --t-border:        rgba(6,182,212,.15);
            --t-border2:       rgba(6,182,212,.08);
            --t-text:          #E2EAF4;
            --t-text-muted:    #94A3B8;
            --t-text-sub:      #64748B;
            --t-input-bg:      #1A2B3C;
            --t-input-border:  rgba(6,182,212,.25);
            --t-input-text:    #E2EAF4;
            --t-topbar-bg:     #1A2B3C;
            --t-topbar-border: rgba(6,182,212,.15);
            --t-th-bg:         #243447;
            --t-tr-hover:      #243447;
            --t-modal-bg:      #1A2B3C;
            --t-label:         #94A3B8;
            --t-toggle-bg:     #06B6D4;
            --t-toggle-knob:   #FFFFFF;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--t-bg);
            color: var(--t-text);
            transition: background .25s, color .25s;
        }

        /* ── Sidebar (immer dunkel – Markenidentität) ─────────── */
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
            display: flex; flex-direction: column; align-items: center; gap: .5rem;
            padding: 1.5rem 1.25rem 1.25rem;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; text-align: center;
        }

        .sidebar-logo .logo-icon {
            width: 64px; height: 64px; flex-shrink: 0;
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
        }

        .sidebar-logo .logo-icon img { width: 64px; height: 64px; object-fit: contain; border-radius: 12px; }
        .sidebar-logo .brand { font-size: 1.1rem; font-weight: 700; color: #fff; }
        .sidebar-logo .sub   { font-size: .65rem; color: var(--c-accent1); letter-spacing: .1em; text-transform: uppercase; }

        /* Nav */
        .sidebar-nav { flex: 1; padding: 1rem 0; }

        .nav-section {
            padding: .5rem 1.25rem .25rem;
            font-size: .65rem; font-weight: 700;
            letter-spacing: .12em; text-transform: uppercase;
            color: #64748B;
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

        /* ── Sidebar Footer ──────────────────────────────────── */
        .sidebar-footer {
            border-top: 1px solid var(--c-border);
            padding: .75rem 0 0;
            flex-shrink: 0;
        }

        .sidebar-meta {
            padding: .6rem 1.25rem .9rem;
            border-top: 1px solid var(--c-border);
            margin-top: .5rem;
        }

        .sidebar-meta .meta-version {
            font-size: .65rem; font-weight: 700;
            color: #94A3B8; letter-spacing: .08em;
            text-transform: uppercase; margin-bottom: .25rem;
        }

        .sidebar-meta .meta-copy {
            font-size: .63rem; color: #64748B; line-height: 1.6;
        }

        .sidebar-meta .meta-heart { color: #F43F5E; }

        .sidebar-user {
            display: flex; align-items: center; gap: .6rem;
            padding: .6rem 1.25rem .5rem;
        }

        .sidebar-user .avatar {
            width: 30px; height: 30px; flex-shrink: 0;
            background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; color: #fff;
        }

        .sidebar-user .uname { font-size: .82rem; font-weight: 600; color: #CBD5E1; line-height: 1.2; margin-bottom: .3rem; }

        .sidebar-badge {
            display: inline-flex; align-items: center;
            padding: .15rem .45rem; border-radius: 999px;
            font-size: .62rem; font-weight: 700; letter-spacing: .03em;
            margin-right: .25rem;
        }

        .sb-admin        { background: rgba(30,64,175,.35);  color: #93C5FD; }
        .sb-projektadmin { background: rgba(245,158,11,.25); color: #FCD34D; }
        .sb-developer    { background: rgba(6,182,212,.2);   color: #67E8F9; }
        .sb-mitarbeiter  { background: rgba(100,116,139,.2); color: #94A3B8; }

        /* ── Session Timer ───────────────────────────────────── */
        .session-timer {
            display: flex; align-items: center; gap: .4rem;
            padding: .3rem .7rem; border-radius: 6px;
            font-size: .78rem; font-weight: 700; letter-spacing: .04em;
            color: var(--t-text-muted);
            border: 1px solid var(--t-border);
            transition: all .4s;
            font-variant-numeric: tabular-nums;
            margin-left: auto;
        }

        .session-timer svg { width: 13px; height: 13px; flex-shrink: 0; }

        .session-timer.warn {
            color: #D97706;
            background: rgba(245,158,11,.1);
            border-color: rgba(245,158,11,.35);
            animation: timerPulse 1.4s ease-in-out infinite;
        }

        .session-timer.critical {
            color: #DC2626;
            background: rgba(239,68,68,.1);
            border-color: rgba(239,68,68,.35);
            animation: timerPulse .7s ease-in-out infinite;
        }

        @keyframes timerPulse {
            0%, 100% { opacity: 1; }
            50%       { opacity: .55; }
        }

        /* ── Theme-Toggle ────────────────────────────────────── */
        .theme-toggle {
            display: flex; align-items: center; gap: .5rem;
            margin-left: auto;
            margin-right: .25rem;
        }

        .theme-toggle-label {
            font-size: .72rem; color: var(--t-text-muted);
            user-select: none;
        }

        .toggle-switch {
            position: relative;
            width: 40px; height: 22px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .toggle-switch input { display: none; }

        .toggle-track {
            position: absolute; inset: 0;
            background: var(--t-toggle-bg);
            border-radius: 999px;
            transition: background .25s;
        }

        .toggle-knob {
            position: absolute;
            top: 3px; left: 3px;
            width: 16px; height: 16px;
            border-radius: 50%;
            background: var(--t-toggle-knob);
            box-shadow: 0 1px 4px rgba(0,0,0,.25);
            transition: transform .25s, background .25s;
        }

        .toggle-switch input:checked ~ .toggle-track { background: var(--c-accent1); }
        .toggle-switch input:checked ~ .toggle-knob  { transform: translateX(18px); }

        .theme-icon {
            font-size: .85rem; line-height: 1;
            transition: opacity .2s;
        }

        /* ── Header user chip ───────────────────────────────── */
        .topbar-user {
            display: flex; align-items: center; gap: .5rem;
        }

        .topbar-user .avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--c-secondary), var(--c-accent1));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .topbar-user .uname { font-size: .82rem; font-weight: 600; color: var(--t-text); }

        /* ── Main ─────────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .topbar {
            height: 56px;
            background: var(--t-topbar-bg);
            border-bottom: 1px solid var(--t-topbar-border);
            display: flex; align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            transition: background .25s, border-color .25s;
        }

        .topbar .page-title { font-size: 1rem; font-weight: 600; color: var(--t-text); }
        .topbar .breadcrumb { font-size: .8rem; color: var(--t-text-muted); }
        .topbar .breadcrumb a { color: var(--c-accent1); text-decoration: none; }

        .content { flex: 1; padding: 2rem; }

        /* ── Cards ──────────────────────────────────────────────── */
        .card {
            background: var(--t-surface);
            border-radius: 10px;
            border: 1px solid var(--t-border);
            overflow: hidden;
            transition: background .25s, border-color .25s;
        }

        .card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--t-border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h2 { font-size: .95rem; font-weight: 700; color: var(--t-text); }
        .card-body { padding: 1.5rem; }

        /* ── Table ──────────────────────────────────────────────── */
        .tbl { width: 100%; border-collapse: collapse; font-size: .85rem; }

        .tbl th {
            padding: .75rem 1rem; text-align: left;
            font-size: .72rem; font-weight: 700; letter-spacing: .06em;
            text-transform: uppercase; color: var(--t-text-muted);
            border-bottom: 1px solid var(--t-border);
            background: var(--t-th-bg);
            transition: background .25s, border-color .25s;
        }

        .tbl td {
            padding: .75rem 1rem;
            border-bottom: 1px solid var(--t-border2);
            vertical-align: middle;
            color: var(--t-text);
            transition: color .25s, border-color .25s;
        }

        .tbl tbody tr:hover { background: var(--t-tr-hover); }
        .tbl tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ─────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: .2rem .6rem; border-radius: 999px;
            font-size: .72rem; font-weight: 600;
        }

        .badge-cyan   { background: rgba(6,182,212,.12);  color: #0891B2; }
        .badge-blue   { background: rgba(30,64,175,.12);  color: #1D4ED8; }
        .badge-amber  { background: rgba(245,158,11,.12); color: #D97706; }
        .badge-green  { background: rgba(34,197,94,.12);  color: #16A34A; }
        .badge-red    { background: rgba(239,68,68,.12);  color: #DC2626; }
        .badge-gray   { background: rgba(100,116,139,.12);color: #64748B; }

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
            background: transparent; color: var(--t-text-muted);
            border: 1px solid var(--t-border);
        }
        .btn-ghost:hover { color: var(--t-text); border-color: var(--t-text-muted); }
        .btn-sm { padding: .3rem .65rem; font-size: .75rem; }

        /* ── Forms ──────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.25rem; }

        .form-label {
            display: block; font-size: .75rem; font-weight: 600;
            letter-spacing: .06em; text-transform: uppercase;
            color: var(--t-label); margin-bottom: .4rem;
        }

        .form-control {
            width: 100%; padding: .6rem .875rem;
            border: 1px solid var(--t-input-border); border-radius: 7px;
            font-size: .875rem; color: var(--t-input-text);
            background: var(--t-input-bg); outline: none;
            transition: border-color .15s, box-shadow .15s, background .25s, color .25s;
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

        .alert-success { background: rgba(34,197,94,.1);  border-color: #16A34A; color: #15803D; }
        .alert-error   { background: rgba(239,68,68,.1);  border-color: #DC2626; color: #B91C1C; }

        [data-theme="dark"] .alert-success { color: #4ADE80; }
        [data-theme="dark"] .alert-error   { color: #F87171; }

        /* ── Modal ──────────────────────────────────────────────── */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.55); z-index: 200;
            align-items: center; justify-content: center;
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            background: var(--t-modal-bg);
            border: 1px solid var(--t-border);
            border-radius: 12px;
            padding: 1.75rem; width: 400px; max-width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
            color: var(--t-text);
            transition: background .25s;
        }

        .modal h3 { font-size: 1rem; font-weight: 700; margin-bottom: 1rem; color: var(--t-text); }
        .modal-footer { display: flex; gap: .75rem; justify-content: flex-end; margin-top: 1.25rem; }

        /* ── Rollen-Checkbox mit Tooltip ───────────────────────── */
        .role-checkbox-label {
            display: flex; align-items: center; gap: .4rem;
            padding: .4rem .75rem;
            border: 1px solid var(--t-border); border-radius: 6px;
            cursor: pointer; font-size: .83rem; font-weight: 500;
            color: var(--t-text-muted);
            position: relative;
        }

        .role-info-wrap {
            position: relative;
            display: inline-flex; align-items: center;
            margin-left: .1rem;
        }

        .role-info-icon {
            width: 14px; height: 14px;
            color: #94A3B8; cursor: help;
            transition: color .15s; flex-shrink: 0;
        }

        .role-info-wrap:hover .role-info-icon { color: var(--c-accent1); }

        .role-tooltip {
            display: none; position: absolute;
            bottom: calc(100% + 6px); left: 0;
            background: #1E293B; color: #F1F5F9;
            font-size: .75rem; font-weight: 400;
            padding: .4rem .7rem; border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,.3);
            pointer-events: none; z-index: 50;
        }

        .role-tooltip::after {
            content: ''; position: absolute;
            top: 100%; left: .6rem;
            border: 5px solid transparent;
            border-top-color: #1E293B;
        }

        .role-info-wrap:hover .role-tooltip { display: block; }

        /* ══════════════════════════════════════════════════════════
           DARK MODE — Globale Overrides für alle Views
           (!important überschreibt inline style="" Attribute)
        ══════════════════════════════════════════════════════════ */

        /* Globale Form-Elemente */
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="password"],
        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="number"],
        [data-theme="dark"] input[type="search"],
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background: var(--t-input-bg) !important;
            color: var(--t-input-text) !important;
            border-color: var(--t-input-border) !important;
        }

        /* Badge-Farben im Dark Mode */
        [data-theme="dark"] .badge-cyan   { background: rgba(6,182,212,.22)   !important; color: #67E8F9 !important; }
        [data-theme="dark"] .badge-blue   { background: rgba(59,130,246,.22)  !important; color: #93C5FD !important; }
        [data-theme="dark"] .badge-amber  { background: rgba(245,158,11,.22)  !important; color: #FCD34D !important; }
        [data-theme="dark"] .badge-green  { background: rgba(34,197,94,.22)   !important; color: #4ADE80 !important; }
        [data-theme="dark"] .badge-red    { background: rgba(239,68,68,.22)   !important; color: #F87171 !important; }
        [data-theme="dark"] .badge-gray   { background: rgba(100,116,139,.22) !important; color: #94A3B8 !important; }

        /* Tab-System */
        [data-theme="dark"] .tab-bar { border-color: var(--t-border) !important; }
        [data-theme="dark"] .tab-btn { color: var(--t-text-muted) !important; }

        /* Preference-Kacheln (profile/settings) */
        [data-theme="dark"] .pref-option {
            background: var(--t-surface2) !important;
            border-color: var(--t-border) !important;
            color: var(--t-text-muted) !important;
        }
        [data-theme="dark"] .pref-option:hover { background: rgba(6,182,212,.1) !important; }
        [data-theme="dark"] .pref-active {
            background: rgba(6,182,212,.12) !important;
            border-color: var(--c-accent1) !important;
            color: var(--c-accent1) !important;
        }
        [data-theme="dark"] .pref-preview {
            background: var(--t-surface) !important;
            border-color: var(--t-border) !important;
        }

        /* Accordion (admin/access) */
        [data-theme="dark"] .acc-list    { border-color: var(--t-border2) !important; }
        [data-theme="dark"] .acc-header  { border-color: var(--t-border2) !important; }
        [data-theme="dark"] .acc-header:hover     { background: var(--t-surface2) !important; }
        [data-theme="dark"] .acc-header.is-open   { background: var(--t-surface2) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .acc-uname   { color: var(--t-text) !important; }
        [data-theme="dark"] .acc-chevron { color: var(--t-border) !important; }
        [data-theme="dark"] .acc-body    { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .proj-assign-row  { border-color: var(--t-border2) !important; }
        [data-theme="dark"] .proj-assign-name { color: var(--t-text) !important; }
        [data-theme="dark"] .proj-none   { color: var(--t-text-muted) !important; }
        [data-theme="dark"] .role-option { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .role-option:hover    { background: rgba(6,182,212,.1) !important; }
        [data-theme="dark"] .role-option.selected { background: rgba(6,182,212,.15) !important; }
        [data-theme="dark"] .ro-name     { color: var(--t-text) !important; }

        /* Dashboard Projektkacheln */
        [data-theme="dark"] .proj-card {
            background: var(--t-surface) !important;
            border-color: var(--t-border) !important;
        }

        /* Revisionskarten (projects/show) */
        [data-theme="dark"] .rev-card   { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .type-filter-btn                       { background: var(--t-surface2) !important; border-color: var(--t-border) !important; color: var(--t-text-muted) !important; }
        [data-theme="dark"] .type-filter-btn[data-type="update"]   { background: rgba(59,130,246,.22)  !important; border-color: rgba(59,130,246,.4)  !important; color: #93C5FD !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn[data-type="change"]   { background: rgba(234,179,8,.22)   !important; border-color: rgba(234,179,8,.4)   !important; color: #FCD34D !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn[data-type="fix"]      { background: rgba(239,68,68,.22)   !important; border-color: rgba(239,68,68,.4)   !important; color: #F87171 !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn[data-type="release"]  { background: rgba(34,197,94,.22)   !important; border-color: rgba(34,197,94,.4)   !important; color: #4ADE80 !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn[data-type="hotfix"]   { background: rgba(239,68,68,.22)   !important; border-color: rgba(239,68,68,.4)   !important; color: #F87171 !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn[data-type="replaced"] { background: rgba(148,163,184,.22) !important; border-color: rgba(148,163,184,.4) !important; color: #94A3B8 !important; opacity: 1 !important; }
        [data-theme="dark"] .type-filter-btn.active-filter {
            background: rgba(6,182,212,.2) !important;
            color: var(--c-accent1) !important;
            border-color: var(--c-accent1) !important;
        }

        /* Typ-Badges in Revisionseinträgen (Journal + Liste) */
        [data-theme="dark"] .rev-type-badge[data-type="update"]   { background: rgba(59,130,246,.22)  !important; color: #93C5FD !important; border-color: rgba(59,130,246,.4)  !important; }
        [data-theme="dark"] .rev-type-badge[data-type="change"]   { background: rgba(234,179,8,.22)   !important; color: #FCD34D !important; border-color: rgba(234,179,8,.4)   !important; }
        [data-theme="dark"] .rev-type-badge[data-type="fix"]      { background: rgba(239,68,68,.22)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; }
        [data-theme="dark"] .rev-type-badge[data-type="release"]  { background: rgba(34,197,94,.22)   !important; color: #4ADE80 !important; border-color: rgba(34,197,94,.4)   !important; }
        [data-theme="dark"] .rev-type-badge[data-type="hotfix"]   { background: rgba(239,68,68,.22)   !important; color: #F87171 !important; border-color: rgba(239,68,68,.4)   !important; }
        [data-theme="dark"] .rev-type-badge[data-type="deaktiviert"] { background: rgba(100,116,139,.22) !important; color: #94A3B8 !important; border-color: rgba(100,116,139,.4) !important; }
        [data-theme="dark"] .rev-type-badge[data-type="broken"]      { background: rgba(15,23,42,.7)     !important; color: #CBD5E1 !important; border-color: rgba(71,85,105,.7)   !important; }
        [data-theme="dark"] .rev-type-badge[data-type="replaced"] { background: rgba(148,163,184,.22) !important; color: #94A3B8 !important; border-color: rgba(148,163,184,.4) !important; }

        /* Changelog manage */
        [data-theme="dark"] .cl-toolbar h2  { color: var(--t-text) !important; }
        [data-theme="dark"] .cl-table-wrap  { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .cl-table tbody tr       { border-color: var(--t-border2) !important; }
        [data-theme="dark"] .cl-table tbody tr:hover { background: var(--t-tr-hover) !important; }
        [data-theme="dark"] .cl-table td    { color: var(--t-text) !important; }
        [data-theme="dark"] .btn-edit       { background: var(--t-surface2) !important; border-color: var(--t-border) !important; color: var(--t-text-muted) !important; }
        [data-theme="dark"] .cl-empty       { color: var(--t-text-muted) !important; }

        /* Changelog Formular */
        [data-theme="dark"] .cl-form-card   { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .cl-form-body   { background: var(--t-surface) !important; }
        [data-theme="dark"] .cl-form-row label { color: var(--t-label) !important; }
        [data-theme="dark"] .cl-form-row input[type="text"],
        [data-theme="dark"] .cl-form-row textarea {
            background: var(--t-input-bg) !important;
            color: var(--t-input-text) !important;
            border-color: var(--t-input-border) !important;
        }
        [data-theme="dark"] .badge-toolbar  { background: var(--t-surface2) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .cl-hint        { color: var(--t-text-sub) !important; }
        [data-theme="dark"] .cl-release-row { background: rgba(245,158,11,.07) !important; border-color: rgba(245,158,11,.2) !important; }

        /* Changelog öffentlich */
        [data-theme="dark"] .cl-card        { background: var(--t-surface) !important; border-color: var(--t-border) !important; }
        [data-theme="dark"] .cl-card-body   { background: var(--t-surface) !important; }
        [data-theme="dark"] .cl-text        { color: var(--t-text-muted) !important; }
        [data-theme="dark"] .cl-line        { color: var(--t-text) !important; }

        /* ── Responsive ─────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main    { margin-left: 0; }
        }
    </style>

    {{-- Theme sofort setzen (vor Render, verhindert Flash) --}}
    <script>
        (function() {
            var t = localStorage.getItem('rg-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    @stack('styles')
</head>
<body>

{{-- ============================================================
     SIDEBAR
============================================================ --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <img src="{{ asset('logo.png') }}" alt="ReviGuard Logo">
        </div>
        <div class="brand">ReviGuard</div>
        <div class="sub">Revision Mgmt</div>
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

        {{-- Konto --}}
        <div class="nav-section" style="margin-top:.75rem;">Konto</div>

        <a href="{{ route('profile.roles') }}"
           class="nav-item {{ request()->routeIs('profile.roles') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <polyline points="9 12 11 14 15 10"/>
            </svg>
            Meine Berechtigungen
        </a>

        <a href="{{ route('profile.settings') }}"
           class="nav-item {{ request()->routeIs('profile.settings', 'profile.2fa*', 'profile.password') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            Einstellungen
        </a>

        @if(auth()->user()->hasRole('developer'))
        <div class="nav-section" style="margin-top:.75rem;">Entwickler</div>

        <a href="{{ route('changelog.manage') }}"
           class="nav-item {{ request()->routeIs('changelog.manage', 'changelog.create', 'changelog.edit') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                <rect x="9" y="3" width="6" height="4" rx="1"/>
                <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
            </svg>
            Changelog pflegen
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="nav-section" style="margin-top:.75rem;">Administration</div>

        <a href="{{ route('admin.access') }}"
           class="nav-item {{ request()->routeIs('admin.access', 'admin.users.*', 'admin.permissions.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Benutzer & Berechtigungen
        </a>

        <a href="{{ route('admin.settings') }}"
           class="nav-item {{ request()->routeIs('admin.settings', 'admin.system-admins.*', 'admin.2fa-policy*') ? 'active' : '' }}">
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
            <div class="avatar">{{ strtoupper(substr(auth()->user()->vorname, 0, 1) . substr(auth()->user()->nachname ?? '', 0, 1)) }}</div>
            <div>
                <div class="uname">{{ auth()->user()->name }}</div>
                @php
                    $roleNames = auth()->user()->roles->pluck('name')->toArray();
                    $hasDev    = in_array('developer', $roleNames);
                    $orgBadge  = in_array('administrator', $roleNames)
                                    ? ['label' => 'Administrator',       'class' => 'sb-admin']
                                    : (in_array('projektleiter_admin', $roleNames)
                                        ? ['label' => 'Projektadministrator', 'class' => 'sb-projektadmin']
                                        : ['label' => 'Mitarbeiter', 'class' => 'sb-mitarbeiter']);
                @endphp
                <div>
                    <span class="sidebar-badge {{ $orgBadge['class'] }}">{{ $orgBadge['label'] }}</span>
                    @if($hasDev)
                        <span class="sidebar-badge sb-developer">Developer</span>
                    @endif
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; text-align:left; color:#94A3B8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Abmelden
            </button>
        </form>
    </div>

    <div class="sidebar-meta">
        <a href="{{ route('changelog.index') }}" class="meta-version" style="text-decoration:none; cursor:pointer;" title="Changelog anzeigen">
            ReviGuard v{{ config('app.version', '0.5.1') }} ↗
        </a>
        <div class="meta-copy">
            Made with <span class="meta-heart">♥</span> by Michel Matthes<br>
            Powered by KI &nbsp;&middot;&nbsp; &copy; {{ date('Y') }}
        </div>
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

        @php $sessionTimeoutMin = (int) \App\Models\SystemSetting::get('session_timeout', 10); @endphp
        @if($sessionTimeoutMin > 0)
        {{-- Session Timer --}}
        <div class="session-timer" id="session-timer" title="Automatische Abmeldung bei Inaktivität">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span id="session-timer-display">{{ $sessionTimeoutMin }} min</span>
        </div>
        @endif

        {{-- Theme Toggle --}}
        <div class="theme-toggle" style="margin-left:{{ $sessionTimeoutMin > 0 ? '0' : 'auto' }}">
            <span class="theme-icon" id="theme-icon-sun">☀️</span>
            <label class="toggle-switch" title="Dark / Light Mode">
                <input type="checkbox" id="theme-checkbox">
                <div class="toggle-track"></div>
                <div class="toggle-knob"></div>
            </label>
            <span class="theme-icon" id="theme-icon-moon">🌙</span>
        </div>

        {{-- User --}}
        <div class="topbar-user">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->vorname, 0, 1) . substr(auth()->user()->nachname ?? '', 0, 1)) }}</div>
            <span class="uname">{{ auth()->user()->name }}</span>
        </div>
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

{{-- Hidden logout form für Session-Timeout --}}
<form id="auto-logout-form" method="POST" action="{{ route('logout') }}" style="display:none;">
    @csrf
</form>

@if($sessionTimeoutMin > 0)
<script>
(function () {
    var TIMEOUT_SEC  = {{ $sessionTimeoutMin * 60 }};
    var WARN_SEC     = 120;   // 2 Minuten
    var CRITICAL_SEC = 60;    // 1 Minute
    var remaining    = TIMEOUT_SEC;
    var timerEl      = document.getElementById('session-timer');
    var displayEl    = document.getElementById('session-timer-display');

    function pad(n) { return String(n).padStart(2, '0'); }

    function updateDisplay() {
        var m = Math.floor(remaining / 60);
        var s = remaining % 60;

        if (remaining <= WARN_SEC) {
            displayEl.textContent = pad(m) + ':' + pad(s);
        } else {
            displayEl.textContent = Math.ceil(remaining / 60) + ' min';
        }

        if (remaining <= CRITICAL_SEC) {
            timerEl.className = 'session-timer critical';
        } else if (remaining <= WARN_SEC) {
            timerEl.className = 'session-timer warn';
        } else {
            timerEl.className = 'session-timer';
        }
    }

    function resetTimer() {
        remaining = TIMEOUT_SEC;
        updateDisplay();
    }

    function tick() {
        if (remaining <= 0) {
            document.getElementById('auto-logout-form').submit();
            return;
        }
        remaining--;
        updateDisplay();
    }

    // Aktivität des Users setzt Timer zurück
    ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'].forEach(function (evt) {
        document.addEventListener(evt, resetTimer, { passive: true });
    });

    updateDisplay();
    setInterval(tick, 1000);
})();
</script>
@endif

<script>
(function () {
    var checkbox = document.getElementById('theme-checkbox');
    var html     = document.documentElement;

    // Aktuellen Stand wiederherstellen
    var current = localStorage.getItem('rg-theme') || 'light';
    checkbox.checked = (current === 'dark');

    checkbox.addEventListener('change', function () {
        var theme = this.checked ? 'dark' : 'light';
        html.setAttribute('data-theme', theme);
        localStorage.setItem('rg-theme', theme);
        if (typeof updateJournalTimeline === 'function') updateJournalTimeline();
    });
})();
</script>

@stack('scripts')
</body>
</html>
