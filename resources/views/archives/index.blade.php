@extends('layout')
@section('page-title', 'Archive Document')
@section('content')
<div class="animate-slide-up">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
        <h2 style="font-size: 20px; font-weight: 700; color: #111827;">Arsip Dokumen Persyaratan</h2>
        <button class="btn btn-primary" onclick="window.location.href='{{ route('archives.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Arsip
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
                        <th>ID Arsip</th>
                        <th>Nama Klien</th>
                        <th>ID Kasus</th>
                        <th>Lokasi Folder</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives as $a)
                    <tr>
                        <td data-label="ID Arsip" class="font-mono">{{ $a->id_arsip }}</td>
                        <td data-label="Klien" style="font-weight: 600; color: #111827;">{{ $a->client_name }}</td>
                        <td data-label="ID Kasus">{{ $a->id_kasus }}</td>
                        <td data-label="Folder">
                            <div class="badge-premium badge-green">
                                <svg style="width:12px;height:12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                {{ $a->folder_location }}
                            </div>
                        </td>
                        <td data-label="">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('archives.edit', $a->id_arsip) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 11px;">Edit</a>
                                <form action="{{ route('archives.destroy', $a->id_arsip) }}" method="POST" onsubmit="return confirm('Hapus arsip ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 11px;">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-state">
                        <td colspan="5">Belum ada data arsip.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 18px;">
        {{ $archives->links() }}
    </div>
</div>
@endsection
