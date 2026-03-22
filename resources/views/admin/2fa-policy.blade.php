@extends('layouts.app')

@section('title', '2FA-Richtlinie')

@section('breadcrumb')
    <a href="{{ route('admin.settings') }}">Administration</a> &rsaquo; 2FA-Richtlinie
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card" style="max-width: 600px;">
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

        {{-- Info box --}}
        <div style="background: rgba(6,182,212,.07); border: 1px solid rgba(6,182,212,.25);
                    border-radius: 8px; padding: .875rem 1rem; margin-bottom: 1.75rem;">
            <p style="font-size: .83rem; color: #0E7490; line-height: 1.6;">
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

                <div style="display: flex; flex-direction: column; gap: .75rem; margin-top: .25rem;">

                    {{-- none --}}
                    <label style="display: flex; align-items: flex-start; gap: .75rem; cursor: pointer;
                                  padding: .85rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0;
                                  background: {{ $policy === 'none' ? 'rgba(6,182,212,.06)' : '#fff' }};
                                  border-color: {{ $policy === 'none' ? 'var(--c-accent1)' : '#E2E8F0' }};">
                        <input type="radio" name="policy" value="none" {{ $policy === 'none' ? 'checked' : '' }}
                               style="margin-top: .15rem; accent-color: var(--c-accent1);">
                        <div>
                            <div style="font-size: .875rem; font-weight: 600; color: #1E293B; margin-bottom: .2rem;">
                                Keine Pflicht
                            </div>
                            <div style="font-size: .8rem; color: #64748B;">
                                Jeder Benutzer entscheidet selbst, ob er 2FA aktiviert.
                            </div>
                        </div>
                    </label>

                    {{-- any --}}
                    <label style="display: flex; align-items: flex-start; gap: .75rem; cursor: pointer;
                                  padding: .85rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0;
                                  background: {{ $policy === 'any' ? 'rgba(6,182,212,.06)' : '#fff' }};
                                  border-color: {{ $policy === 'any' ? 'var(--c-accent1)' : '#E2E8F0' }};">
                        <input type="radio" name="policy" value="any" {{ $policy === 'any' ? 'checked' : '' }}
                               style="margin-top: .15rem; accent-color: var(--c-accent1);">
                        <div>
                            <div style="font-size: .875rem; font-weight: 600; color: #1E293B; margin-bottom: .2rem;">
                                2FA erforderlich (TOTP <em>oder</em> YubiKey)
                            </div>
                            <div style="font-size: .8rem; color: #64748B;">
                                Alle Benutzer müssen mindestens eine 2FA-Methode nutzen.
                            </div>
                        </div>
                    </label>

                    {{-- totp --}}
                    <label style="display: flex; align-items: flex-start; gap: .75rem; cursor: pointer;
                                  padding: .85rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0;
                                  background: {{ $policy === 'totp' ? 'rgba(6,182,212,.06)' : '#fff' }};
                                  border-color: {{ $policy === 'totp' ? 'var(--c-accent1)' : '#E2E8F0' }};">
                        <input type="radio" name="policy" value="totp" {{ $policy === 'totp' ? 'checked' : '' }}
                               style="margin-top: .15rem; accent-color: var(--c-accent1);">
                        <div>
                            <div style="font-size: .875rem; font-weight: 600; color: #1E293B; margin-bottom: .2rem;">
                                TOTP erforderlich
                            </div>
                            <div style="font-size: .8rem; color: #64748B;">
                                Alle Benutzer müssen eine Authenticator-App (TOTP) einrichten und nutzen.
                            </div>
                        </div>
                    </label>

                    {{-- webauthn --}}
                    <label style="display: flex; align-items: flex-start; gap: .75rem; cursor: pointer;
                                  padding: .85rem 1rem; border-radius: 8px; border: 1px solid #E2E8F0;
                                  background: {{ $policy === 'webauthn' ? 'rgba(6,182,212,.06)' : '#fff' }};
                                  border-color: {{ $policy === 'webauthn' ? 'var(--c-accent1)' : '#E2E8F0' }};">
                        <input type="radio" name="policy" value="webauthn" {{ $policy === 'webauthn' ? 'checked' : '' }}
                               style="margin-top: .15rem; accent-color: var(--c-accent1);">
                        <div>
                            <div style="font-size: .875rem; font-weight: 600; color: #1E293B; margin-bottom: .2rem;">
                                YubiKey erforderlich
                            </div>
                            <div style="font-size: .8rem; color: #64748B;">
                                Alle Benutzer müssen einen Hardware-Sicherheitsschlüssel (WebAuthn) registrieren und nutzen.
                            </div>
                        </div>
                    </label>

                </div>

                @error('policy')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: .5rem;">
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Richtlinie speichern
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
