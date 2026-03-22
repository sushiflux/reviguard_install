@extends('layouts.app')

@section('title', 'Passwort ändern')
@section('breadcrumb', 'Passwort ändern')

@section('content')
<div>
    <div class="card">
        <div class="card-header">
            <h2>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--c-accent1)" stroke-width="2" style="vertical-align:middle; margin-right:.4rem;">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Passwort ändern
            </h2>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div style="background:#D1FAE5; border:1px solid #6EE7B7; color:#065F46; padding:.75rem 1rem; border-radius:6px; margin-bottom:1rem; font-size:.875rem;">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Aktuelles Passwort *</label>
                    <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Neues Passwort *</label>
                    <input type="password" name="password" class="form-control" required autocomplete="new-password">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Neues Passwort bestätigen *</label>
                    <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:.5rem;">
                    <button type="submit" class="btn btn-primary">Passwort speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
