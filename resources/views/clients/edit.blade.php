@extends('layout')
@section('page-title', 'Edit Klien')
@section('content')
<div class="animate-slide-up">
    <div style="margin-bottom: 22px;">
        <a href="{{ route('clients.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:var(--text-muted); text-decoration:none; font-size:13.5px; margin-bottom:12px;">
            <svg viewBox="0 0 24 24" style="width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5;"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar
        </a>
        <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Edit Data Klien: {{ $client->name }}</h2>
        <span style="font-family: monospace; font-size: 12px; color: var(--text-muted);">{{ $client->id_klien }}</span>
    </div>

    <div class="chart-card" style="max-width: 600px;">
        <form action="{{ route('clients.update', $client->id_klien) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-row">
                <label>Nama Lengkap Klien *</label>
                <input type="text" name="name" placeholder="Contoh: Budi Santoso" required value="{{ old('name', $client->name) }}">
            </div>
            <div class="form-row">
                <label>Nomor Telepon Klien</label>
                <input type="tel" name="phone" placeholder="0812..." value="{{ old('phone', $client->phone) }}">
            </div>
            <div class="form-row">
                <label>Tanggal Lahir Klien</label>
                <input type="date" name="birth_date" value="{{ old('birth_date', $client->birth_date ? $client->birth_date->format('Y-m-d') : '') }}">
            </div>
            <div class="form-row">
                <label>Alamat Lengkap Klien</label>
                <textarea name="address" placeholder="Jl. Mawar No. 123...">{{ old('address', $client->address) }}</textarea>
            </div>
            <div class="form-row">
                <label>Catatan Khusus</label>
                <textarea name="notes" placeholder="Catatan tambahan tentang klien...">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('clients.index') }}'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
