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
                    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:.6rem;">

                        {{-- Kacheln --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="dashboard_view" value="tile"
                                   {{ $user->dashboard_view === 'tile' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->dashboard_view === 'tile' ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2"  y="2"  width="44" height="30" rx="3" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <rect x="6"  y="7"  width="22" height="4"  rx="1" fill="#94A3B8"/>
                                        <rect x="6"  y="14" width="34" height="3"  rx="1" fill="#CBD5E1"/>
                                        <rect x="6"  y="19" width="26" height="3"  rx="1" fill="#E2E8F0"/>
                                        <circle cx="9" cy="28" r="3" fill="#06B6D4"/>
                                        <rect x="54" y="2"  width="44" height="30" rx="3" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <rect x="58" y="7"  width="18" height="4"  rx="1" fill="#94A3B8"/>
                                        <rect x="58" y="14" width="34" height="3"  rx="1" fill="#CBD5E1"/>
                                        <rect x="58" y="19" width="28" height="3"  rx="1" fill="#E2E8F0"/>
                                        <circle cx="61" cy="28" r="3" fill="#F59E0B"/>
                                        <rect x="2"  y="36" width="44" height="30" rx="3" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <rect x="6"  y="41" width="26" height="4"  rx="1" fill="#94A3B8"/>
                                        <rect x="6"  y="48" width="34" height="3"  rx="1" fill="#CBD5E1"/>
                                        <rect x="6"  y="53" width="20" height="3"  rx="1" fill="#E2E8F0"/>
                                        <circle cx="9" cy="62" r="3" fill="#06B6D4"/>
                                        <rect x="54" y="36" width="44" height="30" rx="3" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <rect x="58" y="41" width="30" height="4"  rx="1" fill="#94A3B8"/>
                                        <rect x="58" y="48" width="34" height="3"  rx="1" fill="#CBD5E1"/>
                                        <rect x="58" y="53" width="24" height="3"  rx="1" fill="#E2E8F0"/>
                                        <circle cx="61" cy="62" r="3" fill="#F59E0B"/>
                                    </svg>
                                </div>
                                <span>Kacheln</span>
                            </div>
                        </label>

                        {{-- Liste --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="dashboard_view" value="list"
                                   {{ $user->dashboard_view === 'list' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->dashboard_view === 'list' ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2"  width="96" height="11" rx="2" fill="#E2E8F0"/>
                                        <rect x="5" y="5"  width="18" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="30" y="5" width="14" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="65" y="5" width="12" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="2" y="16" width="96" height="10" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="19" width="28" height="3"  rx="1" fill="#64748B"/>
                                        <rect x="30" y="19" width="18" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="80" y="18" width="14" height="5" rx="2" fill="#DCFCE7"/>
                                        <rect x="2" y="29" width="96" height="10" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="32" width="22" height="3"  rx="1" fill="#64748B"/>
                                        <rect x="30" y="32" width="22" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="80" y="31" width="14" height="5" rx="2" fill="#FEF9C3"/>
                                        <rect x="2" y="42" width="96" height="10" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="45" width="32" height="3"  rx="1" fill="#64748B"/>
                                        <rect x="30" y="45" width="16" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="80" y="44" width="14" height="5" rx="2" fill="#DCFCE7"/>
                                        <rect x="2" y="55" width="96" height="10" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="58" width="25" height="3"  rx="1" fill="#64748B"/>
                                        <rect x="30" y="58" width="20" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="80" y="57" width="14" height="5" rx="2" fill="#FEE2E2"/>
                                    </svg>
                                </div>
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
                    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:.6rem;">

                        {{-- Journal --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="revision_view" value="journal"
                                   {{ $user->revision_view === 'journal' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->revision_view === 'journal' ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <line x1="13" y1="4"  x2="13" y2="64" stroke="#E2E8F0" stroke-width="2"/>
                                        <circle cx="13" cy="12" r="5" fill="#06B6D4"/>
                                        <rect x="23" y="8.5" width="40" height="4" rx="1" fill="#334155"/>
                                        <rect x="23" y="15"  width="54" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="23" y="20"  width="42" height="3" rx="1" fill="#E2E8F0"/>
                                        <circle cx="13" cy="38" r="5" fill="#06B6D4"/>
                                        <rect x="23" y="34.5" width="32" height="4" rx="1" fill="#334155"/>
                                        <rect x="23" y="41"   width="50" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="23" y="46"   width="38" height="3" rx="1" fill="#E2E8F0"/>
                                        <circle cx="13" cy="62" r="5" fill="#06B6D4"/>
                                        <rect x="23" y="58.5" width="44" height="4" rx="1" fill="#334155"/>
                                    </svg>
                                </div>
                                <span>Journal</span>
                            </div>
                        </label>

                        {{-- Liste --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="revision_view" value="list"
                                   {{ $user->revision_view === 'list' ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->revision_view === 'list' ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2"  width="96" height="11" rx="2" fill="#E2E8F0"/>
                                        <rect x="5" y="5"  width="14" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="26" y="5" width="18" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="70" y="5" width="10" height="4" rx="1" fill="#94A3B8"/>
                                        <rect x="2" y="16" width="96" height="9" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="18.5" width="22" height="3" rx="1" fill="#64748B"/>
                                        <rect x="26" y="18.5" width="30" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="70" y="17.5" width="22" height="5" rx="2" fill="#CFFAFE"/>
                                        <rect x="2" y="28" width="96" height="9" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="30.5" width="18" height="3" rx="1" fill="#64748B"/>
                                        <rect x="26" y="30.5" width="26" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="70" y="29.5" width="22" height="5" rx="2" fill="#FEF9C3"/>
                                        <rect x="2" y="40" width="96" height="9" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="42.5" width="26" height="3" rx="1" fill="#64748B"/>
                                        <rect x="26" y="42.5" width="32" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="70" y="41.5" width="22" height="5" rx="2" fill="#CFFAFE"/>
                                        <rect x="2" y="52" width="96" height="9" rx="1" fill="#F8FAFC" stroke="#F1F5F9" stroke-width="1"/>
                                        <rect x="5" y="54.5" width="20" height="3" rx="1" fill="#64748B"/>
                                        <rect x="26" y="54.5" width="28" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="70" y="53.5" width="22" height="5" rx="2" fill="#CFFAFE"/>
                                    </svg>
                                </div>
                                <span>Liste</span>
                            </div>
                        </label>

                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Vorgänger-Revisionen</label>
                    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:.6rem;">

                        {{-- Eingeklappt --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="predecessors_expanded" value="0"
                                   {{ !$user->predecessors_expanded ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ !$user->predecessors_expanded ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="3"  width="96" height="14" rx="2" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="11" cy="10" r="4" fill="#06B6D4"/>
                                        <rect x="20" y="7"  width="38" height="4" rx="1" fill="#334155"/>
                                        <rect x="20" y="13" width="26" height="2.5" rx="1" fill="#CBD5E1"/>
                                        <polyline points="88,7 92,10 88,13" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="2" y="22" width="96" height="14" rx="2" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="11" cy="29" r="4" fill="#06B6D4"/>
                                        <rect x="20" y="26" width="44" height="4" rx="1" fill="#334155"/>
                                        <rect x="20" y="32" width="30" height="2.5" rx="1" fill="#CBD5E1"/>
                                        <polyline points="88,26 92,29 88,32" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="2" y="41" width="96" height="14" rx="2" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="11" cy="48" r="4" fill="#06B6D4"/>
                                        <rect x="20" y="45" width="32" height="4" rx="1" fill="#334155"/>
                                        <rect x="20" y="51" width="22" height="2.5" rx="1" fill="#CBD5E1"/>
                                        <polyline points="88,45 92,48 88,51" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <span>Eingeklappt</span>
                            </div>
                        </label>

                        {{-- Ausgeklappt --}}
                        <label style="flex:0 0 170px; cursor:pointer;">
                            <input type="radio" name="predecessors_expanded" value="1"
                                   {{ $user->predecessors_expanded ? 'checked' : '' }}
                                   style="display:none;" class="pref-radio">
                            <div class="pref-option {{ $user->predecessors_expanded ? 'pref-active' : '' }}">
                                <div class="pref-preview">
                                    <svg viewBox="0 0 100 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2"  width="96" height="13" rx="2" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1"/>
                                        <circle cx="11" cy="8.5" r="4" fill="#06B6D4"/>
                                        <rect x="20" y="5.5" width="38" height="4" rx="1" fill="#1D4ED8"/>
                                        <rect x="20" y="11"  width="24" height="2.5" rx="1" fill="#BFDBFE"/>
                                        <polyline points="88,6 91,8.5 88,11" stroke="#93C5FD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" transform="rotate(90 89.5 8.5)"/>
                                        <rect x="11" y="18" width="87" height="9" rx="2" fill="#F8FAFC" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="18" cy="22.5" r="2.5" fill="#94A3B8"/>
                                        <rect x="24" y="20.5" width="32" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="60" y="20"   width="18" height="4" rx="2" fill="#CFFAFE"/>
                                        <rect x="11" y="30" width="87" height="9" rx="2" fill="#F8FAFC" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="18" cy="34.5" r="2.5" fill="#94A3B8"/>
                                        <rect x="24" y="32.5" width="26" height="3" rx="1" fill="#CBD5E1"/>
                                        <rect x="60" y="32"   width="18" height="4" rx="2" fill="#FEF9C3"/>
                                        <rect x="2" y="43" width="96" height="13" rx="2" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1"/>
                                        <circle cx="11" cy="49.5" r="4" fill="#06B6D4"/>
                                        <rect x="20" y="46.5" width="44" height="4" rx="1" fill="#334155"/>
                                        <rect x="20" y="52"   width="28" height="2.5" rx="1" fill="#CBD5E1"/>
                                        <polyline points="88,47 92,49.5 88,52" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
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
    padding: .75rem;
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

.pref-preview {
    width: 100%;
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: .4rem;
}
.pref-preview svg { display: block; width: 100%; height: auto; }
.pref-active .pref-preview { border-color: rgba(6,182,212,.4); }
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
