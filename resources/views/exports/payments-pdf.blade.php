<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Pembayaran — HUGO Assistant</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #1f2937; }
    h1 { font-size: 18px; color: #CC3300; margin-bottom: 4px; }
    .sub { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #111827; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
    tr:nth-child(even) td { background: #f9fafb; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
    .lunas   { background: #dcfce7; color: #15803d; }
    .sebagian{ background: #fef9c3; color: #92400e; }
    .belum   { background: #fee2e2; color: #dc2626; }
    @media print { @page { margin: 1.5cm; } }
</style>
</head>
<body>
    <h1>HUGO Assistant — Laporan Pembayaran</h1>
    <div class="sub">Dicetak: {{ now()->format('d M Y H:i') }} &nbsp;|&nbsp; Total: {{ $payments->count() }} record</div>
    <table>
        <thead>
            <tr><th>No</th><th>Klien</th><th>Nama Kasus</th><th>Jumlah</th><th>Status</th><th>Tgl Update</th></tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $p)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $p->case?->client_name ?? '-' }}</td>
                <td>{{ $p->case?->case_name ?? '-' }}</td>
                <td>{{ $p->amount ?? '-' }}</td>
                <td><span class="badge {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                <td>{{ $p->updated_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.onload = () => window.print();</script>
</body>
</html>
