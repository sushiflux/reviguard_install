@extends('layouts.app')

@section('title', 'Neues Projekt')
@section('breadcrumb')
<a href="{{ route('projects.index') }}">Projekte</a> &rsaquo; Neu
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Neues Projekt anlegen</h2>
        <a href="{{ route('projects.index') }}" class="btn btn-ghost btn-sm">Abbrechen</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Projektname *</label>
                <input type="text" name="name" class="form-control"
                    value="{{ old('name') }}" required autofocus
                    placeholder="z.B. Netzwerk-Infrastruktur">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Beschreibung</label>
                <textarea name="description" class="form-control" rows="4"
                    placeholder="Kurze Beschreibung des Projekts...">{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex; justify-content:flex-end; gap:.75rem; margin-top:.5rem;">
                <a href="{{ route('projects.index') }}" class="btn btn-ghost">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Projekt anlegen</button>
            </div>
        </form>
    </div>
</div>
@endsection
