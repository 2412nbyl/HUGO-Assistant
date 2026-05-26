@extends('layout')
@section('page-title', 'Payment Status')
@section('content')
    <style>
        :root {
            --text-dark: #1f2937;
            --text-mid: #374151;
            --text-muted: #6b7280;
        }

        /* ── Page header ── */
        .pay-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* ── Stats bar ── */
        .pay-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .pay-stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow .15s, transform .15s;
        }
        .pay-stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); transform: translateY(-1px); }
        .pay-stat-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .pay-stat-val { font-size: 24px; font-weight: 800; color: #111827; line-height: 1; }
        .pay-stat-label { font-size: 12px; color: #6b7280; margin-top: 3px; font-weight: 500; }

        /* ── Filter bar ── */
        .pay-filter-bar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px 18px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .pay-search-wrap {
            flex: 1; min-width: 200px; position: relative;
        }
        .pay-search-wrap input {
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
        .pay-search-wrap input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .08);
            background: #fff;
        }
        .pay-search-wrap svg {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            width: 15px; height: 15px;
            color: #9ca3af; stroke: currentColor; fill: none; stroke-width: 2;
            pointer-events: none;
        }
        .filter-divider { width: 1px; height: 30px; background: #e5e7eb; flex-shrink: 0; }
        .pill-group { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
        .pill-label {
            font-size: 11.5px; font-weight: 600; color: #9ca3af;
            text-transform: uppercase; letter-spacing: .5px; margin-right: 2px;
        }
        .pill {
            padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 600;
            border: 1.5px solid #e5e7eb; background: #fff; color: #374151; cursor: pointer;
            font-family: 'Inter', sans-serif; transition: all .15s; text-decoration: none;
            display: inline-block; white-space: nowrap;
        }
        .pill:hover, .pill.active {
            background: var(--accent); border-color: var(--accent); color: #fff;
        }

        /* ── Payment table ── */
        .pay-table-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
        }
        .pay-table-head {
            display: grid;
            grid-template-columns: 1fr auto auto;
            padding: 12px 20px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11.5px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ── Payment row ── */
        .pay-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            border-bottom: 1px solid #f3f4f6;
            transition: background .12s;
        }
        .pay-row:last-child { border-bottom: none; }
        .pay-row:hover { background: #fafafa; }

        .pay-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1f2937, #374151);
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; font-weight: 700; color: #f9fafb;
            flex-shrink: 0;
        }
        .pay-name { font-size: 14.5px; font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .pay-sub  { font-size: 12.5px; color: var(--text-muted); }

        .pay-amount {
            font-size: 14px; font-weight: 700; color: #111827;
            white-space: nowrap; flex-shrink: 0;
            display: flex; flex-direction: column; align-items: flex-end; gap: 3px;
        }
        .pay-ts { font-size: 11px; color: #9ca3af; font-weight: 400; }

        .edit-btn {
            border: 1.5px solid #e5e7eb; border-radius: 8px;
            padding: 7px 12px; background: #fff; color: #6b7280;
            cursor: pointer; font-size: 12px; font-family: 'Inter', sans-serif;
            display: flex; align-items: center; gap: 5px;
            transition: all .15s; flex-shrink: 0; white-space: nowrap;
        }
        .edit-btn:hover { border-color: var(--accent); color: var(--accent); background: #fff5f0; }
        .edit-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }

        .history-item {
            padding: 10px 14px; background: #f9fafb; border-radius: 8px;
            font-size: 12.5px; color: #374151; border-left: 2px solid #d1d5db; margin-bottom: 6px;
        }
        .history-meta { font-size: 11px; color: #9ca3af; margin-top: 3px; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .pay-stat-grid { grid-template-columns: 1fr 1fr; }
            .pay-filter-bar { flex-direction: column; align-items: stretch; padding: 12px; gap: 12px; }
            .filter-divider { display: none; }
            .pay-search-wrap { width: 100%; min-width: 0; }
            .pill-group { width: 100%; justify-content: flex-start; }
            .pay-row { flex-wrap: wrap; padding: 14px 16px; gap: 10px; }
            .pay-amount { align-items: flex-start; }
        }
        @media (max-width: 480px) {
            .pay-stat-grid { grid-template-columns: 1fr; }
        }
    </style>

    {{-- ── Page Header ── --}}
    <div class="pay-page-header">
        <div>
            <h2 style="font-size:20px; font-weight:800; color:#111827; margin:0;">Status Pembayaran</h2>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">Pantau dan kelola status pembayaran semua kasus</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            @if(in_array(auth()->user()->role, ['admin','notaris','staff']))
            <a href="{{ route('export.payments','csv') . '?' . http_build_query(request()->except('_token')) }}"
               class="btn-export btn-csv">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </a>
            <a href="{{ route('export.payments','pdf') . '?' . http_build_query(request()->except('_token')) }}"
               class="btn-export btn-pdf" target="_blank">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                PDF
            </a>
            @endif
        </div>
    </div>

    {{-- ── Stats ── --}}
    @php
        $totalLunas   = $payments->where('status','lunas')->count();
        $totalSebagian= $payments->where('status','sebagian')->count();
        $totalBelum   = $payments->where('status','belum')->count();
    @endphp
    <div class="pay-stat-grid">
        <div class="pay-stat-card">
            <div class="pay-stat-icon" style="background:#f0fdf4; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#16a34a;fill:none;stroke-width:2.5;display:block;"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="pay-stat-val" style="color:#16a34a;">{{ $totalLunas }}</div>
                <div class="pay-stat-label">Lunas</div>
            </div>
        </div>
        <div class="pay-stat-card">
            <div class="pay-stat-icon" style="background:#fefce8; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#b45309;fill:none;stroke-width:2.5;display:block;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <div class="pay-stat-val" style="color:#b45309;">{{ $totalSebagian }}</div>
                <div class="pay-stat-label">Sebagian</div>
            </div>
        </div>
        <div class="pay-stat-card">
            <div class="pay-stat-icon" style="background:#fef2f2; display:flex; align-items:center; justify-content:center;">
                <svg viewBox="0 0 24 24" style="width:20px;height:20px;stroke:#dc2626;fill:none;stroke-width:2.5;display:block;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </div>
            <div>
                <div class="pay-stat-val" style="color:#dc2626;">{{ $totalBelum }}</div>
                <div class="pay-stat-label">Belum Bayar</div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="pay-filter-bar">
        <form id="filter-form" method="GET" action="{{ route('payment.index') }}" style="display:contents;">
            <!-- Hidden input to submit Status correctly -->
            <input type="hidden" name="status" id="filter-status" value="{{ request('status') }}">

            <div class="pay-search-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama klien atau kasus..." onkeydown="if(event.key === 'Enter') { this.form.submit(); }">
            </div>
            <div class="filter-divider"></div>
            <div class="pill-group">
                <span class="pill-label">Status</span>
                <button type="button" onclick="setFilter('status', '')"
                    class="pill {{ !request('status') ? 'active' : '' }}">Semua</button>
                @foreach (['lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'belum' => 'Belum'] as $val => $lbl)
                    <button type="button" onclick="setFilter('status', '{{ $val }}')"
                        class="pill {{ request('status') === $val ? 'active' : '' }}">{{ $lbl }}</button>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13px;flex-shrink:0;">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                    <circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Cari
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('payment.index') }}" class="btn btn-secondary" style="padding:8px 14px;font-size:13px;white-space:nowrap;margin-left:8px;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;" title="Hapus semua filter">
                    ✕ Reset
                </a>
            @endif
        </form>
    </div>

    {{-- ── Count ── --}}
    <div style="font-size:13px; color:#6b7280; margin-bottom:12px;">
        Menampilkan <strong style="color:#111827;">{{ $payments->count() }}</strong> kasus pembayaran
    </div>

    {{-- ── Payment List ── --}}
    <div class="pay-table-card">
        @forelse($payments as $pay)
            @php
                $labels = ['lunas'=>'Lunas','sebagian'=>'Sebagian','belum'=>'Belum Sama Sekali'];
                $lastHistory = $pay->histories->first();
                $updatedAt   = $lastHistory ? $lastHistory->created_at : $pay->updated_at;
                $rawAmount   = preg_replace('/\D/', '', $pay->amount);
            @endphp
            <div class="pay-row" id="payrow-{{ $pay->id_transaksi }}">
                <div class="pay-avatar">{{ mb_substr($pay->case?->client_name ?? '?', 0, 1) }}</div>

                <div style="flex:1; min-width:0;">
                    <div class="pay-name">{{ $pay->case?->client_name }}</div>
                    <div class="pay-sub">{{ $pay->case?->case_name }}</div>
                </div>

                @if($rawAmount)
                <div class="pay-amount" id="pay-amount-{{ $pay->id_transaksi }}">
                    <span id="amount-val-{{ $pay->id_transaksi }}" style="font-size:15px; font-weight:800; color:#111827;">
                        Rp {{ number_format($rawAmount, 0, ',', '.') }}
                    </span>
                    @if($updatedAt)
                    <span class="pay-ts">{{ $updatedAt->format('d/m/Y H:i') }}</span>
                    @endif
                </div>
                @endif

                <span class="badge-premium {{ $pay->status === 'lunas' ? 'badge-green' : ($pay->status === 'sebagian' ? 'badge-orange' : 'badge-red') }}"
                    id="status-badge-{{ $pay->id_transaksi }}" style="flex-shrink:0;">
                    {{ $labels[$pay->status] }}
                </span>

                <button class="edit-btn" onclick="openPayModal('{{ $pay->id_transaksi }}', '{{ $pay->status }}')">
                    <svg viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    Ubah
                </button>
            </div>
        @empty
            <div style="text-align:center; padding:60px 24px;">
                <div style="font-size:48px; margin-bottom:14px; color:#9ca3af; display:flex; justify-content:center;">
                    <svg viewBox="0 0 24 24" style="width:48px; height:48px; stroke:currentColor; fill:none; stroke-width:1.5;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div style="font-size:16px; font-weight:700; color:#374151; margin-bottom:6px;">Tidak ada data pembayaran</div>
                <div style="font-size:13.5px; color:#9ca3af;">Coba ubah filter pencarian di atas</div>
            </div>
        @endforelse
    </div>

    @push('modals')
    <!-- EDIT PAYMENT MODAL -->
    <div id="pay-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:460px; max-height:90dvh; display:flex; flex-direction:column;">
            <div class="modal-header" style="flex-shrink:0;">
                <span class="modal-title" id="pay-modal-title">Ubah Status Pembayaran</span>
                <button class="modal-close"
                    onclick="document.getElementById('pay-modal').classList.remove('open')">×</button>
            </div>
            <div id="pay-modal-content" style="flex:1; overflow-y:auto; min-height:0;"></div>
            <div class="modal-footer" style="flex-shrink:0;">
                <button class="btn btn-secondary" onclick="document.getElementById('pay-modal').classList.remove('open')">Batal</button>
                <button class="btn btn-primary" onclick="savePayStatus()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
        @php
            $payJson = $payments
                ->map(function ($p) {
                    return [
                        'id'      => $p->id_transaksi,
                        'status'  => $p->status,
                        'client'  => $p->case?->client_name,
                        'case'    => $p->case?->case_name,
                        'history' => $p->histories
                            ->map(function ($h) {
                                return [
                                    'from' => $h->from_status,
                                    'to'   => $h->to_status,
                                    'note' => $h->note,
                                    'by'   => optional($h->changer)->name ?? 'Sistem',
                                    'date' => $h->created_at->format('d/m/Y H:i'),
                                ];
                            })
                            ->values(),
                    ];
                })
                ->keyBy('id');
        @endphp
        <script>
            const paymentsData = @json($payJson);
            let editingPayId   = null;
            const csrfToken    = '{{ csrf_token() }}';
            const routeBase    = '{{ url("/payment") }}';

            function openPayModal(id, currentStatus) {
                editingPayId = id;
                const p    = paymentsData[id] || {};
                const hist = p.history || [];
                const labels = { lunas: 'Lunas', sebagian: 'Sebagian', belum: 'Belum Sama Sekali' };

                const histHtml = hist.length
                    ? hist.map(h => `<div class="history-item">
                            <strong>${labels[h.from] || h.from}</strong>
                            <span style="color:#9ca3af; margin:0 6px;">→</span>
                            <strong>${labels[h.to] || h.to}</strong>
                            ${h.note ? `: <em style="color:#6b7280;">${h.note}</em>` : ''}
                            <div class="history-meta">Oleh: ${h.by} • ${h.date}</div>
                          </div>`).join('')
                    : '<div style="font-size:12.5px;color:#9ca3af;padding:8px 0;">Belum ada riwayat perubahan.</div>';

                // Update modal title with client name
                const titleEl = document.getElementById('pay-modal-title');
                if (titleEl && p.client) titleEl.textContent = `Ubah — ${p.client}`;

                document.getElementById('pay-modal-content').innerHTML = `
                <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">

                    ${p.client ? `<div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
                        <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#1f2937,#374151); display:flex; align-items:center; justify-content:center; font-weight:700; color:#f9fafb; flex-shrink:0;">${p.client.charAt(0).toUpperCase()}</div>
                        <div>
                            <div style="font-weight:600; color:#111827; font-size:14px;">${p.client}</div>
                            <div style="font-size:12px; color:#6b7280;">${p.case || ''}</div>
                        </div>
                    </div>` : ''}

                    <div class="form-row">
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Status Pembayaran</label>
                        <select id="pay-new-status" data-native-select="true"
                            style="width:100%; padding:10px 14px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Inter',sans-serif; color:#111827; background:#fff; outline:none; cursor:pointer; appearance:auto;">
                            <option value="lunas"   ${currentStatus==='lunas'   ? 'selected' : ''}>Lunas</option>
                            <option value="sebagian"${currentStatus==='sebagian'? 'selected' : ''}>Sebagian</option>
                            <option value="belum"   ${currentStatus==='belum'   ? 'selected' : ''}>Belum Sama Sekali</option>
                        </select>
                    </div>

                    <div class="form-row" id="nominal-sebagian-container" style="display: ${currentStatus==='sebagian' ? 'block' : 'none'};">
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Nominal Pembayaran Sebagian (Rp)</label>
                        <input type="text" id="pay-nominal-sebagian" placeholder="Contoh: 5.000.000"
                            style="width:100%; padding:10px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:13.5px; font-family:'Inter',sans-serif; color:#111827; outline:none;"
                            oninput="formatCurrencyInput(this)">
                    </div>

                    <div class="form-row">
                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Catatan (opsional)</label>
                        <textarea id="pay-note" placeholder="Catatan tambahan..." rows="2"
                            style="width:100%; font-family:'Inter',sans-serif; font-size:13.5px;"></textarea>
                    </div>

                    <div>
                        <div style="font-size:11.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Riwayat Transaksi</div>
                        <div style="max-height:200px; overflow-y:auto; padding-right:4px;">
                            ${histHtml}
                        </div>
                    </div>
                </div>`;

                document.getElementById('pay-modal').classList.add('open');
            }

            function formatCurrencyInput(input) {
                let value = input.value.replace(/\D/g, '');
                if (value) {
                    input.value = Number(value).toLocaleString('id-ID');
                } else {
                    input.value = '';
                }
            }

            // Dynamically show/hide partial payment field
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'pay-new-status') {
                    const container = document.getElementById('nominal-sebagian-container');
                    if (container) {
                        container.style.display = e.target.value === 'sebagian' ? 'block' : 'none';
                    }
                }
            });

            function savePayStatus() {
                const status  = document.getElementById('pay-new-status').value;
                const note    = document.getElementById('pay-note').value;
                const nominal_sebagian = document.getElementById('pay-nominal-sebagian') ? document.getElementById('pay-nominal-sebagian').value : '';
                const saveBtn = document.querySelector('#pay-modal .btn-primary');
                const oldStatus = paymentsData[editingPayId] ? paymentsData[editingPayId].status : '';

                if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Menyimpan...'; }

                fetch(`${routeBase}/${editingPayId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status, note, nominal_sebagian })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('pay-modal').classList.remove('open');
                        showToast('Status pembayaran diperbarui ✓', 'success');

                        // Update badge in DOM
                        const badge  = document.getElementById(`status-badge-${editingPayId}`);
                        const clsMap = { lunas: 'badge-green', sebagian: 'badge-orange', belum: 'badge-red' };
                        const lblMap = { lunas: 'Lunas', sebagian: 'Sebagian', belum: 'Belum Sama Sekali' };
                        if (badge) {
                            badge.className = `badge-premium ${clsMap[status]}`;
                            badge.textContent = lblMap[status];
                        }

                        // Update payment amount text in DOM
                        if (data.payment && data.payment.amount) {
                            const amtSpan = document.getElementById(`amount-val-${editingPayId}`);
                            if (amtSpan) {
                                let cleanAmt = data.payment.amount.replace('Rp. ', 'Rp ');
                                amtSpan.textContent = cleanAmt;
                            }
                        }

                        // Update internal data
                        if (paymentsData[editingPayId]) {
                            const now = new Date();
                            const fmt = `${String(now.getDate()).padStart(2,'0')}/${String(now.getMonth()+1).padStart(2,'0')}/${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                            let noteLog = note;
                            if (status === 'sebagian' && nominal_sebagian) {
                                noteLog = `Bayar Sebagian: Rp. ${nominal_sebagian}` + (note ? ` — ${note}` : '');
                            }
                            paymentsData[editingPayId].history = [
                                { from: oldStatus, to: status, note: noteLog, by: 'Anda', date: fmt },
                                ...(paymentsData[editingPayId].history || [])
                            ];
                            paymentsData[editingPayId].status = status;
                            if (data.payment && data.payment.amount) {
                                paymentsData[editingPayId].amount = data.payment.amount;
                            }
                        }
                    } else {
                        showToast(data.message || 'Gagal menyimpan.', 'danger');
                        if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Simpan Perubahan'; }
                    }
                })
                .catch(() => {
                    showToast('Gagal menghubungi server', 'danger');
                    if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Simpan Perubahan'; }
                });
            }

            function setFilter(name, value) {
                document.getElementById('filter-' + name).value = value;
                document.getElementById('filter-form').submit();
            }

            @php $successMsg = session('success'); @endphp
            @if ($successMsg)
                window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes($successMsg) }}', 'success'));
            @endif
        </script>
    @endpush
@endsection
