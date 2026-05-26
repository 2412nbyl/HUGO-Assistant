@extends('layout')
@section('page-title', 'Detail & Edit Kasus')
@section('content')
    <div class="animate-slide-up">
        <div style="background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:28px; box-shadow:0 4px 16px rgba(0,0,0,0.04);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                <h2 style="font-size:20px; font-weight:700; color:#111827;">Kasus: {{ $case->case_name }}</h2>
                <a href="{{ route('cases.index') }}" class="btn btn-secondary">← Kembali</a>
            </div>

            <!-- SIDE-BY-SIDE LAYOUT -->
            <div class="case-edit-grid">

            <!-- LEFT COLUMN: Edit Kasus -->
            <div>
                <form method="POST" action="{{ route('cases.update', $case->id_kasus) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="case-form-grid">
                        <div class="form-row" style="grid-column:1/-1;">
                            <label>Nama Klien</label>
                            <input type="text" name="client_name" value="{{ old('client_name', $case->client_name) }}" required>
                        </div>
                        <div class="form-row">
                            <label>No. HP (WhatsApp)</label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:14px; color:#6b7280; font-weight:600;">+62</span>
                                <input type="text" name="phone" value="{{ old('phone', ltrim($case->phone, '0')) }}" 
                                    style="padding-left:45px;" placeholder="812xxxx">
                            </div>
                        </div>
                        <div class="form-row">
                            <label>Tipe Kasus</label>
                            <select name="type" data-popup-title="Tipe Kasus">
                                <option value="PT" {{ $case->type == 'PT' ? 'selected' : '' }}>PT</option>
                                <option value="CV" {{ $case->type == 'CV' ? 'selected' : '' }}>CV</option>
                                <option value="Pribadi" {{ $case->type == 'Pribadi' ? 'selected' : '' }}>Pribadi</option>
                            </select>
                        </div>
                        <div class="form-row" style="grid-column:1/-1;">
                            <label>Nama Kasus</label>
                            <input type="text" name="case_name" value="{{ old('case_name', $case->case_name) }}" required>
                        </div>
                        <div class="form-row">
                            <label>Deadline</label>
                            <input type="date" name="deadline" value="{{ $case->deadline?->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-row">
                            <label>Nominal Pembayaran (Rp)</label>
                            @php
                                $nomRaw = $case->nominal_bayar ?: 0;
                                $nomFmt = $nomRaw ? 'Rp. ' . number_format($nomRaw, 0, ',', '.') : '';
                            @endphp
                            <input type="text" name="nominal_bayar" value="{{ $nomFmt }}" 
                                class="chat-input" id="edit-nominal"
                                placeholder="Rp. 0"
                                oninput="formatRupiah(this)"
                                style="width:100%; height:42px; border-radius:10px; font-size:14px; font-weight:600; color:var(--accent);">
                        </div>
                        <div class="form-row">
                            <label>Status Progress</label>
                            <select name="status" data-popup-title="Status Progress" class="pill" style="width:100%; height:42px; padding:0 12px; border-radius:10px; appearance:auto;">
                                <option value="proses" {{ $case->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="tertunda" {{ $case->status == 'tertunda' ? 'selected' : '' }}>Tertunda</option>
                                <option value="selesai" {{ $case->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="form-row" style="grid-column:1/-1;">
                            <label>Catatan Progress (Terakhir)</label>
                            <textarea name="progress_note" rows="2" style="resize:vertical;">{{ old('progress_note', $case->progress_note) }}</textarea>
                        </div>
                    </div>

                    <h4 style="margin:28px 0 14px; font-size:15px; font-weight:700; color:#111827; border-bottom:1px solid #e5e7eb; padding-bottom:8px;">Ganti File Utama (Bila Perlu)</h4>
                    <div class="case-file-grid">
                        @foreach (['file_ktp'=>'KTP', 'file_npwp'=>'NPWP', 'file_kk'=>'KK', 'file_surat_tanah'=>'Surat Tanah', 'file_buku_nikah'=>'Buku Nikah', 'file_surat_perintah'=>'Surat Perintah'] as $k => $l)
                            <div class="form-row" style="margin:0; background:#f9fafb; padding:12px; border-radius:8px; border:1px dashed #d1d5db;">
                                <label style="display:block; font-size:12.5px; font-weight:600; color:#374151; margin-bottom:8px;">{{ $l }} @if($case->$k) <a href="{{ asset('storage/'.$case->$k) }}" target="_blank" style="color:#16a34a;font-size:11px;float:right;text-decoration:none;">✓ Lihat File</a> @endif</label>
                                <input type="file" name="{{ $k }}" accept=".pdf,.jpg,.jpeg,.png" style="font-size:12px;width:100%;">
                            </div>
                        @endforeach
                    </div>

                    @if(auth()->user()->role !== 'freelancer')
                        <div style="margin-top:32px;text-align:right;">
                            <button type="submit" class="btn btn-primary" style="padding:12px 24px; font-size:14px;">Simpan Semua Perubahan</button>
                        </div>
                    @endif
                </form>
            </div>

            <!-- RIGHT COLUMN: Dokumen & Timeline -->
            <div style="display:flex; flex-direction:column; gap:24px;">
                
                <!-- Dokumen Pendukung -->
                <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:12px; padding:20px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
                        <div>
                            <h3 style="font-size:16px; font-weight:600;">Kelola Dokumen Tambahan</h3>
                            <p style="font-size:13px; color:#6b7280;">Daftar file pendukung lainnya terkait kasus ini.</p>
                        </div>
                        @if(auth()->user()->role !== 'freelancer')
                            <button class="btn btn-primary" onclick="document.getElementById('file-upload-input').click()">+ Tambah Dokumen</button>
                            <input type="file" id="file-upload-input" style="display:none;" onchange="uploadSupportDocument(this)" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        @endif
                    </div>

                    <div class="table-container">
                        <div style="width:100%; overflow-x:auto;">
                            <table class="docs-table">
                                <thead>
                                    <tr>
                                        <th>Nama File</th>
                                        <th>Waktu</th>
                                        <th style="width:40px; text-align:center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="documents-tbody">
                                    @forelse($case->documents as $doc)
                                        <tr id="doc-row-{{ $doc->id_dok }}">
                                            <td><a href="{{ asset('storage/'.$doc->filepath) }}" target="_blank" style="color:#CC3300; font-weight:600; text-decoration:none; display:block; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $doc->filename }}">{{ $doc->filename }}</a></td>
                                            <td style="color:#6b7280;font-size:11px;">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                            <td style="text-align:center;">
                                                @if(auth()->user()->role !== 'freelancer')
                                                    <button type="button" class="btn-del" onclick="deleteSupportDocument('{{ $doc->id_dok }}')" title="Hapus Dokumen" style="margin:auto; width:28px; height:28px; background:#fff; border:1px solid #e5e7eb; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:0.2s;">
                                                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-docs-row"><td colspan="3" style="text-align:center;color:#6b7280;padding:20px;">Belum ada dokumen pendukung.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Timeline of Notes -->
                <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:20px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                    <h3 style="font-size:16px; font-weight:700; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <svg style="width:18px;height:18px;color:#CC3300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Timeline Progress
                    </h3>
                    
                    <div style="position:relative; padding-left:24px; border-left:2px solid #f3f4f6; margin-left:8px; display:flex; flex-direction:column; gap:20px;">
                        @forelse($case->caseNotes as $note)
                            <div style="position:relative;">
                                <!-- Bullet -->
                                <div style="position:absolute; left:-31px; top:4px; width:12px; height:12px; border-radius:50%; background:{{ $note->status == 'selesai' ? '#22c55e' : ($note->status == 'tertunda' ? '#ef4444' : '#f59e0b') }}; border:3px solid #fff; box-shadow:0 0 0 1px #e5e7eb;"></div>
                                
                                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">
                                    {{ $note->created_at->format('d-m-Y (H:i)') }}
                                    @if($note->user) <span style="margin-left:8px; color:#374151; font-weight:600;">• {{ $note->user->name }}</span> @endif
                                </div>
                                <div style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700; text-transform:uppercase; margin-bottom:6px;
                                    @if($note->status == 'selesai') background:#dcfce7; color:#166534; @elseif($note->status == 'tertunda') background:#fee2e2; color:#991b1b; @else background:#fef3c7; color:#92400e; @endif">
                                    {{ $note->status }}
                                </div>
                                <div style="font-size:13.5px; color:#374151; line-height:1.5; background:#f9fafb; padding:8px 12px; border-radius:8px; border:1px solid #f3f4f6;">
                                    {{ $note->note ?: '(Tanpa catatan)' }}
                                </div>
                            </div>
                        @empty
                            <div style="color:#9ca3af; font-size:13px; font-style:italic;">Belum ada riwayat progress.</div>
                        @endforelse
                    </div>
                </div>

            </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>

        function uploadSupportDocument(input) {
            if(!input.files || input.files.length === 0) return;
            const file = input.files[0];
            const formData = new FormData();
            formData.append('document', file);

            // Show loading Toast
            showToast('Mengunggah dokumen...', 'info');

            fetch("{{ route('cases.documents.upload', $case->id_kasus) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    showToast('Dokumen berhasil diunggah', 'success');
                    const tbody = document.getElementById('documents-tbody');
                    const noDocs = document.getElementById('no-docs-row');
                    if(noDocs) noDocs.remove();

                    const tr = document.createElement('tr');
                    tr.id = `doc-row-${res.document.id}`;
                    tr.innerHTML = `
                        <td><a href="${res.document.url}" target="_blank" style="color:#CC3300; font-weight:600; text-decoration:none;">${res.document.filename}</a></td>
                        <td style="color:#6b7280;font-size:11px;">${res.document.created_at}</td>
                        <td style="text-align:center;">
                            <button type="button" class="btn-del" onclick="deleteSupportDocument('${res.document.id}')" title="Hapus Dokumen" style="margin:auto; width:28px; height:28px; background:#fff; border:1px solid #e5e7eb; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#9ca3af; transition:0.2s;">
                                <svg viewBox="0 0 24 24" style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path><path d="M10 11v6M14 11v6"></path></svg>
                            </button>
                        </td>
                    `;
                    tbody.prepend(tr);
                } else {
                    showToast(res.message || 'Gagal mengunggah dokumen', 'error');
                }
                input.value = ''; // reset input
            })
            .catch(() => {
                showToast('Terjadi kesalahan jaringan', 'error');
                input.value = '';
            });
        }

        function deleteSupportDocument(docId) {
            showConfirm('Hapus Dokumen', 'Apakah Anda yakin ingin menghapus dokumen ini?', () => {
                fetch(`/cases/documents/${docId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const tr = document.getElementById(`doc-row-${docId}`);
                        if(tr) tr.remove();
                        showToast('Dokumen dihapus', 'success');
                    } else {
                        showToast(res.message || 'Gagal menghapus', 'error');
                    }
                });
            }, '🗑️');
        }

        function formatRupiah(el) {
            let val = el.value.replace(/\D/g, "");
            if (val === "") {
                el.value = "";
                return;
            }
            el.value = "Rp. " + parseInt(val).toLocaleString("id-ID");
        }
    </script>
    @endpush
@endsection
