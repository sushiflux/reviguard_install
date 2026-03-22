@extends('layouts.app')

@section('title', 'TOTP einrichten')

@section('breadcrumb')
    <a href="{{ route('profile.2fa') }}">2FA</a> &rsaquo; TOTP einrichten
@endsection

@section('content')
<div style="max-width: 560px;">

    <div class="card">
        <div class="card-header">
            <h2>Authenticator-App einrichten</h2>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-error" style="margin-bottom: 1.25rem;">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <p style="font-size: .875rem; color: #475569; margin-bottom: 1.5rem; line-height: 1.6;">
                Scannen Sie den QR-Code mit einer Authenticator-App (z.&nbsp;B.&nbsp;Google Authenticator,
                Authy oder Microsoft Authenticator). Geben Sie anschließend den angezeigten 6-stelligen
                Code ein, um die Einrichtung zu bestätigen.
            </p>

            {{-- QR Code --}}
            <div style="display: flex; justify-content: center; margin-bottom: 1.75rem;">
                <div style="background: #fff; padding: 12px; border-radius: 10px; border: 1px solid #E2E8F0; display: inline-block;">
                    {!! $qrSvg !!}
                </div>
            </div>

            {{-- Manual secret --}}
            <div style="margin-bottom: 1.75rem;">
                <p style="font-size: .75rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; color: #475569; margin-bottom: .5rem;">
                    Manueller Schlüssel (falls QR-Code nicht scanbar)
                </p>
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 7px; padding: .65rem 1rem;
                            font-family: 'Courier New', monospace; font-size: .95rem; letter-spacing: .12em;
                            color: #1E293B; word-break: break-all; user-select: all;">
                    {{ $secret }}
                </div>
            </div>

            {{-- Confirmation form --}}
            <form method="POST" action="{{ route('profile.2fa.totp.confirm') }}">
                @csrf
                <div class="form-group {{ $errors->has('code') ? '' : '' }}">
                    <label class="form-label" for="code">Bestätigungscode (6 Ziffern)</label>
                    <input
                        type="text"
                        id="code"
                        name="code"
                        class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}"
                        inputmode="numeric"
                        maxlength="6"
                        placeholder="000000"
                        autocomplete="one-time-code"
                        autofocus
                        style="letter-spacing: .25em; text-align: center; font-size: 1.1rem;"
                        value="{{ old('code') }}"
                    >
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: .75rem; margin-top: .5rem;">
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Aktivieren
                    </button>
                    <a href="{{ route('profile.2fa') }}" class="btn btn-ghost">Abbrechen</a>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
