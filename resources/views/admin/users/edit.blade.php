@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')
@section('breadcrumb')
<a href="{{ route('admin.users.index') }}">Benutzer</a> &rsaquo; {{ $user->username }}
@endsection

@section('content')
<div class="card">
        <div class="card-header">
            <h2>{{ $user->username }} bearbeiten</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Zurück</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Benutzername</label>
                        <input type="text" class="form-control" value="{{ $user->username }}" disabled
                            style="background:#F8FAFC; color:#94A3B8; cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">E-Mail *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Vorname *</label>
                        <input type="text" name="vorname" class="form-control" value="{{ old('vorname', $user->vorname) }}" required>
                        @error('vorname')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nachname *</label>
                        <input type="text" name="nachname" class="form-control" value="{{ old('nachname', $user->nachname) }}" required>
                        @error('nachname')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Rollen</label>
                    <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.25rem;">
                        @foreach($roles as $role)
                        <label class="role-checkbox-label">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                style="accent-color: var(--c-accent1);"
                                {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
                            {{ $role->display_name }}
                            @if($role->description)
                            <span class="role-info-wrap">
                                <svg class="role-info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="8"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
                                <span class="role-tooltip">{{ $role->description }}</span>
                            </span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Abbrechen</a>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
@endsection
