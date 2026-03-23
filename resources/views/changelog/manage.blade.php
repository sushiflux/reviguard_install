@extends('layouts.app')

@section('title', 'Changelog pflegen')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; Changelog pflegen
@endsection

@section('content')
<style>
    .cl-toolbar {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .cl-toolbar h2 { font-size: 1rem; font-weight: 700; color: #1E293B; }

    .cl-table-wrap {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }

    .cl-table { width: 100%; border-collapse: collapse; }

    .cl-table thead th {
        background: var(--c-primary);
        color: rgba(255,255,255,.6);
        font-size: .72rem; font-weight: 700;
        letter-spacing: .08em; text-transform: uppercase;
        padding: .7rem 1rem; text-align: left;
    }

    .cl-table tbody tr { border-bottom: 1px solid #F1F5F9; }
    .cl-table tbody tr:last-child { border-bottom: none; }
    .cl-table tbody tr:hover { background: #F8FAFC; }

    .cl-table td { padding: .75rem 1rem; font-size: .875rem; color: #334155; vertical-align: middle; }

    .v-badge {
        display: inline-block;
        font-size: .72rem; font-weight: 700;
        letter-spacing: .1em; text-transform: uppercase;
        padding: .2rem .65rem; border-radius: 999px;
        background: rgba(6,182,212,.1);
        border: 1px solid rgba(6,182,212,.3);
        color: #0E7490;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        font-size: .72rem; font-weight: 600;
        padding: .2rem .65rem; border-radius: 999px;
    }

    .status-badge.released {
        background: rgba(34,197,94,.08);
        border: 1px solid rgba(34,197,94,.3);
        color: #16A34A;
    }

    .status-badge.draft {
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.3);
        color: #B45309;
    }

    .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .released .status-dot { background: #22C55E; }
    .draft    .status-dot { background: #F59E0B; }

    .cl-actions { display: flex; gap: .4rem; flex-wrap: nowrap; }

    .btn-sm {
        font-size: .75rem; font-weight: 600;
        padding: .3rem .7rem; border-radius: 6px;
        border: 1px solid; cursor: pointer;
        text-decoration: none; display: inline-block;
        white-space: nowrap; transition: opacity .15s;
    }

    .btn-sm:hover { opacity: .8; }
    .btn-edit    { background: #fff;              border-color: #CBD5E1; color: #475569; }
    .btn-release { background: rgba(34,197,94,.08); border-color: rgba(34,197,94,.4); color: #16A34A; }
    .btn-delete  { background: rgba(239,68,68,.06); border-color: rgba(239,68,68,.3); color: #DC2626; }

    .cl-empty {
        text-align: center; padding: 3rem;
        color: #94A3B8; font-size: .875rem;
    }
</style>

<div class="cl-toolbar">
    <h2>Changelog-Einträge</h2>
    <a href="{{ route('changelog.create') }}" class="btn btn-primary">+ Neuer Eintrag</a>
</div>

<div class="cl-table-wrap">
    @if($entries->isEmpty())
        <div class="cl-empty">Noch keine Einträge vorhanden.</div>
    @else
    <table class="cl-table">
        <thead>
            <tr>
                <th>Version</th>
                <th>Titel</th>
                <th>Erstellt von</th>
                <th>Erstellt am</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td><span class="v-badge">v{{ $entry->version }}</span></td>
                <td>{{ $entry->title }}</td>
                <td>{{ $entry->author->name ?? '—' }}</td>
                <td>{{ $entry->created_at->format('d.m.Y') }}</td>
                <td>
                    @if($entry->released_at)
                        <span class="status-badge released">
                            <span class="status-dot"></span>
                            Veröffentlicht
                        </span>
                    @else
                        <span class="status-badge draft">
                            <span class="status-dot"></span>
                            Entwurf
                        </span>
                    @endif
                </td>
                <td>
                    <div class="cl-actions">
                        <a href="{{ route('changelog.edit', $entry) }}" class="btn-sm btn-edit">Bearbeiten</a>
                        @if(!$entry->released_at)
                        <form method="POST" action="{{ route('changelog.release', $entry) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn-sm btn-release">Veröffentlichen</button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('changelog.destroy', $entry) }}" style="display:inline"
                              onsubmit="return confirm('Eintrag wirklich löschen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-delete">Löschen</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
