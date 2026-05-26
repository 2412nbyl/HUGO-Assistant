@extends('layout')
@section('page-title', 'Tambah Staff')
@section('content')
    <div class="animate-slide-up">
        @include('partials.page-header', [
            'backUrl' => route('staff.index'),
            'backLabel' => 'Kembali ke Daftar',
            'title' => 'Tambah Staff Baru',
            'subtitle' => 'Pilih akun terdaftar untuk mengisi data otomatis, atau isi manual.',
        ])

        <div class="chart-card form-card">
            <form action="{{ route('staff.store') }}" method="POST" id="staff-form">
                @csrf
                <div class="form-row">
                    <label for="staff-id-user">Pilih Akun Terdaftar (Username)</label>
                    <select name="id_user" id="staff-id-user" data-popup-title="Akun Pengguna">
                        <option value="">-- Tidak Terhubung ke Akun --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('id_user') == $user->id)>
                                {{ $user->name }} ({{ $user->username }}) — {{ ucfirst($user->role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Nama Lengkap Staff *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP" value="{{ old('name') }}">
                </div>
                <div class="form-split">
                    <div class="form-row">
                        <label>Jabatan *</label>
                        <input type="text" name="position" required placeholder="Contoh: Admin Legal" value="{{ old('position') }}">
                    </div>
                    <div class="form-row">
                        <label>Status Kerja *</label>
                        <select name="work_status" data-popup-title="Status Kerja">
                            <option value="Aktif" @selected(old('work_status', 'Aktif') == 'Aktif')>Aktif</option>
                            <option value="Cuti" @selected(old('work_status') == 'Cuti')>Cuti</option>
                            <option value="Resign" @selected(old('work_status') == 'Resign')>Resign</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label>Nomor Telepon</label>
                    <input type="tel" name="phone" placeholder="08..." value="{{ old('phone') }}">
                </div>
                <div class="form-row">
                    <label>Email Pribadi</label>
                    <input type="email" name="email" placeholder="staff@example.com" value="{{ old('email') }}">
                </div>
                <div class="form-row">
                    <label>Alamat Lengkap</label>
                    <textarea name="address" placeholder="Alamat tinggal saat ini...">{{ old('address') }}</textarea>
                </div>
                <div class="form-row">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}">
                </div>
                <div class="form-row">
                    <label>Catatan Khusus</label>
                    <textarea name="notes" placeholder="Catatan internal tentang staff ini...">{{ old('notes') }}</textarea>
                </div>

                <div class="modal-footer" style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid #f3f4f6;">
                    <button type="button" class="btn btn-secondary"
                        onclick="window.location.href='{{ route('staff.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data Staff</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>window.STAFF_USER_MAP = @json($usersForStaff);</script>
<script src="{{ asset('js/staff-form.js') }}?v={{ filemtime(public_path('js/staff-form.js')) }}"></script>
@endpush
