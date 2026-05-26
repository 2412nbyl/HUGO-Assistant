@extends('layout')
@section('page-title', 'Tambah Klien')
@section('content')
<div class="animate-slide-up">
    @include('partials.page-header', [
        'backUrl' => route('clients.index'),
        'backLabel' => 'Kembali ke Daftar',
        'title' => 'Tambah Klien Baru',
    ])

    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="chart-card form-card">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <label>Nama Lengkap Klien *</label>
                <input type="text" name="name" placeholder="Contoh: Budi Santoso" required value="{{ old('name') }}">
            </div>
            <div class="form-row">
                <label>Nomor Telepon Klien</label>
                <input type="tel" name="phone" placeholder="0812..." value="{{ old('phone') }}">
            </div>
            <div class="form-row">
                <label>Tanggal Lahir Klien</label>
                <input type="date" name="birth_date" value="{{ old('birth_date') }}">
            </div>
            <div class="form-row">
                <label>Alamat Lengkap Klien</label>
                <textarea name="address" placeholder="Jl. Mawar No. 123...">{{ old('address') }}</textarea>
            </div>
            <div class="form-row">
                <label>Catatan Khusus</label>
                <textarea name="notes" placeholder="Catatan tambahan tentang klien...">{{ old('notes') }}</textarea>
            </div>

            <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('clients.index') }}'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data Klien</button>
            </div>
        </form>
    </div>
</div>
@endsection
