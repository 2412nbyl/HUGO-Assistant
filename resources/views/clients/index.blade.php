@extends('layout')
@section('page-title', 'Client List')
@section('content')
<div class="animate-slide-up">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
        <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Daftar Klien</h2>
        <button class="btn btn-primary" onclick="window.location.href='{{ route('clients.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Klien
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
                        <th>ID Klien</th>
                        <th>Nama Lengkap</th>
                        <th>Telepon</th>
                        <th style="text-align: center;">Lahir</th>
                        <th style="text-align: center;">Total Kasus</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $c)
                    <tr>
                        <td data-label="ID">{{ $c->id_klien }}</td>
                        <td data-label="Nama" style="font-weight: 600; color: #111827;">{{ $c->name }}</td>
                        <td data-label="Telepon">{{ $c->phone ?: '-' }}</td>
                        <td data-label="Lahir">{{ $c->birth_date ? $c->birth_date->format('d/m/Y') : '-' }}</td>
                        <td data-label="Kasus" style="text-align: center;">
                            <span class="badge-premium badge-blue">
                                {{ $c->cases_count ?? $c->cases()->count() }}
                            </span>
                        </td>
                        <td data-label="">
                            <div style="display: flex; gap: 8px; flex-wrap:wrap;">
                                <a href="{{ route('clients.edit', $c->id_klien) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 11px;">Edit</a>
                                <form action="{{ route('clients.destroy', $c->id_klien) }}" method="POST" onsubmit="return confirm('Hapus klien ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 11px;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state">
                        <td colspan="6">Belum ada data klien.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 18px;">
        {{ $clients->links() }}
    </div>
</div>
@endsection
