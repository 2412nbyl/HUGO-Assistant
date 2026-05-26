@extends('layout')
@section('page-title', 'Cases')
@section('content')
    <div class="animate-slide-up">
    <style>
        /* Filter Bar */
        .filter-bar {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-bar .filter-bar-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .search-wrap {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-wrap input {
            width: 100%;
            padding: 9px 14px 9px 38px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: #f9fafb;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .search-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(204, 51, 0, .09);
            background: #fff;
        }


        .search-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            color: #9ca3af;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            pointer-events: none;
        }

        .filter-divider {
            width: 1px;
            height: 30px;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .pill-group {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            align-items: center;
        }

        .pill-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-right: 2px;
        }

        .pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            color: #374151;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            transition: all .15s;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .pill:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .pill.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }


        .pill-type-PT .pill.active,
        .pill-type-PT .pill:hover {
            background: #3b82f6;
            border-color: var(--accent);
            color: var(--accent);
        }

        .pill-type-CV .pill.active,
        .pill-type-CV .pill:hover {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
        }

        .pill-type-Pribadi .pill.active,
        .pill-type-Pribadi .pill:hover {
            background: #ec4899;
            border-color: #ec4899;
            color: #fff;
        }

        .filter-submit {
            flex-shrink: 0;
        }

        /* Add-case modal: client dropdown — clear focus + selected state */
        #add-case-modal .client-select-shell label {
            font-weight: 600;
            color: #374151;
        }

        #add-case-modal .client-select-field {
            width: 100%;
            max-width: 100%;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 38px 12px 14px;
            font-size: 14px;
            font-weight: 500;
            background: #fafafa;
            color: #111827;
            transition: border-color .2s, box-shadow .2s, background .2s, color .2s;
            cursor: pointer;
            box-sizing: border-box;
        }

        #add-case-modal .client-select-field:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(204, 51, 0, 0.14);
            background: #fff;
        }

        #add-case-modal .client-select-field.has-client {
            border-color: rgba(204, 51, 0, 0.65);
            background: linear-gradient(180deg, #fff5f0, #fff);
            font-weight: 700;
            color: #7c2d12;
            box-shadow: 0 0 0 1px rgba(204, 51, 0, 0.12);
        }

        /* Cases list */
        .cases-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .cases-header span {
            font-size: 13.5px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .month-label {
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .month-label::after {
            content: '';
            flex: 1;
            height: 1.5px;
            background: #e5e7eb;
        }

        .case-row {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow .15s, border-color .15s;
        }

        .case-row:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
            border-color: #d1d5db;
        }

        .case-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }


        .case-name {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .case-sub {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }

        .bg-PT {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .bg-CV {
            background: #dcfce7;
            color: #15803d;
        }

        .bg-Pribadi {
            background: #fce7f3;
            color: #be185d;
        }

        .bg-selesai {
            background: #dcfce7;
            color: #16a34a;
        }

        .bg-proses {
            background: #fef9c3;
            color: #b45309;
        }

        .bg-tertunda {
            background: #fee2e2;
            color: #dc2626;
        }

        .case-deadline {
            font-size: 12px;
            color: var(--text-muted);
            margin-left: auto;
            margin-right: 10px;
            white-space: nowrap;
        }

        .btn-edit {
            width: 30px;
            height: 30px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            transition: all .15s;
            text-decoration: none;
        }

        .btn-edit:hover {
            border-color: var(--accent);
            background: #fff5f0;
            color: var(--accent);
        }


        .btn-edit svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .btn-del {
            width: 30px;
            height: 30px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            transition: all .15s;
        }

        .btn-del:hover {
            border-color: #ef4444;
            background: #fef2f2;
            color: #ef4444;
        }

        .btn-del svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        /* File chips */
        .file-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 8px;
            margin-top: 4px;
        }

        .file-chip {
            border: 1.5px dashed #d1d5db;
            border-radius: 8px;
            padding: 10px;
            font-size: 12.5px;
            color: #6b7280;
            cursor: pointer;
            text-align: center;
            transition: all .15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .file-chip:hover {
            border-color: #CC3300;
            background: #fff5f0;
            color: #CC3300;
        }

        .file-chip.ok {
            border-color: #22c55e;
            background: #f0fdf4;
            color: #16a34a;
        }

        .file-chip svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        /* ── Mobile responsive ────────────────────────────────────────── */
        @media (max-width: 768px) {
            .filter-bar { 
                flex-direction: column; 
                align-items: stretch !important; 
                padding: 16px; 
                gap: 16px; 
            }
            .search-wrap { width: 100%; min-width: 0; }
            .pill-group { width: 100%; justify-content: flex-start; }
            .filter-divider { display: none; }
            .cases-header { flex-direction: column; align-items: flex-start; gap: 10px; }
            .cases-header > div { width: 100%; flex-wrap: wrap; }
            .case-row-top { flex-wrap: wrap !important; gap: 12px !important; padding: 16px !important; }
            .case-info-main { min-width: 100% !important; flex: none !important; }
            .case-meta-tags, .case-status-wrap, .case-deadline-wrap, .case-actions { 
                width: 100% !important; 
                margin: 0 !important; 
                justify-content: flex-start !important; 
            }
            .case-deadline { margin-left: 0; }
            #add-case-modal .form-grid {
                padding: 16px !important;
                gap: 12px !important;
            }
            #add-case-modal .client-select-field {
                min-height: 48px;
                font-size: 16px;
            }
        }
        @media (max-width: 480px) {
            .case-name { font-size: 13.5px; }
            .case-row-top .badge { font-size: 10.5px; padding: 3px 9px; }
            #add-case-modal .modal-box {
                max-width: 100% !important;
            }
        }

        /* ─── Case Card (replaces .case-row) ─────────────────────────────── */
        .case-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            margin-bottom: 10px;
            overflow: hidden;
            transition: box-shadow .15s, border-color .15s;
        }
        .case-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,.09);
            border-color: #d1d5db;
        }

        .case-row-top {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 18px;
            flex-wrap: nowrap;
        }

        .case-name-block {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .case-id-tag {
            font-family: monospace;
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 0.5px;
        }

        .case-client-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #111827;
            line-height: 1.2;
        }

        /* ─── Progress Zone ───────────────────────────────────────────────── */
        .case-progress-zone {
            padding: 0 18px 12px;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .progress-track {
            height: 6px;
            background: #f3f4f6;
            border-radius: 99px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 99px;
            width: 0%; /* animated in via JS */
            transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1),
                        background 0.35s ease;
        }

        .progress-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .progress-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            transition: background 0.3s;
        }

        .progress-status-lbl {
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
            flex-shrink: 0;
            transition: color 0.3s;
        }

        .case-note-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 12.5px;
            color: #6b7280;
            font-family: 'Inter', sans-serif;
            padding: 2px 6px;
            border-bottom: 1.5px solid transparent;
            transition: border-color .2s, color .2s;
            min-width: 0;
        }
        .case-note-input:focus {
            border-bottom-color: var(--accent);
            color: #1f2937;
        }
        .case-note-input::placeholder { color: #d1d5db; font-style: italic; }

        .note-save-indicator {
            font-size: 11px;
            color: #22c55e;
            flex-shrink: 0;
            transition: opacity .3s;
            opacity: 0;
            min-width: 24px;
        }
        .note-save-indicator.show { opacity: 1; }
    </style>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <form id="filter-form" method="GET" action="{{ route('cases.index') }}" class="filter-bar-form">
            <!-- Hidden inputs to submit Type & Status in unison -->
            <input type="hidden" name="type" id="filter-type" value="{{ request('type') }}">
            <input type="hidden" name="status" id="filter-status" value="{{ request('status') }}">

            <!-- Search -->
            <div class="search-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama klien atau kasus..." onkeydown="if(event.key === 'Enter') { this.form.submit(); }">
            </div>

            <div class="filter-divider"></div>

            <!-- Type filter pills -->
            <div class="pill-group">
                <span class="pill-label">Tipe</span>
                <button type="button" onclick="setFilter('type', '', this)"
                    class="pill {{ !request('type') ? 'active' : '' }}">Semua</button>
                @foreach (['PT', 'CV', 'Pribadi'] as $t)
                    <button type="button" onclick="setFilter('type', '{{ $t }}', this)"
                        class="pill pill-type-{{ $t }} {{ request('type') === $t ? 'active' : '' }}">{{ $t }}</button>
                @endforeach
            </div>

            <div class="filter-divider"></div>

            <!-- Status filter pills -->
            <div class="pill-group">
                <span class="pill-label">Status</span>
                <button type="button" onclick="setFilter('status', '', this)"
                    class="pill {{ !request('status') ? 'active' : '' }}">Semua</button>
                @foreach (['proses' => 'Proses', 'selesai' => 'Selesai', 'tertunda' => 'Tertunda'] as $val => $label)
                    <button type="button" onclick="setFilter('status', '{{ $val }}', this)"
                        class="pill {{ request('status') === $val ? 'active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>

            <div class="filter-divider"></div>

            <!-- Month + Year filter -->
            <div class="pill-group" style="align-items:center; gap:6px;">
                <span class="pill-label">Periode</span>
                <select name="month" data-native-select="true" onchange="this.form.submit()"
                    style="padding:6px 10px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:12.5px; font-family:'Inter',sans-serif; color:#374151; background:#fff; outline:none; cursor:pointer; appearance:auto;">
                    <option value="">Semua Bulan</option>
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agt',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'] as $num => $name)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="year" data-native-select="true" onchange="this.form.submit()"
                    style="padding:6px 10px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:12.5px; font-family:'Inter',sans-serif; color:#374151; background:#fff; outline:none; cursor:pointer; appearance:auto;">
                    <option value="">Semua Tahun</option>
                    @for($y = date('Y'); $y >= date('Y') - 4; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="btn btn-primary filter-submit" style="padding:8px 18px;font-size:13px;">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Cari
            </button>

            @if(request()->hasAny(['search','type','status','month','year']))
            <a href="{{ route('cases.index') }}" class="btn btn-secondary" style="padding:8px 14px;font-size:13px;white-space:nowrap;margin-left:8px;" title="Hapus semua filter">
                ✕ Reset
            </a>
            @endif
        </form>
    </div>

    <!-- HEADER ROW -->
    <div class="cases-header">
        <span>{{ $cases->count() }} kasus ditemukan</span>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <!-- Export Buttons (admin/notaris/staff only) -->
            @if(in_array(auth()->user()->role, ['admin','notaris','staff']))
            <a href="{{ route('export.cases','csv') . '?' . http_build_query(request()->except('_token')) }}"
               class="btn-export btn-csv">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </a>
            <a href="{{ route('export.cases','pdf') . '?' . http_build_query(request()->except('_token')) }}"
               class="btn-export btn-pdf" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                PDF
            </a>
            @endif
            <button class="btn btn-primary" onclick="openAddCaseModal()">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                Tambah Kasus
            </button>
        </div>
    </div>

    <!-- CASES LIST grouped by month -->
    @php
        $grouped = $cases->groupBy(fn($c) => $c->created_at->translatedFormat('F Y'));
    @endphp

    @forelse($grouped as $month => $items)
        <div style="margin-bottom:24px;">
            <div class="month-label">{{ $month }}</div>
            @foreach ($items as $case)
                @php
                    $statusColors = [
                        'proses'   => ['bar' => '#f59e0b', 'bg' => '#fef3c7', 'text' => '#92400e', 'pct' => 45],
                        'selesai'  => ['bar' => '#22c55e', 'bg' => '#dcfce7', 'text' => '#166534', 'pct' => 100],
                        'tertunda' => ['bar' => '#ef4444', 'bg' => '#fee2e2', 'text' => '#991b1b', 'pct' => 20],
                    ];
                    $sc = $statusColors[$case->status] ?? $statusColors['proses'];
                @endphp
                <div class="case-card" id="case-{{ $case->id_kasus }}">

                    {{-- ── TOP ROW: Main Info ── --}}
                    <div class="case-row-top">
                        <div class="case-avatar">{{ mb_substr($case->client_name, 0, 1) }}</div>
                        
                        <div class="case-info-main" style="flex:1; min-width:180px;">
                            <div class="case-name-block">
                                <span class="case-id-tag">{{ $case->id_kasus }}</span>
                                <span class="case-client-name">{{ $case->client_name }}</span>
                            </div>
                            <div class="case-sub">
                                {{ $case->case_name }}
                                @if($case->nominal_bayar) • <span style="color:#111827; font-weight:600;">Rp. {{ number_format($case->nominal_bayar, 0, ',', '.') }}</span> @endif
                                @if($case->phone) • <span style="color:#6b7280;">+62 {{ ltrim($case->phone, '0') }}</span> @endif
                            </div>
                        </div>

                        <div class="case-meta-tags" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                            <span class="badge bg-selesai" style="font-size:10px; opacity:0.8;">{{ $case->id_klien ?? 'Guest' }}</span>
                            <span class="badge bg-{{ $case->type }}">{{ $case->type }}</span>
                        </div>

                        <div class="case-status-wrap" style="min-width:110px;">
                            <select class="form-select status-select" style="width:100%; font-size:11.5px; border-radius:20px; cursor:pointer;"
                                data-case="{{ $case->id_kasus }}"
                                data-prev="{{ $case->status }}"
                                data-popup-title="Status Kasus"
                                onchange="confirmStatusChange('{{ $case->id_kasus }}', this)">
                                @foreach(['proses'=>'Proses','tertunda'=>'Tertunda','selesai'=>'Selesai'] as $val=>$lbl)
                                    @php
                                        $role = auth()->user()->role;
                                        $allowed = ['freelancer'=>['proses'],'staff'=>['proses','tertunda'],'admin'=>['proses','tertunda','selesai'],'notaris'=>['proses','tertunda','selesai']];
                                        $canSet = in_array($val, $allowed[$role] ?? []);
                                    @endphp
                                    <option value="{{ $val }}" {{ $case->status===$val?'selected':'' }} {{ !$canSet?'disabled':'' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="case-deadline-wrap" style="white-space:nowrap; font-size:12px; color:var(--text-muted); display:inline-flex; align-items:center; gap:4px;">
                            <svg viewBox="0 0 24 24" style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $case->deadline?->format('d/m/Y') }}
                        </div>

                        <div class="case-actions" style="display:flex; gap:6px; margin-left:8px;">
                            @if(in_array(auth()->user()->role, ['admin','notaris','staff']))
                            <a href="{{ route('cases.edit', $case->id_kasus) }}" class="btn-edit" title="Edit & Dokumen">
                                <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            </a>
                            @endif
                            <form method="POST" action="{{ route('cases.destroy', $case->id_kasus) }}"
                                onsubmit="event.preventDefault(); showConfirm('Hapus Kasus','Yakin hapus kasus <strong>{{ addslashes($case->client_name) }}</strong>?',()=>this.submit(),'!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-del" title="Hapus">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- ── BOTTOM ZONE: Progress bar + Note ── --}}
                    <div class="case-progress-zone">

                        {{-- Progress track --}}
                        <div class="progress-track">
                            <div class="progress-bar-fill"
                                id="pb-{{ $case->id_kasus }}"
                                style="width:{{ $sc['pct'] }}%; background:{{ $sc['bar'] }};">
                            </div>
                        </div>

                        {{-- Status label + pct --}}
                        <div class="progress-meta">
                            <span class="progress-status-dot" style="background:{{ $sc['bar'] }};"></span>
                            <span class="progress-status-lbl" id="psl-{{ $case->id_kasus }}"
                                style="color:{{ $sc['text'] }};">
                                {{ ucfirst($case->status) }}
                                — {{ $sc['pct'] }}%
                            </span>

                            {{-- Inline note input --}}
                            <input type="text"
                                class="case-note-input"
                                id="note-{{ $case->id_kasus }}"
                                data-case="{{ $case->id_kasus }}"
                                value="{{ $case->progress_note ?? '' }}"
                                maxlength="300"
                                placeholder="Tambah catatan... (e.g. dokumen masih kurang, menunggu verifikasi)"
                                onblur="saveNote('{{ $case->id_kasus }}', this)"
                                onkeydown="if(event.key==='Enter'){event.preventDefault();this.blur();}">
                            <span class="note-save-indicator" id="nsi-{{ $case->id_kasus }}"></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div
            style="text-align:center;padding:48px;color:#6b7280;background:#fff;border-radius:14px;border:1px solid #e5e7eb;">
            <div style="display:flex;justify-content:center;margin-bottom:12px;">
                <svg viewBox="0 0 24 24" style="width:48px;height:48px;stroke:#9ca3af;fill:none;stroke-width:1.5;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div style="font-size:15px;font-weight:600;color:#374151;margin-bottom:6px;">Tidak ada kasus ditemukan</div>
            <div style="font-size:13.5px;">Coba ubah filter atau tambahkan kasus baru.</div>
        </div>
    @endforelse

    @push('modals')
        <div id="add-case-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
            <div class="modal-box" style="max-width:560px;">
                <div class="modal-header">
                    <span class="modal-title">Tambah Kasus Baru</span>
                    <button class="modal-close"
                        onclick="document.getElementById('add-case-modal').classList.remove('open')">×</button>
                </div>
                <form method="POST" action="{{ route('cases.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid" style="padding:24px;">
                        {{-- Client selector --}}
                        <div class="form-row client-select-shell" style="grid-column:span 2;">
                            <label for="add-case-client-select">Pilih Klien Terdaftar</label>
                            <select name="id_klien" id="add-case-client-select" class="form-select client-select-field" data-popup-title="Pilih Klien" onchange="autoFillClient(this)">
                                <option value="">-- Klien Baru --</option>
                                @foreach ($clients as $cl)
                                    <option value="{{ $cl->id_klien }}"
                                        data-name="{{ $cl->name }}"
                                        data-phone="{{ $cl->phone }}"
                                        data-address="{{ $cl->address }}"
                                        data-birth="{{ $cl->birth_date?->format('Y-m-d') }}">
                                        {{ $cl->name }} ({{ $cl->id_klien }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-row">
                            <label>Nama Klien *</label>
                            <input type="text" name="client_name" id="inc-client-name" required placeholder="Nama lengkap klien">
                        </div>
                        <div class="form-row">
                            <label>Telepon (WhatsApp)</label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:#6b7280; font-weight:600;">+62</span>
                                <input type="text" name="phone" id="inc-phone" style="padding-left:45px;" placeholder="812xxxx">
                            </div>
                        </div>
                        <div class="form-row" style="grid-column:span 2;">
                            <label>Alamat</label>
                            <textarea name="address" id="inc-address" placeholder="Alamat klien..." rows="2"></textarea>
                        </div>
                        <div class="form-row">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="birth_date" id="inc-birth">
                        </div>
                        <div class="form-row">
                            <label>Nama Kasus *</label>
                            <input type="text" name="case_name" required placeholder="Contoh: Pendirian PT ABC">
                        </div>
                        <div class="form-row">
                            <label>Tipe Kasus *</label>
                            <select name="type" data-popup-title="Tipe Kasus" required>
                                <option value="PT">PT</option>
                                <option value="CV">CV</option>
                                <option value="Pribadi">Pribadi</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Deadline *</label>
                            <input type="date" name="deadline" required>
                        </div>
                        <div class="form-row" style="grid-column:span 2;">
                            <label>Nominal Pembayaran (Rp)</label>
                            <input type="text" name="nominal_bayar" id="inc-nominal" placeholder="Contoh: 15.000.000" oninput="formatRupiah(this); liveTerbilang(this)">
                            <div id="terbilang-preview" style="font-size:11px; color:var(--accent); margin-top:4px; font-weight:600; min-height:1em;"></div>
                        </div>
                    </div>

                    <div style="padding:0 24px 24px;">
                        <div class="form-row">
                            <label>Berkas Dokumen</label>
                            <div class="file-grid">
                                @foreach ([['file_ktp', 'KTP'], ['file_npwp', 'NPWP'], ['file_kk', 'KK'], ['file_surat_tanah', 'Surat Tanah'], ['file_buku_nikah', 'Buku Nikah'], ['file_surat_perintah', 'Surat Perintah']] as [$fname, $flabel])
                                    <label class="file-chip" id="chip-{{ $fname }}" for="{{ $fname }}"
                                        @if ($fname === 'file_surat_perintah') style="grid-column:span 2;" @endif>
                                        <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;flex-shrink:0;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                        {{ $flabel }}
                                    </label>
                                    <input type="file" id="{{ $fname }}" name="{{ $fname }}"
                                        accept=".pdf,.jpg,.jpeg,.png" style="display:none"
                                        onchange="markChip('chip-{{ $fname }}','{{ $flabel }}',this)">
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="document.getElementById('add-case-modal').classList.remove('open')">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Kasus</button>
                    </div>
                </form>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            function markChip(chipId, label, input) {
                const chip = document.getElementById(chipId);
                if (input.files[0]) {
                    chip.classList.add('ok');
                    chip.innerHTML = `<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2.5;flex-shrink:0;"><polyline points="20 6 9 17 4 12"/></svg>${label} ✓`;
                }
            }

            function autoFillClient(select) {
                const opt = select.options[select.selectedIndex];
                if (!opt || !opt.value) {
                    select.classList.remove('has-client');
                    return;
                }
                select.classList.add('has-client');
                document.getElementById('inc-client-name').value = opt.getAttribute('data-name') || '';
                document.getElementById('inc-phone').value = opt.getAttribute('data-phone') || '';
                document.getElementById('inc-address').value = opt.getAttribute('data-address') || '';
                document.getElementById('inc-birth').value = opt.getAttribute('data-birth') || '';
            }

            function formatRupiah(el) {
                let val = el.value.replace(/\D/g, "");
                if (val === "") {
                    el.value = "";
                    return;
                }
                el.value = "Rp. " + parseInt(val).toLocaleString("id-ID");
            }

            function liveTerbilang(input) {
                // Strip Rp and dots to get raw number
                const val = parseInt(input.value.replace(/\D/g, ""));
                const preview = document.getElementById('terbilang-preview');
                if (!val || isNaN(val)) {
                    preview.innerText = '';
                    return;
                }

                const words = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                function convert(n) {
                    if (n < 12) return words[n];
                    if (n < 20) return convert(n - 10) + " Belas";
                    if (n < 100) return convert(Math.floor(n / 10)) + " Puluh " + convert(n % 10);
                    if (n < 200) return "Seratus " + convert(n - 100);
                    if (n < 1000) return convert(Math.floor(n / 100)) + " Ratus " + convert(n % 100);
                    if (n < 2000) return "Seribu " + convert(n - 1000);
                    if (n < 1000000) return convert(Math.floor(n / 1000)) + " Ribu " + convert(n % 1000);
                    if (n < 1000000000) return convert(Math.floor(n / 1000000)) + " Juta " + convert(n % 1000000);
                    if (n < 1000000000000) return convert(Math.floor(n / 1000000000)) + " Miliar " + convert(n % 1000000000);
                    return "";
                }

                preview.innerText = (convert(val).trim() + " Rupiah").replace(/\s+/g, ' ');
            }

            var STATUS_META = {
                proses:   { bar:'#f59e0b', text:'#92400e', pct: 45,  label:'Proses — 45%' },
                selesai:  { bar:'#22c55e', text:'#166534', pct: 100, label:'Selesai — 100%' },
                tertunda: { bar:'#ef4444', text:'#991b1b', pct: 20,  label:'Tertunda — 20%' }
            };

            // Animate all progress bars on load
            window.addEventListener('load', function() {
                document.querySelectorAll('.progress-bar-fill').forEach(function(el, i) {
                    var target = el.style.width; // e.g. "45%"
                    el.style.width = '0%';
                    setTimeout(function() { el.style.width = target; }, 150 + i * 30);
                });
            });

            function openAddCaseModal() {
                var modal = document.getElementById('add-case-modal');
                if (!modal) return;
                modal.classList.add('open');
                if (window.HugoPopupSelect) HugoPopupSelect.refresh(modal);
            }

            function confirmStatusChange(caseId, selectEl) {
                var newStatus = selectEl.value;
                var prev = selectEl.dataset.prev || newStatus;
                var labelMap = { proses: 'Proses', tertunda: 'Tertunda', selesai: 'Selesai' };
                showConfirm(
                    'Ubah Status Kasus',
                    'Ubah status menjadi "<strong>' + labelMap[newStatus] + '</strong>"?',
                    function() {
                        fetch('/cases/' + caseId + '/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf,
                                'ngrok-skip-browser-warning': 'true'
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            if (data.success) {
                                selectEl.dataset.prev = newStatus;
                                // ── Live update progress bar ──
                                var m = STATUS_META[newStatus] || STATUS_META.proses;
                                var pb  = document.getElementById('pb-'  + caseId);
                                var psl = document.getElementById('psl-' + caseId);
                                var dot = psl ? psl.previousElementSibling : null;
                                if (pb)  { pb.style.width = '0%'; setTimeout(function(){ pb.style.width = m.pct+'%'; pb.style.background = m.bar; }, 50); }
                                if (psl) { psl.textContent = m.label; psl.style.color = m.text; }
                                if (dot) { dot.style.background = m.bar; }
                                showToast('Status diubah ke "' + labelMap[newStatus] + '"', 'success');
                            } else {
                                showToast(data.error || 'Gagal mengubah status', 'danger');
                                selectEl.value = prev;
                                if (window.HugoPopupSelect) HugoPopupSelect.refresh(selectEl.closest('.popup-select-wrap') || selectEl.parentNode);
                            }
                        })
                        .catch(function() {
                            selectEl.value = prev;
                            if (window.HugoPopupSelect) HugoPopupSelect.refresh(selectEl.closest('.popup-select-wrap') || selectEl.parentNode);
                            showToast('Gagal mengubah status', 'danger');
                        });
                    },
                    '!',
                    function() {
                        selectEl.value = prev;
                        if (window.HugoPopupSelect) HugoPopupSelect.refresh(selectEl.closest('.popup-select-wrap') || selectEl.parentNode);
                    }
                );
                selectEl.dataset.prev = prev;
            }

            // ── Inline Note Save ────────────────────────────────────────────
            var _noteTimers = {};
            function saveNote(caseId, input) {
                clearTimeout(_noteTimers[caseId]);
                _noteTimers[caseId] = setTimeout(function() {
                    var note = input.value;
                    var ind = document.getElementById('nsi-' + caseId);
                    fetch('/cases/' + caseId + '/note', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf,
                            'ngrok-skip-browser-warning': 'true'
                        },
                        body: JSON.stringify({ note: note })
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success && ind) {
                            ind.textContent = '✓ Tersimpan';
                            ind.classList.add('show');
                            setTimeout(function() { ind.classList.remove('show'); }, 2000);
                        }
                    })
                    .catch(function() {});
                }, 600); // debounce 600ms
            }

            function setFilter(name, value, btn) {
                document.getElementById('filter-' + name).value = value;
                document.getElementById('filter-form').submit();
            }

            @if (session('success'))
                showToast('{{ session('success') }}', 'success');
            @endif
        </script>
    @endpush
@endsection
