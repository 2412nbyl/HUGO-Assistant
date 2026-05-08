@extends('layout')
@section('page-title', 'Tambah Staff')
@section('content')
    <div class="animate-slide-up">
        <div style="margin-bottom: 22px;">
            <a href="{{ route('staff.index') }}"
                style="display:inline-flex; align-items:center; gap:6px; color:var(--text-muted); text-decoration:none; font-size:13.5px; margin-bottom:12px;">
                <svg viewBox="0 0 24 24" style="width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.5;">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Kembali ke Daftar
            </a>
            <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Tambah Staff Baru</h2>
        </div>

        <div class="chart-card" style="max-width: 600px;">
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <label>Pilih Akun Terdaftar (Username)</label>
                    <select name="id_user">
                        <option value="">-- Tidak Terhubung ke Akun --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Nama Lengkap Staff *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-row">
                        <label>Jabatan *</label>
                        <input type="text" name="position" required placeholder="Contoh: Admin Legal">
                    </div>
                    <div class="form-row">
                        <label>Status Kerja *</label>
                        <select name="work_status">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Resign">Resign</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="phone" placeholder="08...">
                </div>
                <div class="form-row">
                    <label>Email Pribadi</label>
                    <input type="email" name="email" placeholder="staff@example.com">
                </div>
                <div class="form-row">
                    <label>Alamat Lengkap</label>
                    <textarea name="address" placeholder="Alamat tinggal saat ini..."></textarea>
                </div>
                <div class="form-row">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date">
                </div>
                <div class="form-row">
                    <label>Catatan Khusus</label>
                    <textarea name="notes" placeholder="Catatan internal tentang staff ini..."></textarea>
                </div>

                <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                    <button type="button" class="btn btn-secondary"
                        onclick="window.location.href='{{ route('staff.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data Staff</button>
                </div>
            </form>
        </div>
    </div>
@endsection
