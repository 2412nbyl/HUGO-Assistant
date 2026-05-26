@extends('layout')
@section('page-title', 'Staff Management')

@push('styles')
<style>
    /* ── Staff Card Grid ── */
    .sf-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    /* Filter bar */
    .sf-filter-bar {
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
    .sf-search-wrap {
        flex: 1; min-width: 200px; position: relative;
    }
    .sf-search-wrap input {
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
    .sf-search-wrap input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(220,38,38,.08);
        background: #fff;
    }
    .sf-search-wrap svg {
        position: absolute; left: 12px; top: 50%;
        transform: translateY(-50%);
        width: 15px; height: 15px;
        stroke: #9ca3af; fill: none; stroke-width: 2;
        pointer-events: none;
    }

    /* Staff table card */
    .sf-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
    }
    .sf-table {
        width: 100%;
        border-collapse: collapse;
    }
    .sf-table thead tr {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .sf-table th {
        padding: 12px 16px;
        font-size: 11.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        text-align: left;
        white-space: nowrap;
    }
    .sf-table th:last-child { text-align: right; }
    .sf-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background .12s;
    }
    .sf-table tbody tr:last-child { border-bottom: none; }
    .sf-table tbody tr:hover { background: #fafafa; }
    .sf-table td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #374151;
        vertical-align: middle;
    }
    .sf-table td:last-child { text-align: right; }

    /* Avatar */
    .sf-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px; font-weight: 700; color: #fff;
        flex-shrink: 0;
    }
    .sf-name-cell { display: flex; align-items: center; gap: 12px; }
    .sf-name-text { font-weight: 600; color: #111827; }
    .sf-pos-text { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .sf-id-tag { font-size: 11px; color: #9ca3af; font-family: monospace; }

    /* Status badge */
    .sf-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px; font-weight: 600;
        white-space: nowrap;
    }
    .sf-status-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .sf-status-active { background: #f0fdf4; color: #16a34a; }
    .sf-status-active .sf-status-dot { background: #16a34a; }
    .sf-status-inactive { background: #f9fafb; color: #6b7280; }
    .sf-status-inactive .sf-status-dot { background: #9ca3af; }

    /* Action buttons */
    .sf-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .sf-btn {
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
    .sf-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
    .sf-btn:hover { border-color: var(--accent); color: var(--accent); background: #fff5f0; }
    .sf-btn-del:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* Role pill */
    .role-pill {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px; font-weight: 600;
        text-transform: capitalize;
    }
    .role-admin    { background: #fee2e2; color: #b91c1c; }
    .role-notaris  { background: #dbeafe; color: #1d4ed8; }
    .role-staff    { background: #e0f2fe; color: #0369a1; }
    .role-freelancer { background: #f3e8ff; color: #7c3aed; }
    .role-klien    { background: #fef9c3; color: #b45309; }

    /* Responsive */
    @media (max-width: 768px) {
        .sf-table th:nth-child(4),
        .sf-table td:nth-child(4) { display: none; }
    }
    @media (max-width: 560px) {
        .sf-table th:nth-child(3),
        .sf-table td:nth-child(3) { display: none; }
    }
</style>
@endpush

@section('content')
<div class="animate-slide-up">

    {{-- ── Page Header ── --}}
    <div class="sf-header-row">
        <div>
            <p style="font-size:13.5px; color:#6b7280; margin:0;">Kelola data staff dan anggota tim</p>
        </div>
        <button class="btn btn-primary" onclick="window.location.href='{{ route('staff.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Staff
        </button>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px; display:flex; align-items:center; gap:8px;">
            <span>✔</span> {{ session('success') }}
        </div>
    @endif

    {{-- ── Filter Bar ── --}}
    <div class="sf-filter-bar">
        <div class="sf-search-wrap">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="sf-search" placeholder="Cari nama, jabatan, atau email..."
                oninput="filterStaffTable(this.value)" autocomplete="off">
        </div>
        <div style="font-size:12.5px; color:#6b7280; white-space:nowrap;">
            <span id="sf-count">{{ $staffs->count() }}</span> staff
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="sf-table-card">
        <table class="sf-table" id="sf-main-table">
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th style="text-align:center;">Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                $avatarColors = [
                    0 => '#dc2626', 1 => '#2563eb', 2 => '#0ea5e9',
                    3 => '#7c3aed', 4 => '#059669', 5 => '#d97706',
                ];
                @endphp
                @forelse($staffs as $i => $s)
                @php
                    $initial  = mb_strtoupper(mb_substr($s->name, 0, 1));
                    $color    = $avatarColors[$i % count($avatarColors)];
                    $isActive = strtolower($s->work_status) === 'aktif' || strtolower($s->work_status) === 'active';
                @endphp
                <tr class="sf-row" data-search="{{ strtolower($s->name . ' ' . $s->position . ' ' . ($s->email ?? '') . ' ' . $s->id_staff) }}">
                    <td>
                        <div class="sf-name-cell">
                            <div class="sf-avatar" style="background:{{ $color }};">{{ $initial }}</div>
                            <div>
                                <div class="sf-name-text">{{ $s->name }}</div>
                                <div class="sf-pos-text">{{ $s->position }}</div>
                                <div class="sf-id-tag">{{ $s->id_staff }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $userRole = optional(\App\Models\User::where('name', $s->name)->first())->role ?? 'staff';
                        @endphp
                        <span class="role-pill role-{{ $userRole }}">{{ ucfirst($userRole) }}</span>
                    </td>
                    <td>
                        @if($s->email)
                            <a href="mailto:{{ $s->email }}"
                                style="color:#374151; text-decoration:none; font-size:13px;">{{ $s->email }}</a>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="sf-status {{ $isActive ? 'sf-status-active' : 'sf-status-inactive' }}">
                            <span class="sf-status-dot"></span>
                            {{ $s->work_status }}
                        </span>
                    </td>
                    <td>
                        <div class="sf-actions">
                            <a href="{{ route('staff.edit', $s->id_staff) }}" class="sf-btn">
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('staff.destroy', $s->id_staff) }}" method="POST"
                                onsubmit="event.preventDefault(); showConfirm('Hapus Staff','Yakin hapus staff <strong>{{ addslashes($s->name) }}</strong>?',()=>this.submit(),'!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="sf-btn sf-btn-del">
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
                        <div style="text-align:center; padding:60px 24px;">
                            <div style="display:flex; justify-content:center; color:#9ca3af; margin-bottom:14px;">
                                <svg viewBox="0 0 24 24" style="width:48px;height:48px;stroke:currentColor;fill:none;stroke-width:1.5;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div style="font-size:16px; font-weight:700; color:#374151; margin-bottom:6px;">Belum ada data staff</div>
                            <div style="font-size:13.5px; color:#9ca3af;">Tambahkan staff pertama menggunakan tombol di atas</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top:18px; overflow-x:auto;">
        {{ $staffs->links() }}
    </div>

</div>
@endsection

@push('scripts')
<script>
function filterStaffTable(q) {
    var rows = document.querySelectorAll('#sf-main-table .sf-row');
    var term = q.toLowerCase().trim();
    var visible = 0;
    rows.forEach(function(row) {
        var match = !term || row.dataset.search.includes(term);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    var el = document.getElementById('sf-count');
    if (el) el.textContent = visible;
}
</script>
@endpush
