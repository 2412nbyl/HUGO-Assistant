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
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            padding: 2px 4px;
            border-radius: 4px;
            font-size: 10.5px;
            font-weight: 500;
            margin-bottom: 2px;
            line-height: 1.2;
            max-height: 2.6em;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
            white-space: normal;
            text-align: center;
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

        .status-birthday {
            background: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
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
            justify-content: space-between;
            gap: 12px;
            padding: 14px;
            background: #f9fafb;
            border-radius: 10px;
            border-left: 3px solid var(--accent);
            margin-bottom: 10px;
            overflow: visible;
        }

        .case-detail-card > div:first-child {
            flex: 1;
            min-width: 0;
        }

        .case-detail-card > span:last-child {
            flex-shrink: 0;
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
            .cal-header h3 { font-size: 13.5px; }
            .cal-grid { gap: 2px; }
            .cal-cell { 
                min-height: 56px;
                padding: 4px 2px; 
                border-radius: 6px; 
                display: flex;
                flex-direction: column;
                align-items: stretch;
                overflow: visible;
            }
            .cal-date { 
                font-size: 9.5px; 
                margin-bottom: 2px; 
                width: 100%; 
                text-align: center; 
            }
            .case-pin { 
                font-size: 7.5px;
                padding: 2px 3px; 
                border-radius: 3px; 
                margin-bottom: 2px;
                width: 100%;
            }
            .cal-day-label { font-size: 8.5px; padding: 4px 0; }
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

            $calClients = isset($clients) ? $clients->map(function ($cl) {
                return [
                    'id' => $cl->id_klien,
                    'name' => $cl->name,
                    'birth_month' => $cl->birth_date ? (int)$cl->birth_date->format('m') : null,
                    'birth_day' => $cl->birth_date ? (int)$cl->birth_date->format('d') : null,
                    'birth_date_formatted' => $cl->birth_date ? $cl->birth_date->format('d/m/Y') : null,
                    'phone' => $cl->phone,
                ];
            })->filter(fn($cl) => !is_null($cl['birth_month']))->values() : [];
        @endphp
        <script>
            const dbCases = @json($calCases);
            const dbClients = @json($calClients);

            const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober',
                'November', 'Desember'
            ];
            const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
            const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

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
                    
                    const dayCases = dbCases.filter(c => c.date === dateStr || c.created === dateStr);
                    const dayBirthdays = dbClients.filter(c => c.birth_month === (m + 1) && c.birth_day === d);

                    const casePins = dayCases.map(c =>
                        `<div class="case-pin status-${c.status}" onclick="event.stopPropagation();showDetail('${dateStr}')">${c.client}</div>`
                    ).join('');

                    const bdayPins = dayBirthdays.map(c =>
                        `<div class="case-pin status-birthday" onclick="event.stopPropagation();showDetail('${dateStr}')">🎂 ${c.name}</div>`
                    ).join('');

                    grid.innerHTML += `<div class="cal-cell ${isToday?'today':''}" onclick="showDetail('${dateStr}')">
            <div class="cal-date">${d}</div>${casePins}${bdayPins}</div>`;
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
                const monthNum = parseInt(m);
                const dayNum = parseInt(d);
                const dayBirthdays = dbClients.filter(c => c.birth_month === monthNum && c.birth_day === dayNum);

                document.getElementById('detail-heading').textContent =
                    `Kasus & Acara pada ${dayNum} ${MONTH_SHORT[monthNum-1]} ${y}`;
                const body = document.getElementById('detail-body');
                
                if (!dayCases.length && !dayBirthdays.length) {
                    body.innerHTML = '<div class="empty-state">Tidak ada kasus atau hari ulang tahun pada tanggal ini.</div>';
                    return;
                }

                const colors = {
                    selesai: '#22c55e',
                    proses: '#f59e0b',
                    tertunda: '#ef4444'
                };

                let html = '';

                if (dayBirthdays.length) {
                    html += dayBirthdays.map(c => {
                        return `<div class="case-detail-card" style="border-left-color: #db2777; background: #fff5f7;">
                            <div style="flex:1;">
                                <div style="font-size:14.5px;font-weight:700;color:#9d174d;margin-bottom:4px;">🎂 Hari Ulang Tahun: ${c.name}</div>
                                <div style="font-size:13px;color:#db2777;margin-bottom:4px;">Klien Terdaftar (${c.id})</div>
                                ${c.phone ? `<div style="font-size:12px;color:#db2777;">Telp: ${c.phone}</div>` : ''}
                                <div style="font-size:12px;color:#9ca3af;margin-top:3px;">Lahir: ${c.birth_date_formatted}</div>
                            </div>
                            <span style="padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;background:#fbcfe8;color:#9d174d;">HUT</span>
                        </div>`;
                    }).join('');
                }

                if (dayCases.length) {
                    html += dayCases.map(c => {
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

                body.innerHTML = html;
            }

            renderCal();
        </script>
    @endpush
@endsection
