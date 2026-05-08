@extends('layout')
@section('page-title', 'Staff Management')
@section('content')
<div class="animate-slide-up">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
        <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Manajemen Staff</h2>
        <button class="btn btn-primary" onclick="window.location.href='{{ route('staff.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Staff
        </button>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:18px; font-size:14px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="chart-card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>ID Staff</th>
                        <th>Nama Lengkap</th>
                        <th>Jabatan</th>
                        <th style="text-align: center;">Status</th>
                        <th>Email</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $s)
                    <tr>
                        <td data-label="ID">{{ $s->id_staff }}</td>
                        <td data-label="Nama" style="font-weight: 600; color: #111827;">{{ $s->name }}</td>
                        <td data-label="Jabatan">{{ $s->position }}</td>
                        <td data-label="Status">
                            <span class="badge-premium badge-green">
                                {{ $s->work_status }}
                            </span>
                        </td>
                        <td data-label="Email">{{ $s->email ?: '-' }}</td>
                        <td data-label="">
                            <div style="display: flex; gap: 8px; flex-wrap:wrap;">
                                <a href="{{ route('staff.edit', $s->id_staff) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 11px;">Edit</a>
                                <form action="{{ route('staff.destroy', $s->id_staff) }}" method="POST" onsubmit="return confirm('Hapus staff ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 11px;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state">
                        <td colspan="6">Belum ada data staff.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 18px;">
        {{ $staffs->links() }}
    </div>
</div>
@endsection
