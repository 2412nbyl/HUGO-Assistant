@extends('layout')
@section('page-title', 'Case Calendar')
@section('content')
    <style>
        .calendar-wrap {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            margin-bottom: 20px;
        }

        .cal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .cal-header h3 {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
        }

        .cal-nav {
            display: flex;
            gap: 8px;
        }

        .cal-btn {
            width: 34px;
            height: 34px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            font-size: 16px;
            transition: border-color .15s, background .15s;
        }

        .cal-btn:hover {
            border-color: var(--accent);

            background: #fff5f0;
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }

        .cal-day-label {
            text-align: center;
            font-size: 11.5px;
            font-weight: 600;
            color: #9ca3af;
            padding: 6px 0;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .cal-cell {
            min-height: 80px;
            border: 1.5px solid #f3f4f6;
            border-radius: 10px;
            padding: 8px;
            cursor: pointer;
            transition: border-color .15s, background .15s, transform .1s;
        }

        .cal-cell:hover {
            border-color: var(--accent);

            background: #fff5f0;
            transform: scale(1.01);
        }

        .cal-cell.today {
            border-color: var(--accent);

            background: #fff0eb;
        }

        .cal-cell.other-month {
            opacity: .35;
            pointer-events: none;
        }

        .cal-date {
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .cal-cell.today .cal-date {
            color: var(--accent);

        }

        .case-pin {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 500;
            margin-bottom: 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .status-selesai {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-proses {
            background: #fef9c3;
            color: #ca8a04;
        }

        .status-tertunda {
            background: #fee2e2;
            color: #dc2626;
        }

        .detail-panel {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            min-height: 120px;
        }

        .detail-panel h3 {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }

        .case-detail-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 10px;
            border-left: 3px solid var(--accent);

            margin-bottom: 10px;
        }

        .empty-state {
            text-align: center;
            color: #9ca3af;
            padding: 30px;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .calendar-wrap { padding: 12px; border-radius: 10px; }
            .cal-header { margin-bottom: 12px; }
            .cal-header h3 { font-size: 14px; }
            .cal-grid { gap: 2px; }
            .cal-cell { min-height: 55px; padding: 4px; border-radius: 6px; }
            .cal-date { font-size: 10px; margin-bottom: 2px; }
            .case-pin { font-size: 8px; padding: 1px 3px; border-radius: 3px; }
            .cal-day-label { font-size: 9px; padding: 4px 0; }
            .detail-panel { padding: 16px; border-radius: 10px; }
            .detail-panel h3 { font-size: 13px; }
            .case-detail-card { padding: 10px; gap: 10px; }
        }
    </style>

    <div class="calendar-wrap">
        <div class="cal-header">
            <h3 id="cal-month-label"></h3>
            <div class="cal-nav">
                <button class="cal-btn" onclick="shiftMonth(-1)">&#8592;</button>
                <button class="cal-btn" onclick="shiftMonth(1)">&#8594;</button>
            </div>
        </div>
        <div class="cal-grid" id="cal-grid"></div>
    </div>

    <div class="detail-panel">
        <h3 id="detail-heading">Klik tanggal untuk melihat detail kasus</h3>
        <div id="detail-body">
            <div class="empty-state">Pilih tanggal di kalender di atas</div>
        </div>
    </div>

    @push('scripts')
        @php
            // Pre-transform cases for JS: use deadline date as calendar key
            $calCases = $cases
                ->map(function ($c) {
                    return [
                        'id' => $c->id,
                        'date' => $c->deadline ? $c->deadline->format('Y-m-d') : null,
                        'created' => $c->created_at ? $c->created_at->format('Y-m-d') : null,
                        'client' => $c->client_name,
                        'kasus' => $c->case_name,
                        'type' => $c->type,
                        'status' => $c->status,
                        'deadline' => $c->deadline ? $c->deadline->format('d/m/Y') : '-',
                        'phone' => $c->phone,
                    ];
                })
                ->values();
        @endphp
        <script>
            const dbCases = @json($calCases);

            const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober',
                'November', 'Desember'
            ];
            const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

            // Start on current month
            const now = new Date();
            let curDate = new Date(now.getFullYear(), now.getMonth(), 1);

            function shiftMonth(delta) {
                curDate.setMonth(curDate.getMonth() + delta);
                renderCal();
            }

            function renderCal() {
                const y = curDate.getFullYear(),
                    m = curDate.getMonth();
                document.getElementById('cal-month-label').textContent = `${MONTHS[m]} ${y}`;
                const grid = document.getElementById('cal-grid');
                grid.innerHTML = DAYS.map(d => `<div class="cal-day-label">${d}</div>`).join('');

                const firstDay = new Date(y, m, 1).getDay();
                const daysInMonth = new Date(y, m + 1, 0).getDate();
                const prevDays = new Date(y, m, 0).getDate();
                const today = new Date();

                for (let i = firstDay - 1; i >= 0; i--) {
                    grid.innerHTML += `<div class="cal-cell other-month"><div class="cal-date">${prevDays-i}</div></div>`;
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    const dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    const isToday = today.getFullYear() === y && today.getMonth() === m && today.getDate() === d;
                    // Cases where deadline OR created_at is on this date
                    const dayCases = dbCases.filter(c => c.date === dateStr || c.created === dateStr);
                    const pins = dayCases.map(c =>
                        `<div class="case-pin status-${c.status}" onclick="event.stopPropagation();showDetail('${dateStr}')">${c.client}</div>`
                    ).join('');
                    grid.innerHTML += `<div class="cal-cell ${isToday?'today':''}" onclick="showDetail('${dateStr}')">
            <div class="cal-date">${d}</div>${pins}</div>`;
                }

                const total = firstDay + daysInMonth;
                const rem = 7 - (total % 7 || 7);
                for (let i = 1; i <= rem; i++) {
                    grid.innerHTML += `<div class="cal-cell other-month"><div class="cal-date">${i}</div></div>`;
                }
            }

            function showDetail(dateStr) {
                const dayCases = dbCases.filter(c => c.date === dateStr || c.created === dateStr);
                const [y, m, d] = dateStr.split('-');
                document.getElementById('detail-heading').textContent =
                    `Kasus pada ${parseInt(d)} ${MONTH_SHORT[parseInt(m)-1]} ${y}`;
                const body = document.getElementById('detail-body');
                if (!dayCases.length) {
                    body.innerHTML = '<div class="empty-state">Tidak ada kasus pada tanggal ini.</div>';
                    return;
                }
                const colors = {
                    selesai: '#22c55e',
                    proses: '#f59e0b',
                    tertunda: '#ef4444'
                };
                body.innerHTML = dayCases.map(c => {
                    const clr = colors[c.status] || '#9ca3af';
                    return `<div class="case-detail-card">
            <div style="flex:1;">
                <div style="font-size:14.5px;font-weight:600;color:#111827;margin-bottom:4px;">${c.client}</div>
                <div style="font-size:13px;color:#6b7280;margin-bottom:4px;">${c.kasus} <span style="margin-left:6px;background:#f3f4f6;padding:2px 8px;border-radius:12px;font-size:11px;color:#374151;font-weight:600;">${c.type}</span></div>
                ${c.phone ? `<div style="font-size:12px;color:#9ca3af;">${c.phone}</div>` : ''}
                <div style="font-size:12px;color:#9ca3af;margin-top:3px;">Deadline: ${c.deadline}</div>
            </div>
            <span style="padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;background:${clr}22;color:${clr};">${c.status.charAt(0).toUpperCase()+c.status.slice(1)}</span>
        </div>`;
                }).join('');
            }

            renderCal();
        </script>
    @endpush
@endsection
