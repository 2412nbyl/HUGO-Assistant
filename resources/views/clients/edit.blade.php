@extends('layout')
@section('page-title', 'Edit Klien')

@push('styles')
<style>
    /* Edit Klien — mobile-first sticky footer */
    .edit-klien-wrap {
        max-width: 640px;
        width: 100%;
    }
    .edit-klien-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .edit-klien-fields {
        padding: 24px 24px 0;
        display: flex;
        flex-direction: column;
    }
    .edit-klien-footer {
        position: sticky;
        bottom: 0;
        z-index: 10;
        background: #fff;
        border-top: 1px solid #f3f4f6;
        padding: 16px 24px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        box-shadow: 0 -4px 12px rgba(0,0,0,.04);
    }

    @media (max-width: 768px) {
        .edit-klien-fields {
            padding: 16px 16px 0;
            /* Allow the whole page to scroll naturally — don't clip */
        }
        .edit-klien-footer {
            padding: 12px 16px;
        }
        /* Limit notes textarea height on mobile so button stays visible */
        .edit-klien-notes-field {
            max-height: 120px;
            min-height: 80px;
            resize: vertical;
        }
    }
</style>
@endpush

@section('content')
<div class="animate-slide-up">
    @include('partials.page-header', [
        'backUrl' => route('clients.index'),
        'backLabel' => 'Kembali ke Daftar',
        'title' => 'Edit Data Klien: ' . $client->name,
        'subtitle' => $client->id_klien,
    ])

    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:14px;">
            <strong>Gagal:</strong> {{ $errors->first() }}
        </div>
    @endif

    <div class="edit-klien-wrap">
        <form action="{{ route('clients.update', $client->id_klien) }}" method="POST" class="edit-klien-card">
            @csrf
            @method('PUT')

            <div class="edit-klien-fields">
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
                    <textarea name="address" placeholder="Jl. Mawar No. 123..." style="resize:vertical; min-height:80px;">{{ old('address', $client->address) }}</textarea>
                </div>
                <div class="form-row" style="margin-bottom: 8px;">
                    <label>Catatan Khusus</label>
                    <textarea name="notes" class="edit-klien-notes-field"
                        placeholder="Catatan tambahan tentang klien..."
                        style="resize:vertical; min-height:100px;">{{ old('notes', $client->notes) }}</textarea>
                </div>
            </div>

            <div class="edit-klien-footer">
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
