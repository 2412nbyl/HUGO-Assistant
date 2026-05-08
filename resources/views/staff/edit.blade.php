@extends('layout')
@section('page-title', 'Edit Staff')
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
            <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Edit Staff — {{ $staff->name }}</h2>
        </div>

        @if($errors->any())
            <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:14px;">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="chart-card" style="max-width: 600px;">
            <form action="{{ route('staff.update', $staff->id_staff) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <label>Pilih Akun Terdaftar (Username)</label>
                    <select name="id_user">
                        <option value="">-- Tidak Terhubung ke Akun --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $staff->id_user == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Nama Lengkap Staff *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP"
                        value="{{ old('name', $staff->name) }}">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-row">
                        <label>Jabatan *</label>
                        <input type="text" name="position" required placeholder="Contoh: Admin Legal"
                            value="{{ old('position', $staff->position) }}">
                    </div>
                    <div class="form-row">
                        <label>Status Kerja *</label>
                        <select name="work_status">
                            <option value="Aktif" {{ old('work_status', $staff->work_status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Cuti" {{ old('work_status', $staff->work_status) == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                            <option value="Resign" {{ old('work_status', $staff->work_status) == 'Resign' ? 'selected' : '' }}>Resign</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="phone" placeholder="08..."
                        value="{{ old('phone', $staff->phone) }}">
                </div>
                <div class="form-row">
                    <label>Email Pribadi</label>
                    <input type="email" name="email" placeholder="staff@example.com"
                        value="{{ old('email', $staff->email) }}">
                </div>
                <div class="form-row">
                    <label>Alamat Lengkap</label>
                    <textarea name="address" placeholder="Alamat tinggal saat ini...">{{ old('address', $staff->address) }}</textarea>
                </div>
                <div class="form-row">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date"
                        value="{{ old('birth_date', $staff->birth_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-row">
                    <label>Catatan Khusus</label>
                    <textarea name="notes" placeholder="Catatan internal tentang staff ini...">{{ old('notes', $staff->notes) }}</textarea>
                </div>

                <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                    <button type="button" class="btn btn-secondary"
                        onclick="window.location.href='{{ route('staff.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
