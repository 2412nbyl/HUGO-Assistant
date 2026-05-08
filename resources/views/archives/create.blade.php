@extends('layout')
@section('page-title', 'Tambah Arsip')
@section('content')
<div class="animate-slide-up">
    <div style="margin-bottom: 22px;">
        <a href="{{ route('archives.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:var(--text-muted); text-decoration:none; font-size:13.5px; margin-bottom:12px;">
            <svg viewBox="0 0 24 24" style="width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5;"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar
        </a>
        <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Tambah Arsip Baru</h2>
    </div>

    <div class="chart-card" style="max-width: 600px;">
        <form action="{{ route('archives.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <label>Pilih Kasus *</label>
                <select name="id_kasus" required onchange="updateClientInfo(this)">
                    <option value="">-- Pilih Kasus --</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id_kasus }}" data-client-id="{{ $case->id_klien }}" data-client-name="{{ $case->client_name }}">
                            {{ $case->id_kasus }} - {{ $case->case_name }} ({{ $case->client_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <input type="hidden" name="id_klien" id="id_klien">
            
            <div class="form-row">
                <label>Nama Klien</label>
                <input type="text" name="client_name" id="client_name" readonly style="background:#f3f4f6; cursor:not-allowed;">
            </div>

            <div class="form-row">
                <label>Lokasi Folder Penyimpanan (Physical Location) *</label>
                <input type="text" name="folder_location" required placeholder="Contoh: Rak A, Map Biru No. 45">
            </div>

            <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('archives.index') }}'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data Arsip</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateClientInfo(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('id_klien').value = opt.getAttribute('data-client-id') || '';
    document.getElementById('client_name').value = opt.getAttribute('data-client-name') || '';
}
</script>
@endsection
