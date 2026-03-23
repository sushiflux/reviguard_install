@extends('layouts.app')

@section('title', 'Changelog')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; Changelog
@endsection

@section('content')
<style>
    /* ── Wrapper ── */
    .cl-wrap {
        position: relative;
        padding: .5rem 0 3rem;
    }

    /* Zentrale Zeitstrahllinie */
    .cl-wrap::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0; bottom: 0;
        width: 2px;
        transform: translateX(-50%);
        background: linear-gradient(
            to bottom,
            transparent,
            var(--c-accent1) 6%,
            var(--c-accent1) 94%,
            transparent
        );
        opacity: .35;
    }

    /* ── Versionsmarker (mittig auf dem Zeitstrahl) ── */
    .cl-version-marker {
        position: relative;
        display: flex;
        justify-content: center;
        margin: 2rem 0 1.5rem;
        z-index: 2;
    }

    .cl-version-marker:first-child { margin-top: 0; }

    .cl-version-pill {
        background: var(--c-primary);
        border: 2px solid var(--c-accent1);
        color: var(--c-accent1);
        font-size: .72rem; font-weight: 800;
        letter-spacing: .14em; text-transform: uppercase;
        padding: .35rem 1.1rem;
        border-radius: 999px;
        box-shadow: 0 0 0 4px rgba(6,182,212,.1), 0 2px 12px rgba(0,0,0,.15);
        white-space: nowrap;
    }

    /* ── Eintrag-Zeile (Grid: Karte | Punkt | Karte) ── */
    .cl-entry {
        display: grid;
        grid-template-columns: 1fr 60px 1fr;
        align-items: flex-start;
        margin-bottom: 2rem;
        position: relative;
    }

    /* ── Zeitstrahl-Punkt ── */
    .cl-dot-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 1.4rem;
        position: relative;
        z-index: 2;
    }

    .cl-dot {
        width: 16px; height: 16px;
        border-radius: 50%;
        background: var(--c-accent1);
        box-shadow: 0 0 0 4px rgba(6,182,212,.2), 0 0 12px rgba(6,182,212,.3);
        flex-shrink: 0;
    }

    /* Horizontaler Verbinder zum Knopf */
    .cl-entry.entry-left  .cl-dot-col::before,
    .cl-entry.entry-right .cl-dot-col::before {
        content: '';
        position: absolute;
        top: calc(1.4rem + 7px);
        height: 2px;
        background: var(--c-accent1);
        opacity: .4;
        width: 30px;
    }

    .cl-entry.entry-left  .cl-dot-col::before { left: 0; }
    .cl-entry.entry-right .cl-dot-col::before { right: 0; }

    /* ── Karte ── */
    .cl-card {
        background: #fff;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        overflow: hidden;
        transition: box-shadow .2s, transform .2s;
    }

    .cl-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,.1);
        transform: translateY(-2px);
    }

    /* Linke Karte: rechtsbündig ausrichten, Abstand zum Punkt */
    .cl-entry.entry-left .cl-card-col {
        padding-right: .5rem;
    }

    /* Rechte Karte: linksbündig, Abstand zum Punkt */
    .cl-entry.entry-right .cl-card-col {
        padding-left: .5rem;
    }

    /* Leere Spalte gegenüber */
    .cl-empty { /* nichts nötig */ }

    .cl-card-header {
        background: var(--c-primary);
        padding: .85rem 1.15rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .cl-card-title {
        font-size: .9rem; font-weight: 700;
        color: #fff; flex: 1;
        line-height: 1.3;
    }

    .cl-card-date {
        font-size: .68rem; font-weight: 600;
        letter-spacing: .08em; text-transform: uppercase;
        color: rgba(255,255,255,.4);
        white-space: nowrap;
    }

    .cl-card-body {
        padding: 1rem 1.15rem 1.15rem;
    }

    .cl-card-author {
        font-size: .72rem; font-weight: 600;
        color: var(--c-accent1); letter-spacing: .06em;
        text-transform: uppercase; margin-bottom: .65rem;
        display: flex; align-items: center; gap: .4rem;
    }

    .cl-card-author::before {
        content: '';
        display: inline-block;
        width: 5px; height: 5px;
        border-radius: 50%;
        background: var(--c-accent1);
    }

    /* Content-Zeilen */
    .cl-line {
        display: flex;
        align-items: flex-start;
        gap: .5rem;
        font-size: .82rem;
        color: #334155;
        line-height: 1.6;
        padding: .05rem 0;
    }

    .cl-line-dot {
        width: 5px; height: 5px;
        border-radius: 50%;
        background: var(--c-accent1);
        margin-top: .52rem;
        flex-shrink: 0;
        opacity: .7;
    }

    .cl-text {
        font-size: .82rem;
        color: #475569;
        line-height: 1.6;
        padding: .15rem 0;
    }

    /* ── Typ-Badges ── */
    .cl-badge {
        display: inline-block;
        font-size: .65rem; font-weight: 800;
        letter-spacing: .08em; text-transform: uppercase;
        padding: .1rem .45rem;
        border-radius: 4px;
        flex-shrink: 0;
        margin-top: .22rem;
        line-height: 1.4;
    }

    .cl-badge.badge-new      { background: rgba(34,197,94,.12);  border: 1px solid rgba(34,197,94,.35);  color: #16A34A; }
    .cl-badge.badge-add      { background: rgba(6,182,212,.12);  border: 1px solid rgba(6,182,212,.35);  color: #0E7490; }
    .cl-badge.badge-change   { background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.35); color: #B45309; }
    .cl-badge.badge-fix      { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.3);   color: #DC2626; }
    .cl-badge.badge-remove   { background: rgba(100,116,139,.1); border: 1px solid rgba(100,116,139,.3); color: #475569; }
    .cl-badge.badge-security { background: rgba(139,92,246,.1);  border: 1px solid rgba(139,92,246,.3);  color: #7C3AED; }

    /* ── Leer-Zustand ── */
    .cl-empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        color: var(--c-muted);
    }

    .cl-empty-state svg { margin-bottom: .75rem; opacity: .35; }

    /* ── Responsive ── */
    @media (max-width: 700px) {
        .cl-wrap::before { left: 20px; }

        .cl-entry {
            grid-template-columns: 40px 1fr;
        }

        .cl-entry.entry-left .cl-empty,
        .cl-entry.entry-right .cl-empty { display: none; }

        .cl-entry.entry-right {
            direction: ltr;
        }

        .cl-dot-col {
            grid-column: 1;
            grid-row: 1;
            padding-top: 1.2rem;
        }

        .cl-entry.entry-left  .cl-dot-col::before,
        .cl-entry.entry-right .cl-dot-col::before {
            left: auto; right: 0; width: 24px;
        }

        .cl-entry.entry-left  .cl-card-col,
        .cl-entry.entry-right .cl-card-col {
            grid-column: 2;
            grid-row: 1;
            padding-left: .5rem;
            padding-right: 0;
        }

        .cl-version-marker { justify-content: flex-start; padding-left: 4px; }
    }
</style>

@if($entries->isEmpty())
<div style="text-align:center; padding:4rem 2rem; color:var(--c-muted);">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:.75rem; opacity:.3"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
    <div>Noch keine Changelog-Einträge veröffentlicht.</div>
</div>
@else
<div class="cl-wrap">

    @php $i = 0; @endphp

    @foreach($entries as $version => $versionEntries)

    {{-- Versionsmarker mittig auf dem Zeitstrahl --}}
    <div class="cl-version-marker">
        <span class="cl-version-pill">v{{ $version }}</span>
    </div>

    @foreach($versionEntries as $entry)
    @php
        $side = ($i % 2 === 0) ? 'entry-left' : 'entry-right';
        $i++;
    @endphp

    <div class="cl-entry {{ $side }}">

        @php
            $cardHtml = ''; // Karte wird via Include gerendert
        @endphp

        @if($side === 'entry-left')
            <div class="cl-card-col">
                <div class="cl-card">
                    <div class="cl-card-header">
                        <span class="cl-card-title">{{ $entry->title }}</span>
                        <span class="cl-card-date">{{ $entry->released_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="cl-card-body">
                        <div class="cl-card-author">{{ $entry->author->name ?? '—' }}</div>
                        @include('changelog._content')
                    </div>
                </div>
            </div>
            <div class="cl-dot-col"><div class="cl-dot"></div></div>
            <div class="cl-empty"></div>
        @else
            <div class="cl-empty"></div>
            <div class="cl-dot-col"><div class="cl-dot"></div></div>
            <div class="cl-card-col">
                <div class="cl-card">
                    <div class="cl-card-header">
                        <span class="cl-card-title">{{ $entry->title }}</span>
                        <span class="cl-card-date">{{ $entry->released_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="cl-card-body">
                        <div class="cl-card-author">{{ $entry->author->name ?? '—' }}</div>
                        @include('changelog._content')
                    </div>
                </div>
            </div>
        @endif

    </div>
    @endforeach

    @endforeach

</div>
@endif
@endsection
