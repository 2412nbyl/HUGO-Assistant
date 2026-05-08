@extends('layout')
@section('page-title', 'Payment Status')
@section('content')
    <style>
        :root {
            --text-dark: #1f2937;
            --text-mid: #374151;
            --text-muted: #6b7280;
        }

        .filter-bar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-wrap {
            flex: 1;
            min-width: 180px;
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

        .pill:hover,
        .pill.active {
            background: var(--accent);
            border-color: var(--accent);

            color: #fff;
        }

        .pay-row {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow .15s, border-color .15s;
        }

        .pay-row:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .07);
            border-color: #d1d5db;
        }

        .pay-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1f2937, #374151);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 700;
            color: #f9fafb;
            flex-shrink: 0;
        }

        .pay-name {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 2px;
        }

        .pay-sub {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .pay-status-btn {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: opacity .15s;
        }

        .pay-lunas { background: #dcfce7; color: #16a34a; }
        .pay-sebagian { background: #fef9c3; color: #b45309; }
        .pay-belum { background: #fee2e2; color: #dc2626; }

        .edit-btn {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 10px;
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all .15s;
        }

        .edit-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }


        .edit-btn svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .history-item {
            padding: 10px 14px;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 12.5px;
            color: #374151;
            border-left: 2px solid #d1d5db;
            margin-bottom: 6px;
        }

        .history-meta {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }
    </style>

    <!-- FILTER BAR -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('payment.index') }}" style="display:contents;">
            <div class="search-wrap">
                <svg viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama klien atau kasus...">
            </div>
            <div class="filter-divider"></div>
            <div class="pill-group">
                <span class="pill-label">Status</span>
                <a href="{{ route('payment.index', request()->except('status', 'page')) }}"
                    class="pill {{ !request('status') ? 'active' : '' }}">Semua</a>
                @foreach (['lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'belum' => 'Belum'] as $val => $lbl)
                    <a href="{{ route('payment.index', array_merge(request()->except('status', 'page'), ['status' => $val])) }}"
                        class="pill {{ request('status') === $val ? 'active' : '' }}">{{ $lbl }}</a>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13px;">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#fff;fill:none;stroke-width:2.5;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                Cari
            </button>
        </form>
    </div>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
        <div style="font-size:13.5px;color:var(--text-muted);">{{ $payments->count() }} kasus</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <!-- Export Buttons (admin/notaris/staff only) -->
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

    @forelse($payments as $pay)
        @php
            $cls = ['lunas' => 'pay-lunas', 'sebagian' => 'pay-sebagian', 'belum' => 'pay-belum'][$pay->status];
            $labels = ['lunas' => 'Lunas', 'sebagian' => 'Sebagian', 'belum' => 'Belum Sama Sekali'];
            $lastHistory = $pay->histories->first();
            // Use the latest history timestamp if available, otherwise updated_at
            $updatedAt = $lastHistory ? $lastHistory->created_at : $pay->updated_at;
        @endphp
        <div class="pay-row" id="payrow-{{ $pay->id }}">
            <div class="pay-avatar">{{ mb_substr($pay->case?->client_name ?? '?', 0, 1) }}</div>
            <div style="flex:1;min-width:0;">
                <div class="pay-name">{{ $pay->case?->client_name }}</div>
                <div class="pay-sub">
                    {{ $pay->case?->case_name }}
                    @if($pay->amount)
                        @php
                            $rawAmount = preg_replace('/\D/', '', $pay->amount);
                        @endphp
                        • <span style="font-weight:600; color:#111827;">Rp. {{ number_format($rawAmount, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <span class="badge-premium {{ $pay->status === 'lunas' ? 'badge-green' : ($pay->status === 'sebagian' ? 'badge-orange' : 'badge-red') }}" id="status-badge-{{ $pay->id }}">
                    {{ $labels[$pay->status] }}
                </span>

                @if ($updatedAt)
                    <span style="font-size:11px;color:#9ca3af;">{{ $updatedAt->format('d/m/Y H:i') }}</span>
                @endif
            </div>
            <button class="edit-btn" onclick="openPayModal('{{ $pay->id }}', '{{ $pay->status }}')">
                <svg viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Ubah
            </button>
        </div>
    @empty
        <div
            style="text-align:center;padding:48px;color:#6b7280;background:#fff;border-radius:14px;border:1px solid #e5e7eb;">
            <div style="font-size:36px;margin-bottom:12px;"></div>
            <div style="font-size:15px;font-weight:600;color:#374151;">Tidak ada data pembayaran</div>
        </div>
    @endforelse

    @push('modals')
    <!-- EDIT PAYMENT MODAL -->
    <div id="pay-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title" id="pay-modal-title">Ubah Status Pembayaran</span>
                <button class="modal-close"
                    onclick="document.getElementById('pay-modal').classList.remove('open')">×</button>
            </div>
            <div id="pay-modal-content"></div>
        </div>
    </div>
    @endpush

    @push('scripts')
        @php
            $payJson = $payments
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'status' => $p->status,
                        'history' => $p->histories
                            ->map(function ($h) {
                                return [
                                    'from' => $h->from_status,
                                    'to' => $h->to_status,
                                    'note' => $h->note,
                                    'by' => optional($h->changer)->name ?? 'Sistem',
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
            let editingPayId = null;
            const csrfToken = '{{ csrf_token() }}';
            const routeBase = '{{ url('/payment') }}';

            function openPayModal(id, currentStatus) {
                editingPayId = id;
                const p = paymentsData[id] || {};
                const hist = p.history || [];
                const labels = {
                    lunas: 'Lunas',
                    sebagian: 'Sebagian',
                    belum: 'Belum Sama Sekali'
                };

                const histHtml = hist.length ?
                    hist.map(h => `<div class="history-item">${labels[h.from]||h.from} → ${labels[h.to]||h.to}${h.note ? ': <em>'+h.note+'</em>' : ''}
            <div class="history-meta">Terakhir Diubah Oleh: ${h.by} • ${h.date}</div></div>`).join('') :
                    '<div style="font-size:12.5px;color:#9ca3af;">Belum ada riwayat.</div>';

                document.getElementById('pay-modal-content').innerHTML = `
        <div style="padding: 24px;">
            <div class="form-row">
                <label>Status Pembayaran</label>
                <select id="pay-new-status" class="pill" style="width:100%; height:42px; border-radius:10px; appearance:auto; padding:0 12px;">
                    <option value="lunas" ${currentStatus==='lunas'?'selected':''}>Lunas</option>
                    <option value="sebagian" ${currentStatus==='sebagian'?'selected':''}>Sebagian</option>
                    <option value="belum" ${currentStatus==='belum'?'selected':''}>Belum Sama Sekali</option>
                </select>
            </div>
            <div class="form-row">
                <label>Catatan (opsional)</label>
                <textarea id="pay-note" placeholder="Tuliskan catatan tambahan jika perlu..."></textarea>
            </div>
            <div style="margin-top: 24px;">
                <label style="font-size:12px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:12px;">Riwayat Transaksi</label>
                <div style="max-height: 220px; overflow-y: auto; padding-right: 8px;">
                    ${histHtml}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="document.getElementById('pay-modal').classList.remove('open')">Batal</button>
            <button class="btn btn-primary" onclick="savePayStatus()">Simpan Perubahan</button>
        </div>`;

                document.getElementById('pay-modal').classList.add('open');
            }

            function savePayStatus() {
                const status = document.getElementById('pay-new-status').value;
                const note = document.getElementById('pay-note').value;
                const saveBtn = document.querySelector('#pay-modal-content .btn-primary');
                
                // Get the old status before updating
                const oldStatus = paymentsData[editingPayId] ? paymentsData[editingPayId].status : '';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Menyimpan...';
                }

                fetch(`${routeBase}/${editingPayId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status,
                            note
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('pay-modal').classList.remove('open');
                            showToast('Status pembayaran diperbarui ✓', 'success');

                            // Update badge directly in the DOM
                            const badge = document.getElementById(`status-badge-${editingPayId}`);
                            const clsMap = {
                                lunas: 'badge-green',
                                sebagian: 'badge-orange',
                                belum: 'badge-red'
                            };
                            const lblMap = {
                                lunas: 'Lunas',
                                sebagian: 'Sebagian',
                                belum: 'Belum Sama Sekali'
                            };
                            if (badge) {
                                // Keep badge-premium, just change the color class
                                badge.className = `badge-premium ${clsMap[status]}`;
                                badge.textContent = lblMap[status];

                                // Update timestamp below badge
                                const tsEl = badge.nextElementSibling;
                                const now = new Date();
                                const fmt =
                                    `${String(now.getDate()).padStart(2,'0')}/${String(now.getMonth()+1).padStart(2,'0')}/${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                                if (tsEl) {
                                    tsEl.textContent = fmt;
                                }
                            }

                            // Update internal data so history modal reflects new status
                            if (paymentsData[editingPayId]) {
                                if (status !== oldStatus) {
                                    const now2 = new Date();
                                    const fmt2 =
                                        `${String(now2.getDate()).padStart(2,'0')}/${String(now2.getMonth()+1).padStart(2,'0')}/${now2.getFullYear()} ${String(now2.getHours()).padStart(2,'0')}:${String(now2.getMinutes()).padStart(2,'0')}`;
                                    
                                    paymentsData[editingPayId].history = [{
                                            from: oldStatus,
                                            to: status,
                                            note: note,
                                            by: 'Anda',
                                            date: fmt2
                                        },
                                        ...(paymentsData[editingPayId].history || [])
                                    ];
                                    paymentsData[editingPayId].status = status;
                                }
                            }
                        } else {
                            showToast(data.message || 'Gagal menyimpan. Coba lagi.', 'danger');
                            if (saveBtn) {
                                saveBtn.disabled = false;
                                saveBtn.textContent = 'Simpan';
                            }
                        }
                    })
                    .catch(() => {
                        showToast('Gagal menghubungi server', 'danger');
                        if (saveBtn) {
                            saveBtn.disabled = false;
                            saveBtn.textContent = 'Simpan';
                        }
                    });
            }


            @php $successMsg = session('success'); @endphp
            @if ($successMsg)
                window.addEventListener('DOMContentLoaded', () => showToast('{{ addslashes($successMsg) }}', 'success'));
            @endif
        </script>
    @endpush
@endsection
