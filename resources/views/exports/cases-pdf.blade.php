<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Kasus — HUGO Assistant</title>
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #1f2937; }
    h1 { font-size: 18px; color: #CC3300; margin-bottom: 4px; }
    .sub { font-size: 11px; color: #6b7280; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #111827; color: #fff; padding: 8px 10px; text-align: left; font-size: 11px; }
    td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
    tr:nth-child(even) td { background: #f9fafb; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }
    .proses   { background: #dbeafe; color: #1d4ed8; }
    .selesai  { background: #dcfce7; color: #15803d; }
    .tertunda { background: #fef9c3; color: #92400e; }
    @media print { @page { margin: 1.5cm; } }
</style>
</head>
<body>
    <h1>HUGO Assistant — Laporan Kasus</h1>
    <div class="sub">Dicetak: {{ now()->format('d M Y H:i') }} &nbsp;|&nbsp; Total: {{ $cases->count() }} kasus</div>
    <table>
        <thead>
            <tr>
                <th>No</th><th>Nama Klien</th><th>Nama Kasus</th><th>Jenis</th>
                <th>Status</th><th>Deadline</th><th>Nominal</th><th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cases as $i => $c)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $c->client_name }}</td>
                <td>{{ $c->case_name }}</td>
                <td>{{ $c->type }}</td>
                <td><span class="badge {{ $c->status }}">{{ ucfirst($c->status) }}</span></td>
                <td>{{ $c->deadline?->format('d M Y') }}</td>
                <td>{{ $c->nominal_bayar ? 'Rp '.number_format($c->nominal_bayar,0,',','.') : '-' }}</td>
                <td>{{ $c->creator?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <script>window.onload = () => window.print();</script>
</body>
</html>
