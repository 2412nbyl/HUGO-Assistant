@extends('layout')
@section('page-title', 'Dokumen Kasus Selesai')
@section('content')

<style>
    .fc-header-row {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .fc-filter-bar {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        padding: 14px 18px; display: flex; align-items: center;
        gap: 10px; flex-wrap: wrap; margin-bottom: 16px;
    }
    .fc-search-wrap { flex: 1; min-width: 200px; position: relative; }
    .fc-search-wrap input {
        width: 100%; padding: 9px 14px 9px 38px; border: 1.5px solid #e5e7eb;
        border-radius: 10px; font-size: 13.5px; font-family: 'Inter', sans-serif;
        background: #f9fafb; color: #1f2937; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .fc-search-wrap input:focus {
        border-color: var(--accent); box-shadow: 0 0 0 3px rgba(220,38,38,.08); background: #fff;
    }
    .fc-search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        width: 15px; height: 15px; stroke: #9ca3af; fill: none; stroke-width: 2; pointer-events: none;
    }

    /* Card list & Expandable cards - identical to archives folder list */
    .fc-list { display: flex; flex-direction: column; gap: 14px; }
    .fc-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px; overflow: hidden;
        transition: box-shadow .15s;
    }
    .fc-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }
    .fc-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; gap: 12px; flex-wrap: wrap;
        cursor: pointer; user-select: none;
    }
    .fc-card-header:hover { background: #fafafa; }
    .fc-card-left { display: flex; align-items: center; gap: 14px; }
    
    /* Green gradient folder icon to signify Finished/Completed */
    .fc-folder-icon {
        width: 42px; height: 42px; border-radius: 12px;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .fc-folder-icon svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 1.8; }
    
    .fc-meta { flex: 1; min-width: 0; }
    .fc-client-name { font-size: 15px; font-weight: 700; color: #111827; }
    .fc-case-name {
        font-size: 12px; color: #6b7280; margin-top: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px;
    }
    .fc-card-badges { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
    .fc-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-family: monospace; font-size: 11px; color: #6b7280;
        background: #f3f4f6; padding: 2px 8px; border-radius: 6px;
    }
    .fc-badge-tag { background: #e0f2fe; color: #0369a1; font-weight: 600; }
    .fc-badge-completed { background: #d1fae5; color: #065f46; font-weight: 700; }

    .fc-card-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .fc-chevron { transition: transform .2s; }
    .fc-card.open .fc-chevron { transform: rotate(180deg); }

    /* Storage box integration inside expanded view */
    .fc-storage-box {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 12px 18px; margin: 14px 20px; display: flex;
        align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
    }
    .fc-storage-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .fc-storage-icon {
        width: 34px; height: 34px; border-radius: 8px;
        background: #e2e8f0; display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; color: #475569;
    }
    .fc-storage-icon svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; }
    .fc-storage-path-wrap { min-width: 0; }
    .fc-storage-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .fc-storage-path {
        font-size: 12.5px; color: #334155; font-family: monospace; font-weight: 600;
        margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 450px;
    }

    /* Doc list inside card */
    .fc-doc-list { border-top: 1px solid #f3f4f6; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .fc-doc-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 20px 12px 62px; gap: 12px;
        border-bottom: 1px solid #f9fafb;
        transition: background .1s;
        min-width: 0;
    }
    .fc-doc-item:last-child { border-bottom: none; }
    .fc-doc-item:hover { background: #fafafa; }
    .fc-doc-left { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; overflow: hidden; }
    .fc-doc-icon {
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .fc-doc-icon.pdf  { background: #fef2f2; }
    .fc-doc-icon.img  { background: #eff6ff; }
    .fc-doc-icon.word { background: #eff6ff; }
    .fc-doc-icon.other { background: #f3f4f6; }
    .fc-doc-icon svg { width: 16px; height: 16px; stroke-width: 2; }
    .fc-doc-meta { min-width: 0; flex: 1; }
    .fc-doc-name { font-size: 13px; font-weight: 600; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px; }
    .fc-doc-time {
        font-size: 11.5px; color: #9ca3af; margin-top: 3px;
        display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    }
    .fc-doc-actions { display: flex; gap: 6px; flex-shrink: 0; align-items: center; }

    /* Action buttons */
    .fc-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; border: 1.5px solid #e5e7eb;
        border-radius: 8px; background: #fff; color: #374151;
        font-size: 12px; font-weight: 600; cursor: pointer;
        text-decoration: none; transition: all .15s; white-space: nowrap;
        font-family: 'Inter', sans-serif;
    }
    .fc-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
    .fc-btn:hover { border-color: var(--accent); color: var(--accent); background: #fff5f0; }
    .fc-btn-view { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .fc-btn-view:hover { border-color: #2563eb; color: #2563eb; background: #dbeafe; }

    /* Empty state */
    .fc-empty { text-align: center; padding: 60px 24px; color: #9ca3af; }
    .fc-empty svg { width: 52px; height: 52px; stroke: currentColor; fill: none; stroke-width: 1.4; margin: 0 auto 14px; display: block; }
    .fc-empty-title { font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .fc-empty-sub { font-size: 13.5px; }

    @media (max-width: 768px) {
        .fc-doc-item { padding-left: 20px; }
        .fc-storage-box { margin: 10px 14px; padding: 10px 12px; }
    }
    @media (max-width: 640px) {
        .fc-doc-item { padding-left: 14px; padding-right: 14px; }
        .fc-case-name { max-width: 180px; }
        .fc-doc-name { max-width: 160px; }
        .fc-card-header { padding: 12px 14px; }
        .fc-storage-path { max-width: 180px; }
    }
</style>

<div class="animate-slide-up">

    {{-- ── Page Header ── --}}
    <div class="fc-header-row">
        <div></div>
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('finished-cases.index') }}" class="fc-filter-bar">
        <div class="fc-search-wrap">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama klien, nama kasus, atau ID kasus..." autocomplete="off">
        </div>
        <button type="submit" class="btn btn-secondary" style="white-space:nowrap;">Cari</button>
        @if(request('search'))
            <a href="{{ route('finished-cases.index') }}" class="btn btn-secondary" style="white-space:nowrap;">Reset</a>
        @endif
        <div style="font-size:12.5px; color:#6b7280; white-space:nowrap; margin-left:auto;">
            Total: <strong style="color:#111827;">{{ $cases->count() }}</strong> kasus selesai
        </div>
    </form>

    {{-- ── Finished Cases List ── --}}
    @if($cases->isEmpty())
        <div class="fc-empty">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
            <div class="fc-empty-title">Tidak ada kasus selesai</div>
            <div class="fc-empty-sub">Belum ada kasus yang berstatus selesai atau tidak ada hasil yang cocok dengan pencarian Anda.</div>
        </div>
    @else
        <div class="fc-list">
            @foreach($cases as $case)
                @php
                    // Collect primary files
                    $primaryFields = [
                        'file_ktp'            => 'KTP',
                        'file_npwp'           => 'NPWP',
                        'file_kk'             => 'Kartu Keluarga',
                        'file_surat_tanah'    => 'Surat Tanah',
                        'file_surat_perintah' => 'Surat Perintah',
                        'file_buku_nikah'     => 'Buku Nikah',
                    ];
                    $files = [];
                    foreach ($primaryFields as $field => $label) {
                        if (!empty($case->$field)) {
                            $files[] = [
                                'label'      => $label,
                                'filename'   => basename($case->$field),
                                'filepath'   => $case->$field,
                                'type'       => 'Persyaratan',
                                'created_at' => $case->created_at,
                            ];
                        }
                    }

                    // Add support documents
                    foreach ($case->documents as $doc) {
                        $files[] = [
                            'label'      => 'Dokumen Pendukung',
                            'filename'   => $doc->filename,
                            'filepath'   => $doc->filepath,
                            'type'       => 'Pendukung',
                            'created_at' => $doc->created_at,
                        ];
                    }
                @endphp
                <div class="fc-card {{ $loop->first ? 'open' : '' }}" id="case-card-{{ $case->id_kasus }}">
                    <div class="fc-card-header" onclick="toggleCaseFolder('{{ $case->id_kasus }}')">
                        <div class="fc-card-left">
                            <div class="fc-folder-icon">
                                <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                            </div>
                            <div class="fc-meta">
                                <div class="fc-client-name">{{ $case->client_name }}</div>
                                <div class="fc-case-name">{{ $case->case_name }}</div>
                                <div class="fc-card-badges">
                                    <span class="fc-badge">ID: {{ $case->id_kasus }}</span>
                                    <span class="fc-badge fc-badge-tag">{{ $case->type }}</span>
                                    <span class="fc-badge">{{ count($files) }} dokumen</span>
                                    <span class="fc-badge fc-badge-completed">SELESAI</span>
                                </div>
                            </div>
                        </div>
                        <div class="fc-card-right">
                            <svg class="fc-chevron" viewBox="0 0 24 24" style="width:18px;height:18px;stroke:#9ca3af;fill:none;stroke-width:2;flex-shrink:0;">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Expanded section --}}
                    <div class="fc-expanded-section" id="docs-{{ $case->id_kasus }}" style="{{ $loop->first ? '' : 'display:none;' }}">
                        {{-- Local storage box integration --}}
                        <div class="fc-storage-box">
                            <div class="fc-storage-left">
                                <div class="fc-storage-icon">
                                    @if($case->is_physical)
                                        <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="2" y1="20" x2="20" y2="2"/></svg>
                                    @endif
                                </div>
                                <div class="fc-storage-path-wrap">
                                    <div class="fc-storage-label">Path Penyimpanan {{ $case->is_physical ? '(Fisik)' : '(Server / Local)' }}</div>
                                    <div class="fc-storage-path" title="{{ $case->folder_location }}">{{ $case->folder_location }}</div>
                                </div>
                            </div>
                            @if(!$case->is_physical)
                                <button class="fc-btn" onclick="event.stopPropagation(); openLocalFolder('{{ $case->id_kasus }}')">
                                    <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    Buka Folder
                                </button>
                            @endif
                        </div>

                        {{-- Documents list --}}
                        <div class="fc-doc-list">
                            @if(count($files) === 0)
                                <div style="padding:20px 62px; font-size:13px; color:#9ca3af; display:flex; align-items:center; gap:8px;">
                                    <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:#d1d5db;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Belum ada dokumen yang diunggah untuk kasus ini.
                                </div>
                            @else
                                @foreach($files as $f)
                                    @php
                                        $ext = strtolower(pathinfo($f['filename'], PATHINFO_EXTENSION));
                                        $isPdf  = $ext === 'pdf';
                                        $isImg  = in_array($ext, ['jpg','jpeg','png','webp','gif']);
                                        $isWord = in_array($ext, ['doc','docx']);
                                        $iconClass = $isPdf ? 'pdf' : ($isImg ? 'img' : ($isWord ? 'word' : 'other'));
                                        
                                        // Build actual file server path
                                        $serverPath = storage_path('app/public/' . $f['filepath']);
                                        $serverPath = str_replace('/', DIRECTORY_SEPARATOR, $serverPath);
                                    @endphp
                                    <div class="fc-doc-item">
                                        <div class="fc-doc-left">
                                            <div class="fc-doc-icon {{ $iconClass }}">
                                                @if($isPdf)<svg viewBox="0 0 24 24" style="stroke:#ef4444;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @elseif($isImg)<svg viewBox="0 0 24 24" style="stroke:#3b82f6;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @elseif($isWord)<svg viewBox="0 0 24 24" style="stroke:#2563eb;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                @else<svg viewBox="0 0 24 24" style="stroke:#6b7280;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @endif
                                            </div>
                                            <div class="fc-doc-meta">
                                                <div class="fc-doc-name" title="{{ $f['filename'] }}">{{ $f['filename'] }}</div>
                                                <div class="fc-doc-time">
                                                    <span style="background:{{ $f['type'] === 'Persyaratan' ? '#dbeafe' : '#dcfce7' }}; color:{{ $f['type'] === 'Persyaratan' ? '#1d4ed8' : '#166534' }}; font-size:10px; font-weight:700; padding:1px 7px; border-radius:6px; text-transform:uppercase; letter-spacing:.3px;">
                                                        {{ $f['label'] }}
                                                    </span>
                                                    <span title="Server path: {{ $serverPath }}">
                                                        Path: {{ Str::limit($serverPath, 40) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fc-doc-actions">
                                            <a href="{{ asset('storage/' . $f['filepath']) }}" target="_blank" class="fc-btn fc-btn-view" title="Lihat">
                                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $f['filepath']) }}" download="{{ $f['filename'] }}" class="fc-btn" title="Unduh">
                                                <svg viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@push('scripts')
<script>
function toggleCaseFolder(id) {
    var card = document.getElementById('case-card-' + id);
    var docs = document.getElementById('docs-' + id);
    if (!card || !docs) return;
    var isOpen = card.classList.toggle('open');
    docs.style.display = isOpen ? '' : 'none';
}

function openLocalFolder(id) {
    showToast('Membuka folder explorer...', 'info');
    fetch(`/finished-cases/${id}/open-folder`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            showToast(res.message, 'success');
        } else {
            showToast(res.message || 'Gagal membuka folder.', 'danger');
        }
    })
    .catch(() => {
        showToast('Gagal menghubungi server.', 'danger');
    });
}
</script>
@endpush

@endsection
