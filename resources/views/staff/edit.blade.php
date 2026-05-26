@extends('layout')
@section('page-title', 'Edit Staff')
@section('content')
    <div class="animate-slide-up">
        @include('partials.page-header', [
            'backUrl' => route('staff.index'),
            'backLabel' => 'Kembali ke Daftar',
            'title' => 'Edit Staff — ' . $staff->name,
        ])

        @if($errors->any())
            <div class="alert-banner alert-banner--error">{{ $errors->first() }}</div>
        @endif

        <div class="chart-card form-card">
            <form action="{{ route('staff.update', $staff->id_staff) }}" method="POST" id="staff-form">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <label for="staff-id-user">Pilih Akun Terdaftar (Username)</label>
                    <select name="id_user" id="staff-id-user" data-popup-title="Akun Pengguna">
                        <option value="">-- Tidak Terhubung ke Akun --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $staff->id_user == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->username }}) — {{ ucfirst($user->role) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Nama Lengkap Staff *</label>
                    <input type="text" name="name" required placeholder="Nama lengkap sesuai KTP"
                        value="{{ old('name', $staff->name) }}">
                </div>
                <div class="form-split">
                    <div class="form-row">
                        <label>Jabatan *</label>
                        <input type="text" name="position" required placeholder="Contoh: Admin Legal"
                            value="{{ old('position', $staff->position) }}">
                    </div>
                    <div class="form-row">
                        <label>Status Kerja *</label>
                        <select name="work_status" data-popup-title="Status Kerja">
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

                <div class="modal-footer" style="margin-top: var(--space-md); padding-top: var(--space-md); border-top: 1px solid #f3f4f6;">
                    <button type="button" class="btn btn-secondary"
                        onclick="window.location.href='{{ route('staff.index') }}'">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>window.STAFF_USER_MAP = @json($usersForStaff);</script>
<script src="{{ asset('js/staff-form.js') }}?v={{ filemtime(public_path('js/staff-form.js')) }}"></script>
@endpush
