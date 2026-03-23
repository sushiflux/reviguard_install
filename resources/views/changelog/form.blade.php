@extends('layouts.app')

@section('title', $entry ? 'Eintrag bearbeiten' : 'Neuer Eintrag')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo;
<a href="{{ route('changelog.manage') }}">Changelog pflegen</a> &rsaquo;
{{ $entry ? 'Bearbeiten' : 'Neuer Eintrag' }}
@endsection

@section('content')
<style>
    .cl-form-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        max-width: 780px;
    }

    .cl-form-header {
        background: var(--c-primary);
        padding: 1rem 1.5rem;
        font-size: .95rem; font-weight: 600; color: #fff;
    }

    .cl-form-body { padding: 1.5rem; }

    .cl-form-row { margin-bottom: 1.25rem; }

    .cl-form-row label {
        display: block;
        font-size: .75rem; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #64748B; margin-bottom: .4rem;
    }

    .cl-form-row input[type="text"],
    .cl-form-row textarea {
        width: 100%;
        padding: .65rem .9rem;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        font-size: .875rem; color: #1E293B;
        background: #fff;
        transition: border-color .2s, box-shadow .2s;
        outline: none;
        font-family: inherit;
    }

    .cl-form-row input[type="text"]:focus,
    .cl-form-row textarea:focus {
        border-color: var(--c-accent1);
        box-shadow: 0 0 0 3px rgba(6,182,212,.12);
    }

    .cl-form-row textarea {
        min-height: 260px;
        resize: vertical;
        font-family: 'Cascadia Code', 'Fira Code', 'Consolas', monospace;
        font-size: .82rem;
        line-height: 1.7;
    }

    .cl-hint {
        font-size: .75rem; color: #94A3B8;
        margin-top: .35rem; line-height: 1.5;
    }

    /* ── Badge-Toolbar ── */
    .badge-toolbar {
        display: flex;
        align-items: center;
        gap: .4rem;
        flex-wrap: wrap;
        padding: .55rem .75rem;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
    }

    .badge-toolbar-label {
        font-size: .7rem; font-weight: 700;
        letter-spacing: .07em; text-transform: uppercase;
        color: #94A3B8; margin-right: .25rem;
    }

    .ins-badge {
        font-size: .68rem; font-weight: 800;
        letter-spacing: .08em; text-transform: uppercase;
        padding: .2rem .55rem;
        border-radius: 4px;
        cursor: pointer;
        border: 1px solid;
        transition: opacity .15s, transform .1s;
        line-height: 1.4;
    }

    .ins-badge:hover  { opacity: .75; }
    .ins-badge:active { transform: scale(.94); }

    .ins-badge.badge-new      { background: rgba(34,197,94,.12);  border-color: rgba(34,197,94,.35);  color: #16A34A; }
    .ins-badge.badge-add      { background: rgba(6,182,212,.12);  border-color: rgba(6,182,212,.35);  color: #0E7490; }
    .ins-badge.badge-change   { background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.35); color: #B45309; }
    .ins-badge.badge-fix      { background: rgba(239,68,68,.08);  border-color: rgba(239,68,68,.3);   color: #DC2626; }
    .ins-badge.badge-remove   { background: rgba(100,116,139,.1); border-color: rgba(100,116,139,.3); color: #475569; }
    .ins-badge.badge-security { background: rgba(139,92,246,.1);  border-color: rgba(139,92,246,.3);  color: #7C3AED; }

    /* Textarea direkt an Toolbar anschließen */
    .badge-toolbar + textarea {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-top-color: #E2E8F0;
    }

    .cl-release-row {
        display: flex; align-items: center; gap: .6rem;
        padding: .9rem 1rem;
        background: rgba(245,158,11,.05);
        border: 1px solid rgba(245,158,11,.2);
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .cl-release-row input[type="checkbox"] { accent-color: var(--c-accent2); width: 15px; height: 15px; cursor: pointer; }
    .cl-release-row label { font-size: .83rem; color: #92400E; cursor: pointer; margin: 0; text-transform: none; letter-spacing: 0; font-weight: 600; }

    .cl-form-actions { display: flex; gap: .75rem; }

    .error-msg { font-size: .78rem; color: #EF4444; margin-top: .3rem; }
</style>

<div class="cl-form-card">
    <div class="cl-form-header">
        {{ $entry ? 'Eintrag bearbeiten' : 'Neuen Changelog-Eintrag erstellen' }}
    </div>
    <div class="cl-form-body">
        <form method="POST"
              action="{{ $entry ? route('changelog.update', $entry) : route('changelog.store') }}">
            @csrf
            @if($entry) @method('PUT') @endif

            <div class="cl-form-row">
                <label for="version">Versionsnummer</label>
                <input type="text" id="version" name="version"
                       value="{{ old('version', $entry?->version ?? env('APP_VERSION', '')) }}"
                       placeholder="z. B. 0.3.2" autocomplete="off">
                @error('version')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="cl-form-row">
                <label for="title">Titel</label>
                <input type="text" id="title" name="title"
                       value="{{ old('title', $entry?->title) }}"
                       placeholder="z. B. Bugfix — Doppelte Erfolgsmeldung">
                @error('title')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            <div class="cl-form-row">
                <label for="content">Inhalt</label>

                {{-- Badge-Schnellauswahl --}}
                <div class="badge-toolbar">
                    <span class="badge-toolbar-label">Einfügen:</span>
                    <button type="button" class="ins-badge badge-new"      onclick="insertBadge('New')">New</button>
                    <button type="button" class="ins-badge badge-add"      onclick="insertBadge('Add')">Add</button>
                    <button type="button" class="ins-badge badge-change"   onclick="insertBadge('Change')">Change</button>
                    <button type="button" class="ins-badge badge-fix"      onclick="insertBadge('Fix')">Fix</button>
                    <button type="button" class="ins-badge badge-remove"   onclick="insertBadge('Remove')">Remove</button>
                    <button type="button" class="ins-badge badge-security" onclick="insertBadge('Security')">Security</button>
                </div>

                <textarea id="content" name="content"
                          placeholder="Beschreibe die Änderungen...">{{ old('content', $entry?->content) }}</textarea>
                <div class="cl-hint">
                    Klicke einen Badge an — er wird an der Cursorposition eingefügt. Danach einfach den Text tippen.
                </div>
                @error('content')<div class="error-msg">{{ $message }}</div>@enderror
            </div>

            @if(!$entry || !$entry->released_at)
            <div class="cl-release-row">
                <input type="checkbox" id="release" name="release" value="1"
                       {{ old('release') ? 'checked' : '' }}>
                <label for="release">Sofort veröffentlichen (für alle Benutzer sichtbar)</label>
            </div>
            @endif

            <div class="cl-form-actions">
                <button type="submit" class="btn btn-primary">Speichern</button>
                <a href="{{ route('changelog.manage') }}" class="btn" style="background:#fff; border:1px solid #CBD5E1; color:#475569;">Abbrechen</a>
            </div>
        </form>
    </div>
</div>

<script>
function insertBadge(label) {
    const ta    = document.getElementById('content');
    const start = ta.selectionStart;
    const end   = ta.selectionEnd;
    const val   = ta.value;

    // Prüfen ob die aktuelle Zeile bereits Text hat
    const lineStart  = val.lastIndexOf('\n', start - 1) + 1;
    const currentLine = val.substring(lineStart, start).trim();

    // Zeilenumbruch davor wenn aktuelle Zeile nicht leer
    const prefix = (currentLine.length > 0) ? '\n' : '';
    const insert = prefix + label + ': ';

    ta.value = val.substring(0, start) + insert + val.substring(end);
    const newPos = start + insert.length;
    ta.setSelectionRange(newPos, newPos);
    ta.focus();
}
</script>
@endsection
