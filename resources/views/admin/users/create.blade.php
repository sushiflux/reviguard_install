@extends('layouts.app')

@section('title', 'Neuer Benutzer')
@section('breadcrumb', '<a href="' . route('admin.users.index') . '">Benutzer</a> &rsaquo; Neu')

@section('content')
<div style="max-width:640px;">
    <div class="card">
        <div class="card-header">
            <h2>Neuen Benutzer anlegen</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Abbrechen</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Benutzername *</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
                        @error('username')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">E-Mail *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Passwort *</label>
                    <input type="password" name="password" class="form-control" placeholder="Mind. 8 Zeichen" required>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Rollen</label>
                    <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.25rem;">
                        @foreach($roles as $role)
                        <label style="display:flex; align-items:center; gap:.4rem; padding:.4rem .75rem; border:1px solid #E2E8F0; border-radius:6px; cursor:pointer; font-size:.83rem; font-weight:500; color:#475569;">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                style="accent-color: var(--c-accent1);"
                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                            {{ $role->display_name }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Abbrechen</a>
                    <button type="submit" class="btn btn-primary">Benutzer anlegen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
