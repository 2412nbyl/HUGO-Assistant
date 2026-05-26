@extends('layout')
@section('page-title', 'Client List')

@push('styles')
<style>
    /* ── Client Page Vars ── */
    .cl-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .cl-stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: box-shadow .15s, transform .15s;
    }
    .cl-stat-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,.07);
        transform: translateY(-1px);
    }
    .cl-stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-size: 20px;
    }
    .cl-stat-val {
        font-size: 26px; font-weight: 800; color: #111827; line-height: 1;
    }
    .cl-stat-label {
        font-size: 12px; color: #6b7280; margin-top: 2px; font-weight: 500;
    }

    /* ── Filter bar ── */
    .cl-filter-bar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .cl-search-wrap {
        flex: 1; min-width: 200px; position: relative;
    }
    .cl-search-wrap input {
        width: 100%;
        padding: 9px 14px 9px 38px;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        background: #f9fafb;
        color: #1f2937;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .cl-search-wrap input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(220,38,38,.08);
        background: #fff;
    }
    .cl-search-wrap svg {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        width: 15px; height: 15px;
        stroke: #9ca3af; fill: none; stroke-width: 2;
        pointer-events: none;
    }

    /* ── Client table card ── */
    .cl-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        /* Do NOT use overflow:hidden — it clips the horizontal scrollbar */
        overflow: visible;
    }
    .cl-table-scroll {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior-x: contain;
        border-radius: 16px; /* move rounding here instead */
    }
    .cl-table {
        width: 100%;
        min-width: 620px; /* always enforce — ensures Hapus button is visible */
        border-collapse: collapse;
    }
    .cl-table thead tr {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .cl-table th {
        padding: 12px 16px;
        font-size: 11.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        text-align: left;
        white-space: nowrap;
    }
    .cl-table th:last-child { text-align: right; }
    .cl-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .cl-table tbody tr:last-child { border-bottom: none; }
    .cl-table tbody tr:hover { background: #fafafa; }
    .cl-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }
    .cl-table td:last-child { text-align: right; }

    /* Avatar initials */
    .cl-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700; color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, var(--accent), var(--accent-hover));
    }
    .cl-name-cell {
        display: flex; align-items: center; gap: 12px;
    }
    .cl-name-text { font-weight: 600; color: #111827; }
    .cl-id-tag { font-size: 11px; color: #9ca3af; font-family: monospace; margin-top: 2px; }

    /* Case count badge */
    .case-count-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #eff6ff; color: #1d4ed8;
        border: 1px solid #dbeafe;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px; font-weight: 700;
    }
    .case-count-badge.zero {
        background: #f9fafb; color: #9ca3af; border-color: #e5e7eb;
    }

    /* Action buttons */
    .cl-actions { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: nowrap; }
    .cl-btn-edit {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 12px; font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
        white-space: nowrap;
    }
    .cl-btn-edit svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
    .cl-btn-edit:hover { border-color: var(--accent); color: var(--accent); background: #fff5f0; }
    .cl-btn-del {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        color: #374151;
        font-size: 12px; font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }
    .cl-btn-del svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
    .cl-btn-del:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* Empty state */
    .cl-empty {
        text-align: center;
        padding: 60px 24px;
    }
    .cl-empty-icon { font-size: 48px; margin-bottom: 14px; }
    .cl-empty-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .cl-empty-sub { font-size: 13.5px; color: #9ca3af; }

    /* Responsive */
    @media (max-width: 900px) {
        .cl-stat-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
        .cl-stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="animate-slide-up">

    {{-- ── Page Header ── --}}
    <div style="display:flex; align-items:center; justify-content:flex-end; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
        <button class="btn btn-primary" onclick="window.location.href='{{ route('clients.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Klien
        </button>
    </div>

    {{-- ── Stats ── --}}
    @php
        $totalKlien = \App\Models\Client::count();
        $withCases  = \App\Models\Client::has('cases')->count();
    @endphp
    <div class="cl-stat-grid">
        <div class="cl-stat-card">
            <div class="cl-stat-icon" style="background:#eff6ff; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#2563eb;fill:none;stroke-width:2;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="cl-stat-val">{{ number_format($totalKlien) }}</div>
                <div class="cl-stat-label">Total Klien</div>
            </div>
        </div>
        <div class="cl-stat-card">
            <div class="cl-stat-icon" style="background:#f0fdf4; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#16a34a;fill:none;stroke-width:2;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div class="cl-stat-val" style="color:#16a34a;">{{ $withCases }}</div>
                <div class="cl-stat-label">Klien Aktif (Ada Kasus)</div>
            </div>
        </div>
        <div class="cl-stat-card">
            <div class="cl-stat-icon" style="background:#fff7ed; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#ea580c;fill:none;stroke-width:2;"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <div>
                <div class="cl-stat-val" style="color:#ea580c;">{{ $totalKlien - $withCases }}</div>
                <div class="cl-stat-label">Klien Tanpa Kasus</div>
            </div>
        </div>
    </div>

    {{-- ── Alert ── --}}
    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; display:flex; align-items:center; gap:8px;">
            <span>✔</span> {{ session('success') }}
        </div>
    @endif

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('clients.index') }}" class="cl-filter-bar" style="gap:12px;">
        <div class="cl-search-wrap" style="flex:1;">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" id="cl-search-input" value="{{ request('search') }}" placeholder="Cari nama klien, telepon, atau ID..." autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 8px 18px; font-size: 13px; white-space: nowrap;">Cari</button>
        @if(request('search'))
            <a href="{{ route('clients.index') }}" class="btn btn-secondary" style="padding: 8px 14px; font-size: 13px; white-space: nowrap; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">✕ Reset</a>
        @endif
        <div style="font-size:12.5px; color:#6b7280; white-space:nowrap; margin-left: auto;">
            Menampilkan {{ $clients->count() }} dari {{ $clients->total() }} klien
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="cl-table-card">
        <div class="cl-table-scroll">
        <table class="cl-table" id="cl-main-table">
            <thead>
                <tr>
                    <th>Klien</th>
                    <th>Telepon</th>
                    <th>Tgl. Lahir</th>
                    <th style="text-align:center;">Kasus</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $c)
                @php
                    $initial = mb_strtoupper(mb_substr($c->name, 0, 1));
                    $count   = $c->cases_count ?? $c->cases()->count();
                @endphp
                <tr class="cl-row" data-search="{{ strtolower($c->name . ' ' . $c->phone . ' ' . $c->id_klien) }}">
                    <td>
                        <div class="cl-name-cell">
                            <div class="cl-avatar">{{ $initial }}</div>
                            <div>
                                <div class="cl-name-text">{{ $c->name }}</div>
                                <div class="cl-id-tag">{{ $c->id_klien }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($c->phone)
                            <a href="https://wa.me/62{{ ltrim($c->phone, '0') }}" target="_blank"
                                style="color:#16a34a; text-decoration:none; font-weight:500; display:inline-flex; align-items:center; gap:4px;">
                                <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ $c->phone }}
                            </a>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($c->birth_date)
                            <span style="font-weight:500;">{{ $c->birth_date->format('d/m/Y') }}</span>
                            <div style="font-size:11px; color:#9ca3af;">{{ $c->birth_date->age }} tahun</div>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="case-count-badge {{ $count === 0 ? 'zero' : '' }}">
                            {{ $count === 0 ? '—' : $count }}
                        </span>
                    </td>
                    <td>
                        <div class="cl-actions">
                            <a href="{{ route('clients.edit', $c->id_klien) }}" class="cl-btn-edit">
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('clients.destroy', $c->id_klien) }}" method="POST"
                                onsubmit="event.preventDefault(); showConfirm('Hapus Klien','Yakin hapus klien <strong>{{ addslashes($c->name) }}</strong>?',()=>this.submit(),'!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="cl-btn-del">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="cl-empty">
                            <div class="cl-empty-icon" style="display:flex; justify-content:center; color:#9ca3af; margin-bottom:14px;">
                                <svg viewBox="0 0 24 24" style="width:48px;height:48px;stroke:currentColor;fill:none;stroke-width:1.5;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div class="cl-empty-title">Belum ada klien terdaftar</div>
                            <div class="cl-empty-sub">Tambahkan klien pertama menggunakan tombol di atas</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:18px; overflow-x:auto; padding-bottom:4px;">
        {{ $clients->links() }}
    </div>

</div>
@endsection

@push('scripts')
<script>
// Server-side search implemented for clean pagination
</script>
@endpush
