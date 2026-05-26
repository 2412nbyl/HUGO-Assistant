@extends('layout')
@section('page-title', 'Report Archive')
@section('content')
    <style>
        :root {
            --text-dark: #1f2937;
            --text-mid: #374151;
            --text-muted: #6b7280;
        }

        .filter-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .filter-card-title {
            font-size: 12px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 12px;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .select-styled {
            padding: 8px 13px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: #374151;
            background: #f9fafb;
            cursor: pointer;
            outline: none;
            transition: border-color .15s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 32px;
        }

        .select-styled:focus {
            border-color: var(--accent);

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

        .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        .btn-word {
            border-color: #3b82f6;
            color: #1d4ed8;
            background: #eff6ff;
        }

        .btn-word:hover {
            background: #3b82f6;
            color: #fff;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .report-table th {
            background: #f9fafb;
            padding: 12px 16px;
            text-align: left;
            font-size: 12.5px;
            font-weight: 700;
            color: #374151;
            border-bottom: 1.5px solid #e5e7eb;
        }

        .report-table td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: #1f2937;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .report-table tr:last-child td {
            border-bottom: none;
        }

        .report-table tr:hover td {
            background: #fafafa;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 14px;
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

        .bg-lunas {
            background: #dcfce7;
            color: #16a34a;
        }

        .bg-sebagian {
            background: #fef9c3;
            color: #b45309;
        }

        .bg-belum {
            background: #fee2e2;
            color: #dc2626;
        }

        .row-exports {
            display: flex;
            gap: 5px;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-actions {
                margin-left: 0;
                flex-wrap: wrap;
            }

            .filter-row .btn-primary {
                margin-left: 0 !important;
                width: 100%;
            }

            .report-table-scroll {
                width: 100%;
                max-width: 100%;
                -webkit-overflow-scrolling: touch;
            }

            .report-table-scroll .report-table {
                min-width: 680px;
            }

            .report-table th,
            .report-table td {
                white-space: normal;
                word-break: break-word;
                vertical-align: top;
            }

            .report-table .row-exports {
                flex-wrap: nowrap;
            }
        }
    </style>

    <!-- FILTER CARD -->
    <div class="filter-card">
        <div class="filter-card-title">Filter Laporan</div>
        <form method="GET" action="{{ route('reports.index') }}">
            <div class="filter-row">
                <select name="month" class="select-styled" data-popup-title="Bulan">
                    <option value="">Semua Bulan</option>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="select-styled" data-popup-title="Tahun">
                    @foreach ([2026, 2025, 2024, 2023] as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}</option>
                    @endforeach
                </select>

                <div class="pill-group">
                    <span class="pill-label">Tipe</span>
                    <a href="{{ route('reports.index', request()->except('type')) }}"
                        class="pill {{ !request('type') ? 'active' : '' }}">Semua</a>
                    @foreach (['PT', 'CV', 'Pribadi'] as $t)
                        <a href="{{ route('reports.index', array_merge(request()->all(), ['type' => $t])) }}"
                            class="pill {{ request('type') === $t ? 'active' : '' }}">{{ $t }}</a>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary" style="padding:8px 18px;font-size:13px;margin-left:auto;">
                    Tampilkan
                </button>

                <div class="filter-actions">
                    <button type="button" class="btn-export btn-excel" onclick="exportReport('excel')">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        </svg> Excel
                    </button>
                    <button type="button" class="btn-export btn-pdf" onclick="exportReport('pdf')">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        </svg> PDF
                    </button>
                    <button type="button" class="btn-export btn-word" onclick="exportReport('word')">
                        <svg viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        </svg> Word
                    </button>
                    <button type="button" class="btn btn-secondary" style="padding:7px 14px;font-size:12.5px;"
                        onclick="document.getElementById('print-modal').classList.add('open')">
                        <svg viewBox="0 0 24 24"
                            style="width:13px;height:13px;stroke:currentColor;fill:none;stroke-width:2;">
                            <polyline points="6 9 6 2 18 2 18 9" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <rect x="6" y="14" width="12" height="8" />
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div style="font-size:13.5px;color:#6b7280;margin-bottom:14px;">{{ $cases->count() }} laporan ditemukan</div>

    <!-- TABLE -->
    @if ($cases->isEmpty())
        <div
            style="text-align:center;padding:48px;background:#fff;border-radius:14px;border:1px solid #e5e7eb;color:#6b7280;">
            <div style="font-size:36px;margin-bottom:12px;"></div>
            <div style="font-size:15px;font-weight:600;color:#374151;">Tidak ada laporan untuk filter ini</div>
        </div>
    @else
        <div class="table-responsive report-table-scroll">
        <table class="report-table" id="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Klien</th>
                    <th>Kasus</th>
                    <th>Tipe</th>
                    <th>Status Kasus</th>
                    <th>Status Bayar</th>
                    <th>Deadline</th>
                    <th>Ekspor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cases as $i => $case)
                    <tr>
                        <td style="color:#9ca3af;font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $case->client_name }}</td>
                        <td style="color:#374151;">{{ $case->case_name }}</td>
                        <td><span class="badge bg-{{ $case->type }}">{{ $case->type }}</span></td>
                        <td><span class="badge bg-{{ $case->status }}">{{ ucfirst($case->status) }}</span></td>
                        <td>
                            @if ($case->payment)
                                <span
                                    class="badge bg-{{ $case->payment->status }}">{{ ucfirst($case->payment->status) }}</span>
                            @else
                                <span style="color:#9ca3af;font-size:12px;">—</span>
                            @endif
                        </td>
                        <td style="color:#374151;">{{ $case->deadline?->format('d/m/Y') }}</td>
                        <td>
                            <div class="row-exports">
                                <button type="button" class="btn-export btn-excel" onclick="exportRowExcel({{ $i }})"
                                    title="Excel" style="padding:4px 10px;">XLS</button>
                                <button type="button" class="btn-export btn-pdf" onclick="exportRowPdf({{ $i }})"
                                    title="PDF" style="padding:4px 10px;">PDF</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif

    @push('modals')
    <div id="print-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:720px;">
            <div class="modal-header">
                <span class="modal-title">Preview Cetak</span>
                <button class="modal-close"
                    onclick="document.getElementById('print-modal').classList.remove('open')">×</button>
            </div>
            <div
                style="background:#f9fafb;border-radius:8px;padding:24px;border:1px solid #e5e7eb;max-height:55vh;overflow-y:auto;font-size:13px;color:#1f2937;">
                <h2 style="font-size:17px;font-weight:700;text-align:center;margin-bottom:4px;">HUGO Assistant — Arsip
                    Laporan</h2>
                <p style="text-align:center;font-size:12px;color:#6b7280;margin-bottom:16px;">Dicetak pada:
                    {{ now()->format('d/m/Y H:i') }}</p>
                @if (!$cases->isEmpty())
                    <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
                        <thead>
                            <tr>
                                @foreach (['#', 'Klien', 'Kasus', 'Tipe', 'Status', 'Deadline'] as $h)
                                    <th
                                        style="background:#f3f4f6;padding:7px 10px;text-align:left;font-weight:600;border:1px solid #e5e7eb;">
                                        {{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cases as $i => $case)
                                <tr>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">{{ $i + 1 }}</td>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">{{ $case->client_name }}</td>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">{{ $case->case_name }}</td>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">{{ $case->type }}</td>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">{{ ucfirst($case->status) }}
                                    </td>
                                    <td style="padding:7px 10px;border:1px solid #e5e7eb;">
                                        {{ $case->deadline?->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary"
                    onclick="document.getElementById('print-modal').classList.remove('open')">Tutup</button>
                <button class="btn btn-primary" onclick="window.print()">Cetak</button>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
        @php
            $exportData = $cases
                ->map(
                    fn($c) => [
                        'no' => '',
                        'client' => $c->client_name,
                        'case' => $c->case_name,
                        'type' => $c->type,
                        'status' => ucfirst($c->status),
                        'pay_status' => $c->payment ? ucfirst($c->payment->status) : '-',
                        'deadline' => $c->deadline ? $c->deadline->format('d/m/Y') : '-',
                    ],
                )
                ->values();
        @endphp
        <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
        <script>
            const exportRows = @json($exportData);
            const exportFilename = 'Laporan_HUGO_{{ now()->format('Y-m-d') }}';

            function printedAtLabel() {
                return new Date().toLocaleString('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }) + ' WIB';
            }

            function safeFileName(name) {
                return String(name || 'kasus').replace(/[^\w\s-]/g, '').trim().replace(/\s+/g, '_') || 'kasus';
            }

            function exportReport(fmt) {
                if (!exportRows.length) {
                    showToast('Tidak ada data untuk diekspor', 'danger');
                    return;
                }

                if (fmt === 'excel') {
                    // Real Excel using SheetJS
                    const headers = ['No', 'Klien', 'Kasus', 'Tipe', 'Status Kasus', 'Status Bayar', 'Deadline'];
                    const data = exportRows.map((r, i) => [i + 1, r.client, r.case, r.type, r.status, r.pay_status, r
                        .deadline
                    ]);
                    const ws = XLSX.utils.aoa_to_sheet([headers, ...data]);
                    ws['!cols'] = headers.map(() => ({
                        wch: 20
                    }));
                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, 'Laporan');
                    XLSX.writeFile(wb, exportFilename + '.xlsx');
                    showToast('File Excel berhasil diunduh ✓', 'success');

                } else if (fmt === 'word') {
                    const rows = exportRows.map((r, i) =>
                        `<tr><td>${i+1}</td><td>${r.client}</td><td>${r.case}</td><td>${r.type}</td><td>${r.status}</td><td>${r.pay_status}</td><td>${r.deadline}</td></tr>`
                    ).join('');
                    const html = `<html xmlns:o='urn:schemas-microsoft-com:office:office'><head><meta charset='UTF-8'>
            <style>table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:6px 10px;font-size:12pt}</style></head>
            <body><h2>HUGO Assistant - Arsip Laporan</h2>
            <p>Dicetak: ${printedAtLabel()}</p>
            <table><thead><tr><th>No</th><th>Klien</th><th>Kasus</th><th>Tipe</th><th>Status Kasus</th><th>Status Bayar</th><th>Deadline</th></tr></thead>
            <tbody>${rows}</tbody></table></body></html>`;
                    const blob = new Blob(['\ufeff', html], {
                        type: 'application/msword'
                    });
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = exportFilename + '.doc';
                    a.click();
                    showToast('File Word berhasil diunduh ✓', 'success');

                } else if (fmt === 'pdf') {
                    const win = window.open('', '_blank', 'width=900,height=700');
                    if (!win) {
                        showToast('Izinkan pop-up untuk export PDF', 'warning');
                        return;
                    }
                    const rows = exportRows.map((r, i) =>
                        `<tr><td>${i+1}</td><td>${r.client}</td><td>${r.case}</td><td>${r.type}</td><td>${r.status}</td><td>${r.pay_status}</td><td>${r.deadline}</td></tr>`
                    ).join('');
                    win.document.write(`<html><head><title>Laporan HUGO</title>
            <style>body{font-family:Arial,sans-serif;padding:20px}h2{text-align:center}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:7px 10px;font-size:11pt}</style></head>
            <body><h2>HUGO Assistant — Arsip Laporan</h2><p style='text-align:center;font-size:10pt;color:#666'>Dicetak: ${printedAtLabel()}</p>
            <table><thead><tr><th>No</th><th>Klien</th><th>Kasus</th><th>Tipe</th><th>Status</th><th>Pembayaran</th><th>Deadline</th></tr></thead>
            <tbody>${rows}</tbody></table></body></html>`);
                    win.document.close();
                    setTimeout(() => {
                        win.print();
                    }, 400);
                }
            }

            function exportRowExcel(i) {
                const r = exportRows[i];
                if (!r) {
                    showToast('Data baris tidak ditemukan', 'danger');
                    return;
                }
                const headers = ['No', 'Klien', 'Kasus', 'Tipe', 'Status Kasus', 'Status Bayar', 'Deadline'];
                const data = [[i + 1, r.client, r.case, r.type, r.status, r.pay_status, r.deadline]];
                const ws = XLSX.utils.aoa_to_sheet([headers, ...data]);
                ws['!cols'] = headers.map(function () { return { wch: 22 }; });
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Kasus');
                XLSX.writeFile(wb, 'Kasus_' + safeFileName(r.client) + '.xlsx');
                showToast('Excel baris diunduh ✓', 'success');
            }

            function exportRowPdf(i) {
                const r = exportRows[i];
                if (!r) {
                    showToast('Data baris tidak ditemukan', 'danger');
                    return;
                }
                const win = window.open('', '_blank', 'width=900,height=700');
                if (!win) {
                    showToast('Izinkan pop-up untuk export PDF', 'warning');
                    return;
                }
                const esc = function (s) {
                    return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                };
                win.document.write('<html><head><title>Kasus ' + esc(r.client) + '</title>' +
                    '<style>body{font-family:Arial,sans-serif;padding:20px}h2{text-align:center}' +
                    'table{border-collapse:collapse;width:100%}td,th{border:1px solid #ccc;padding:8px 10px;font-size:11pt;text-align:left}' +
                    'th{background:#f3f4f6}</style></head><body>' +
                    '<h2>HUGO Assistant — Laporan Kasus</h2>' +
                    '<p style="text-align:center;font-size:10pt;color:#666">Dicetak: ' + printedAtLabel() + '</p>' +
                    '<table><thead><tr><th>No</th><th>Klien</th><th>Kasus</th><th>Tipe</th><th>Status</th><th>Pembayaran</th><th>Deadline</th></tr></thead>' +
                    '<tbody><tr><td>' + (i + 1) + '</td><td>' + esc(r.client) + '</td><td>' + esc(r.case) + '</td><td>' + esc(r.type) +
                    '</td><td>' + esc(r.status) + '</td><td>' + esc(r.pay_status) + '</td><td>' + esc(r.deadline) + '</td></tr></tbody></table></body></html>');
                win.document.close();
                setTimeout(function () { win.print(); }, 400);
            }
        </script>
    @endpush
@endsection
