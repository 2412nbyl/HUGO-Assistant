@extends('layout')
@section('page-title', 'Tambah Arsip')
@section('content')
<div class="animate-slide-up">
    @include('partials.page-header', [
        'backUrl' => route('archives.index'),
        'backLabel' => 'Kembali ke Daftar',
        'title' => 'Tambah Arsip Baru',
    ])

    <div class="chart-card form-card">
        <form action="{{ route('archives.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <label>Pilih Kasus *</label>
                <select name="id_kasus" data-popup-title="ID Kasus" required onchange="updateClientInfo(this)">
                    <option value="">-- Pilih Kasus --</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id_kasus }}" data-client-id="{{ $case->id_klien }}" data-client-name="{{ $case->client_name }}">
                            {{ $case->id_kasus }} - {{ $case->case_name }} ({{ $case->client_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            
            <input type="hidden" name="id_klien" id="id_klien">
            
            <div class="form-row">
                <label>Nama Klien</label>
                <input type="text" name="client_name" id="client_name" readonly style="background:#f3f4f6; cursor:not-allowed;">
            </div>

            <!-- Storage Type Selector -->
            <div class="form-row">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#374151;">Jenis Penyimpanan (Storage Type) *</label>
                <div style="display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
                    <label style="flex:1; min-width:200px; border:2.5px solid var(--accent); border-radius:12px; padding:14px; display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; font-size:14px; color:#111827; background:#fff5f0; transition:all 0.2s; box-shadow:0 2px 8px rgba(204,51,0,0.06);" id="label-type-digital">
                        <input type="radio" name="storage_type" value="digital" checked style="accent-color:var(--accent); transform:scale(1.15);" onchange="toggleStorageType('digital')">
                        <span>💻 Digital (Internal Path)</span>
                    </label>
                    <label style="flex:1; min-width:200px; border:2.5px solid #e5e7eb; border-radius:12px; padding:14px; display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; font-size:14px; color:#374151; background:#fff; transition:all 0.2s;" id="label-type-physical">
                        <input type="radio" name="storage_type" value="physical" style="accent-color:var(--accent); transform:scale(1.15);" onchange="toggleStorageType('physical')">
                        <span>🗄️ Fisik (Eksternal / Physical)</span>
                    </label>
                </div>
            </div>

            <!-- Digital Storage Input -->
            <div class="form-row" id="group-digital">
                <label>Pilih Folder / Path Internal *</label>
                <select id="select-digital-path" data-popup-title="Pilih Path Internal" onchange="handleDigitalSelect(this)">
                    <option value="/internal/storage/archives/2026/">💻 /internal/storage/archives/2026/</option>
                    <option value="/internal/storage/cases/">💻 /internal/storage/cases/</option>
                    <option value="/internal/storage/akta/">💻 /internal/storage/akta/</option>
                    <option value="custom">✍️ Tulis Path Kustom...</option>
                </select>
                <div id="custom-digital-wrap" style="display:none; margin-top:10px;">
                    <label style="font-size:12px; color:#6b7280; margin-bottom:4px; display:block;">Masukkan Path Internal Kustom:</label>
                    <input type="text" id="input-custom-digital" placeholder="Contoh: /storage/cases/klien-A">
                </div>
            </div>

            <!-- Physical Storage Input -->
            <div class="form-row" id="group-physical" style="display:none;">
                <label>Pilih Lokasi Fisik / Eksternal *</label>
                <select id="select-physical-path" data-popup-title="Pilih Lokasi Fisik" onchange="handlePhysicalSelect(this)">
                    <option value="Rak Utama, Map Biru A">🗄️ Rak Utama, Map Biru A</option>
                    <option value="Lemari Arsip Utama (Main Cabinet)">🗄️ Lemari Arsip Utama (Main Cabinet)</option>
                    <option value="Laci Dokumen Akta (Notary Drawer)">🗄️ Laci Dokumen Akta (Notary Drawer)</option>
                    <option value="custom">✍️ Tulis Lokasi Kustom...</option>
                </select>
                <div id="custom-physical-wrap" style="display:none; margin-top:10px;">
                    <label style="font-size:12px; color:#6b7280; margin-bottom:4px; display:block;">Masukkan Lokasi Fisik Kustom:</label>
                    <input type="text" id="input-custom-physical" placeholder="Contoh: Rak C, Laci Nomor 5">
                </div>
            </div>

            <!-- Real Form Data Submission -->
            <input type="hidden" name="folder_location" id="folder_location" required>

            <div class="modal-footer" style="margin-top:28px; padding-top:16px; border-top:1px solid #f3f4f6;">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('archives.index') }}'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data Arsip</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateClientInfo(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('id_klien').value = opt.getAttribute('data-client-id') || '';
    document.getElementById('client_name').value = opt.getAttribute('data-client-name') || '';
}

function toggleStorageType(type) {
    const digitalLabel = document.getElementById('label-type-digital');
    const physicalLabel = document.getElementById('label-type-physical');
    const digitalGroup = document.getElementById('group-digital');
    const physicalGroup = document.getElementById('group-physical');

    if (type === 'digital') {
        digitalLabel.style.borderColor = 'var(--accent)';
        digitalLabel.style.background = '#fff5f0';
        digitalLabel.style.fontWeight = '700';
        digitalLabel.style.color = '#111827';
        digitalLabel.style.boxShadow = '0 2px 8px rgba(204,51,0,0.06)';

        physicalLabel.style.borderColor = '#e5e7eb';
        physicalLabel.style.background = '#fff';
        physicalLabel.style.fontWeight = '600';
        physicalLabel.style.color = '#374151';
        physicalLabel.style.boxShadow = 'none';

        digitalGroup.style.display = 'block';
        physicalGroup.style.display = 'none';

        updateFolderLocation('digital');
    } else {
        physicalLabel.style.borderColor = 'var(--accent)';
        physicalLabel.style.background = '#fff5f0';
        physicalLabel.style.fontWeight = '700';
        physicalLabel.style.color = '#111827';
        physicalLabel.style.boxShadow = '0 2px 8px rgba(204,51,0,0.06)';

        digitalLabel.style.borderColor = '#e5e7eb';
        digitalLabel.style.background = '#fff';
        digitalLabel.style.fontWeight = '600';
        digitalLabel.style.color = '#374151';
        digitalLabel.style.boxShadow = 'none';

        physicalGroup.style.display = 'block';
        digitalGroup.style.display = 'none';

        updateFolderLocation('physical');
    }
}

function handleDigitalSelect(select) {
    const wrap = document.getElementById('custom-digital-wrap');
    if (select.value === 'custom') {
        wrap.style.display = 'block';
        document.getElementById('input-custom-digital').focus();
    } else {
        wrap.style.display = 'none';
    }
    updateFolderLocation('digital');
}

function handlePhysicalSelect(select) {
    const wrap = document.getElementById('custom-physical-wrap');
    if (select.value === 'custom') {
        wrap.style.display = 'block';
        document.getElementById('input-custom-physical').focus();
    } else {
        wrap.style.display = 'none';
    }
    updateFolderLocation('physical');
}

function updateFolderLocation(type) {
    let val = '';
    if (type === 'digital') {
        const select = document.getElementById('select-digital-path');
        if (select.value === 'custom') {
            val = document.getElementById('input-custom-digital').value.trim();
        } else {
            val = select.value;
        }
    } else {
        const select = document.getElementById('select-physical-path');
        if (select.value === 'custom') {
            val = document.getElementById('input-custom-physical').value.trim();
        } else {
            val = select.value;
        }
    }
    document.getElementById('folder_location').value = val;
}

document.addEventListener('DOMContentLoaded', function() {
    const inputCustomDigital = document.getElementById('input-custom-digital');
    const inputCustomPhysical = document.getElementById('input-custom-physical');

    if (inputCustomDigital) {
        inputCustomDigital.addEventListener('input', function() {
            updateFolderLocation('digital');
        });
    }
    if (inputCustomPhysical) {
        inputCustomPhysical.addEventListener('input', function() {
            updateFolderLocation('physical');
        });
    }

    toggleStorageType('digital');
});
</script>
@endsection
