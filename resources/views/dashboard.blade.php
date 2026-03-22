@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px,1fr)); gap:1.25rem;">

    <div class="card" style="border-top: 3px solid var(--c-accent1);">
        <div class="card-body" style="text-align:center; padding: 2rem 1.5rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#06B6D4" stroke-width="1.5" style="margin-bottom:.75rem;">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
            <div style="font-size:1.75rem; font-weight:800; color:#1E293B;">–</div>
            <div style="font-size:.8rem; color:#94A3B8; margin-top:.25rem;">Projekte</div>
        </div>
    </div>

    <div class="card" style="border-top: 3px solid var(--c-secondary);">
        <div class="card-body" style="text-align:center; padding: 2rem 1.5rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1E40AF" stroke-width="1.5" style="margin-bottom:.75rem;">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            <div style="font-size:1.75rem; font-weight:800; color:#1E293B;">–</div>
            <div style="font-size:.8rem; color:#94A3B8; margin-top:.25rem;">Revisionen</div>
        </div>
    </div>

    <div class="card" style="border-top: 3px solid var(--c-accent2);">
        <div class="card-body" style="text-align:center; padding: 2rem 1.5rem;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.5" style="margin-bottom:.75rem;">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <div style="font-size:1.75rem; font-weight:800; color:#1E293B;">–</div>
            <div style="font-size:.8rem; color:#94A3B8; margin-top:.25rem;">Offene Einträge</div>
        </div>
    </div>

</div>

<div style="margin-top:2rem;">
    <div class="card">
        <div class="card-header">
            <h2>Meine Projekte</h2>
        </div>
        <div class="card-body" style="color:#94A3B8; font-size:.875rem; text-align:center; padding:3rem;">
            Noch keine Projekte vorhanden.
        </div>
    </div>
</div>
@endsection
