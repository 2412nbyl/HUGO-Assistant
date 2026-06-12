@extends('layout')
@section('page-title', 'Document Archive')
@section('content')

<style>
    /* ── Tab Bar ── */
    .da-tab-bar {
        display: flex; gap: 0; background: #fff;
        border: 1px solid #e5e7eb; border-radius: 16px 16px 0 0;
        overflow: hidden; margin-bottom: 0;
    }
    .da-tab-btn {
        flex: 1; padding: 16px 20px; border: none; background: none;
        font-size: 14px; font-weight: 700; color: #6b7280; cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 8px;
        border-bottom: 3px solid transparent;
        transition: all .18s; font-family: 'Inter', sans-serif;
        position: relative;
    }
    .da-tab-btn:hover { color: #111827; background: #f9fafb; }
    .da-tab-btn.active { color: var(--accent); border-bottom-color: var(--accent); background: #fff; }
    .da-tab-btn svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }
    .da-tab-count {
        background: #f3f4f6; color: #6b7280; font-size: 11px; font-weight: 700;
        padding: 1px 7px; border-radius: 10px; min-width: 22px; text-align: center;
    }
    .da-tab-btn.active .da-tab-count { background: rgba(220,38,38,.1); color: var(--accent); }

    .da-tab-body { background: #fff; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 16px 16px; min-height: 200px; }
    .da-tab-panel { display: none; padding: 20px; }
    .da-tab-panel.active { display: block; }

    /* ── Filter Bar ── */
    .da-filter-bar {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 18px;
    }
    .da-search-wrap { flex: 1; min-width: 200px; position: relative; }
    .da-search-wrap input {
        width: 100%; padding: 9px 14px 9px 38px; border: 1.5px solid #e5e7eb;
        border-radius: 10px; font-size: 13.5px; font-family: 'Inter', sans-serif;
        background: #f9fafb; color: #1f2937; outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .da-search-wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(220,38,38,.08); background: #fff; }
    .da-search-wrap svg {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        width: 15px; height: 15px; stroke: #9ca3af; fill: none; stroke-width: 2; pointer-events: none;
    }

    /* ── Folder / Case cards ── */
    .da-card-list { display: flex; flex-direction: column; gap: 14px; }
    .da-card {
        background: #fafafa; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden;
        transition: box-shadow .15s;
    }
    .da-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }
    .da-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px; gap: 12px; flex-wrap: wrap;
        cursor: pointer; user-select: none;
    }
    .da-card-header:hover { background: rgba(0,0,0,.02); }
    .da-card-left { display: flex; align-items: center; gap: 12px; }
    .da-folder-icon {
        width: 40px; height: 40px; border-radius: 10px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .da-folder-icon.finished { background: linear-gradient(135deg, #10b981, #059669); }
    .da-folder-icon svg { width: 20px; height: 20px; stroke: #fff; fill: none; stroke-width: 1.8; }
    .da-card-meta { flex: 1; min-width: 0; }
    .da-card-name { font-size: 14px; font-weight: 700; color: #111827; }
    .da-card-sub { font-size: 12px; color: #6b7280; margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 380px; }
    .da-card-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 4px; }
    .da-badge { display: inline-flex; align-items: center; gap: 4px; font-family: monospace; font-size: 11px; color: #6b7280; background: #f3f4f6; padding: 2px 7px; border-radius: 6px; }
    .da-badge-green { background: #d1fae5; color: #065f46; font-weight: 700; }
    .da-badge-blue  { background: #e0f2fe; color: #0369a1; font-weight: 600; }
    .da-card-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .da-chevron { transition: transform .2s; }
    .da-card.open .da-chevron { transform: rotate(180deg); }

    /* ── Expanded doc list ── */
    .da-expanded { border-top: 1px solid #f3f4f6; }
    .da-doc-list { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .da-doc-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 11px 18px 11px 56px; gap: 12px;
        border-bottom: 1px solid #f9fafb; transition: background .1s; min-width: 0;
    }
    .da-doc-item:last-child { border-bottom: none; }
    .da-doc-item:hover { background: #f9fafb; }
    .da-doc-left { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 0; overflow: hidden; }
    .da-doc-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .da-doc-icon.pdf  { background: #fef2f2; }
    .da-doc-icon.img  { background: #eff6ff; }
    .da-doc-icon.word { background: #eff6ff; }
    .da-doc-icon.other { background: #f3f4f6; }
    .da-doc-icon svg { width: 15px; height: 15px; stroke-width: 2; }
    .da-doc-meta { min-width: 0; flex: 1; }
    .da-doc-name { font-size: 13px; font-weight: 600; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; }
    .da-doc-time { font-size: 11.5px; color: #9ca3af; margin-top: 2px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .da-doc-actions { display: flex; gap: 6px; flex-shrink: 0; }

    /* ── Buttons ── */
    .da-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 10px; border: 1.5px solid #e5e7eb;
        border-radius: 7px; background: #fff; color: #374151;
        font-size: 12px; font-weight: 600; cursor: pointer;
        text-decoration: none; transition: all .15s; white-space: nowrap;
        font-family: 'Inter', sans-serif;
    }
    .da-btn svg { width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2; }
    .da-btn:hover { border-color: var(--accent); color: var(--accent); background: #fff5f0; }
    .da-btn-view { border-color: #3b82f6; color: #3b82f6; background: #eff6ff; }
    .da-btn-view:hover { border-color: #2563eb; color: #2563eb; background: #dbeafe; }
    .da-btn-del:hover { border-color: #ef4444; color: #ef4444; background: #fef2f2; }

    /* ── Storage box ── */
    .da-storage-box {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 10px 16px; margin: 12px 18px; display: flex;
        align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap;
    }
    .da-storage-left { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .da-storage-icon { width: 30px; height: 30px; border-radius: 7px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #475569; }
    .da-storage-icon svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
    .da-storage-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .da-storage-path { font-size: 12px; color: #334155; font-family: monospace; font-weight: 600; margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 380px; }

    /* ── Orphan section ── */
    .da-orphan-section { background: #fff; border: 1px dashed #d1d5db; border-radius: 12px; padding: 16px 18px; margin-top: 18px; }
    .da-orphan-title { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
    .da-orphan-title svg { width: 15px; height: 15px; stroke: #f59e0b; fill: none; stroke-width: 2; }

    /* ── Empty state ── */
    .da-empty { text-align: center; padding: 50px 20px; color: #9ca3af; }
    .da-empty svg { width: 48px; height: 48px; stroke: currentColor; fill: none; stroke-width: 1.4; margin: 0 auto 12px; display: block; }
    .da-empty-title { font-size: 15px; font-weight: 700; color: #374151; margin-bottom: 5px; }
    .da-empty-sub { font-size: 13px; }

    /* ── Header row ── */
    .da-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }

    @media (max-width: 768px) {
        .da-doc-item { padding-left: 18px; }
        .da-storage-box { margin: 10px 12px; padding: 8px 12px; }
    }
    @media (max-width: 640px) {
        .da-doc-item { padding-left: 12px; padding-right: 12px; }
        .da-card-sub { max-width: 160px; }
        .da-doc-name { max-width: 150px; }
        .da-card-header { padding: 11px 12px; }
        .da-tab-btn { font-size: 13px; padding: 14px 12px; }
        .da-storage-path { max-width: 160px; }
    }
</style>

<div class="animate-slide-up">

    {{-- ── Page Header ── --}}
    <div class="da-header-row">
        <div></div>
        @if($activeTab === 'support')
        <button class="btn btn-primary" onclick="window.location.href='{{ route('archives.create') }}'">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Buat Folder Arsip
        </button>
        @endif
    </div>

    {{-- Flash via toast --}}

    {{-- ── Tab Bar ── --}}
    <div class="da-tab-bar">
        <button type="button" class="da-tab-btn {{ $activeTab === 'support' ? 'active' : '' }}" onclick="switchTab('support')" id="btn-tab-support">
            <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            Dokumen Pendukung
            <span class="da-tab-count" id="tab-count-support">{{ $archives->total() }}</span>
        </button>
        <button type="button" class="da-tab-btn {{ $activeTab === 'finished' ? 'active' : '' }}" onclick="switchTab('finished')" id="btn-tab-finished">
            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Kasus Selesai
            <span class="da-tab-count" id="tab-count-finished">{{ $finishedCases->count() }}</span>
        </button>
    </div>

    {{-- ── Tab Body ── --}}
    <div class="da-tab-body">

        {{-- ════════ TAB: SUPPORT DOCUMENT ════════ --}}
        <div class="da-tab-panel {{ $activeTab === 'support' ? 'active' : '' }}" id="panel-support">

            {{-- Filter --}}
            <form method="GET" action="{{ route('archives.index') }}" class="da-filter-bar">
                <input type="hidden" name="tab" value="support">
                <div class="da-search-wrap">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search_support" value="{{ $searchSupport }}"
                        placeholder="Cari nama klien, ID arsip, lokasi folder..." autocomplete="off">
                </div>
                <button type="submit" class="btn btn-secondary" style="white-space:nowrap;">Cari</button>
                @if($searchSupport)
                    <a href="{{ route('archives.index', ['tab' => 'support']) }}" class="btn btn-secondary" style="white-space:nowrap;">Reset</a>
                @endif
                <div style="font-size:12px; color:#6b7280; white-space:nowrap;">
                    <span>{{ $archives->total() }}</span> folder
                </div>
            </form>

            @if($archives->isEmpty())
                <div class="da-empty">
                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <div class="da-empty-title">Belum ada folder arsip</div>
                    <div class="da-empty-sub">Buat folder arsip pertama menggunakan tombol di atas, lalu upload dokumen ke kasus terkait</div>
                </div>
            @else
                <div class="da-card-list" id="arc-folder-list">
                    @foreach($archives as $a)
                    @php
                        $docs = $a->case && $a->case->documents ? $a->case->documents : collect();
                        $persyaratanFields = [
                            'file_ktp'            => 'KTP',
                            'file_npwp'           => 'NPWP',
                            'file_kk'             => 'Kartu Keluarga',
                            'file_surat_tanah'    => 'Surat Tanah',
                            'file_surat_perintah' => 'Surat Perintah',
                            'file_buku_nikah'     => 'Buku Nikah',
                        ];
                        $persyaratanFiles = [];
                        if ($a->case) {
                            foreach ($persyaratanFields as $field => $label) {
                                if (!empty($a->case->$field)) {
                                    $persyaratanFiles[] = [
                                        'label'      => $label,
                                        'filename'   => $label . '.' . pathinfo($a->case->$field, PATHINFO_EXTENSION),
                                        'filepath'   => $a->case->$field,
                                        'created_at' => $a->case->created_at,
                                    ];
                                }
                            }
                        }
                        $docCount = $docs->count() + count($persyaratanFiles);
                    @endphp
                    <div class="da-card" id="folder-{{ $a->id_arsip }}">
                        <div class="da-card-header" onclick="toggleCard('folder-{{ $a->id_arsip }}','sup-docs-{{ $a->id_arsip }}')">
                            <div class="da-card-left">
                                <div class="da-folder-icon">
                                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                </div>
                                <div class="da-card-meta">
                                    <div class="da-card-name">{{ $a->client_name }}</div>
                                    <div class="da-card-sub" title="{{ $a->folder_location }}">
                                        <svg viewBox="0 0 24 24" style="width:10px;height:10px;stroke:#9ca3af;fill:none;stroke-width:2;display:inline;vertical-align:-1px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                        {{ $a->folder_location }}
                                    </div>
                                    <div class="da-card-badges">
                                        <span class="da-badge">ID: {{ $a->id_arsip }}</span>
                                        <span class="da-badge">Kasus: {{ $a->id_kasus }}</span>
                                        <span class="da-badge">{{ $docCount }} dokumen</span>
                                        <span class="da-badge" title="{{ $a->created_at }}">{{ $a->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="da-card-right">
                                <button class="da-btn" onclick="event.stopPropagation(); openEditFolderModal('{{ $a->id_arsip }}','{{ $a->id_kasus }}','{{ addslashes($a->folder_location) }}','{{ addslashes($a->client_name) }}')" title="Ubah folder">
                                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </button>
                                <form action="{{ route('archives.destroy', $a->id_arsip) }}" method="POST"
                                    onsubmit="event.preventDefault(); showConfirm('Hapus Folder Arsip','Yakin hapus folder <strong>{{ addslashes($a->id_arsip) }}</strong>? Dokumen tidak akan dihapus.',()=>this.submit(),'!')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="da-btn da-btn-del" title="Hapus folder">
                                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                    </button>
                                </form>
                                <svg class="da-chevron" viewBox="0 0 24 24" style="width:16px;height:16px;stroke:#9ca3af;fill:none;stroke-width:2;flex-shrink:0;">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </div>
                        </div>

                        <div class="da-expanded" id="sup-docs-{{ $a->id_arsip }}" style="display:none;">
                            <div class="da-doc-list">
                                @if(count($persyaratanFiles) === 0 && $docs->isEmpty())
                                    <div style="padding:16px 56px; font-size:13px; color:#9ca3af; display:flex; align-items:center; gap:8px;">
                                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#d1d5db;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Belum ada dokumen — upload dari halaman <strong style="color:#374151;margin:0 4px;">detail kasus</strong>.
                                    </div>
                                @else
                                    @foreach($persyaratanFiles as $pf)
                                    @php
                                        $pfExt = strtolower(pathinfo($pf['filepath'], PATHINFO_EXTENSION));
                                        $pfIsPdf  = $pfExt === 'pdf'; $pfIsImg = in_array($pfExt,['jpg','jpeg','png','webp','gif']); $pfIsWord = in_array($pfExt,['doc','docx']);
                                        $pfIcon   = $pfIsPdf ? 'pdf' : ($pfIsImg ? 'img' : ($pfIsWord ? 'word' : 'other'));
                                    @endphp
                                    <div class="da-doc-item">
                                        <div class="da-doc-left">
                                            <div class="da-doc-icon {{ $pfIcon }}">
                                                @if($pfIsPdf)<svg viewBox="0 0 24 24" style="stroke:#ef4444;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @elseif($pfIsImg)<svg viewBox="0 0 24 24" style="stroke:#3b82f6;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @else<svg viewBox="0 0 24 24" style="stroke:#6b7280;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>@endif
                                            </div>
                                            <div class="da-doc-meta">
                                                <div class="da-doc-name" title="{{ $pf['filename'] }}">{{ $pf['filename'] }}</div>
                                                <div class="da-doc-time">
                                                    <span style="background:#dbeafe;color:#1d4ed8;font-size:10px;font-weight:700;padding:1px 6px;border-radius:5px;text-transform:uppercase;">Persyaratan</span>
                                                    <span>{{ $pf['created_at'] ? $pf['created_at']->format('d/m/Y') : '—' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="da-doc-actions">
                                            <a href="{{ asset('storage/' . $pf['filepath']) }}" target="_blank" class="da-btn da-btn-view" title="Lihat">
                                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $pf['filepath']) }}" download="{{ $pf['filename'] }}" class="da-btn" title="Unduh">
                                                <svg viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach

                                    @foreach($docs as $doc)
                                    @php
                                        $ext = strtolower(pathinfo($doc->filename, PATHINFO_EXTENSION));
                                        $isPdf = $ext === 'pdf'; $isImg = in_array($ext,['jpg','jpeg','png','webp','gif']); $isWord = in_array($ext,['doc','docx']);
                                        $iconClass = $isPdf ? 'pdf' : ($isImg ? 'img' : ($isWord ? 'word' : 'other'));
                                        
                                        $canDelete = false;
                                        if (auth()->user()->role !== 'freelancer') {
                                            $canDelete = true;
                                        } elseif ($doc->case && $doc->case->created_by === auth()->id()) {
                                            $canDelete = true;
                                        }
                                    @endphp
                                    <div class="da-doc-item">
                                        <div class="da-doc-left">
                                            <div class="da-doc-icon {{ $iconClass }}">
                                                @if($isPdf)<svg viewBox="0 0 24 24" style="stroke:#ef4444;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @elseif($isImg)<svg viewBox="0 0 24 24" style="stroke:#3b82f6;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @elseif($isWord)<svg viewBox="0 0 24 24" style="stroke:#2563eb;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                                @else<svg viewBox="0 0 24 24" style="stroke:#6b7280;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>@endif
                                            </div>
                                            <div class="da-doc-meta">
                                                <div class="da-doc-name" title="{{ $doc->filename }}">{{ $doc->filename }}</div>
                                                <div class="da-doc-time">
                                                    <span style="background:#dcfce7;color:#166534;font-size:10px;font-weight:700;padding:1px 6px;border-radius:5px;text-transform:uppercase;">Dokumen Akhir</span>
                                                    <span>{{ $doc->created_at->format('d/m/Y H:i') }}</span>
                                                    @if($doc->uploader) <span style="color:#d1d5db;">·</span> <span>{{ $doc->uploader->name ?? $doc->uploaded_by }}</span> @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="da-doc-actions">
                                            <a href="{{ asset('storage/' . $doc->filepath) }}" target="_blank" class="da-btn da-btn-view" title="Lihat">
                                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $doc->filepath) }}" download="{{ $doc->filename }}" class="da-btn" title="Unduh">
                                                <svg viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                                            </a>
                                            @if($canDelete)
                                            <button type="button" class="da-btn da-btn-del" onclick="deleteDirectDocument('{{ $doc->id_dok }}')" title="Hapus Dokumen" style="color:#ef4444; border-color:#fee2e2; background:#fff1f1;">
                                                <svg viewBox="0 0 24 24" style="stroke:#ef4444; width:12px; height:12px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg> Hapus
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Direct upload container for Support Case --}}
                            @php
                                $canUpload = false;
                                if (auth()->user()->role !== 'freelancer') {
                                    $canUpload = true;
                                } elseif ($a->case && $a->case->created_by === auth()->id()) {
                                    $canUpload = true;
                                }
                            @endphp
                            @if($canUpload && $a->case)
                            <div style="padding: 12px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                                <button type="button" class="btn btn-primary" onclick="triggerDirectUpload('{{ $a->id_kasus }}', '{{ $a->id_arsip }}', false)" style="padding: 6px 16px; font-size: 12.5px; gap: 6px; height: 34px;">
                                    <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" /></svg>
                                    + Upload Dokumen
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($archives->hasPages())
                <div style="margin-top:18px;">{{ $archives->links() }}</div>
                @endif
            @endif

            {{-- Orphan Documents --}}
            @if($orphanDocs->isNotEmpty())
            <div class="da-orphan-section">
                <div class="da-orphan-title">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $orphanDocs->count() }} Dokumen Belum Diarsipkan
                    <span style="font-size:11.5px; font-weight:400; color:#9ca3af;">(belum terhubung ke folder arsip)</span>
                </div>
                @foreach($orphanDocs as $doc)
                @php $ext = strtolower(pathinfo($doc->filename, PATHINFO_EXTENSION)); $isPdf = $ext==='pdf'; $isImg=in_array($ext,['jpg','jpeg','png','webp','gif']); @endphp
                <div class="da-doc-item" style="padding-left:8px; border-bottom:1px solid #f3f4f6;">
                    <div class="da-doc-left">
                        <div class="da-doc-icon {{ $isPdf ? 'pdf' : ($isImg ? 'img' : 'other') }}" style="flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="stroke:{{ $isPdf ? '#ef4444' : ($isImg ? '#3b82f6' : '#6b7280') }};"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div>
                            <div class="da-doc-name">{{ $doc->filename }}</div>
                            <div class="da-doc-time">
                                Kasus: {{ $doc->id_kasus }}
                                @if($doc->case) &middot; {{ $doc->case->client_name }} @endif
                                &middot; {{ $doc->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="da-doc-actions">
                        <a href="{{ asset('storage/' . $doc->filepath) }}" target="_blank" class="da-btn da-btn-view">
                            <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>{{-- /panel-support --}}


        {{-- ════════ TAB: FINISHED CASES ════════ --}}
        <div class="da-tab-panel {{ $activeTab === 'finished' ? 'active' : '' }}" id="panel-finished">

            {{-- Filter --}}
            <form method="GET" action="{{ route('archives.index') }}" class="da-filter-bar">
                <input type="hidden" name="tab" value="finished">
                <div class="da-search-wrap">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search_finished" value="{{ $searchFinished }}"
                        placeholder="Cari nama klien, nama kasus, atau ID kasus..." autocomplete="off">
                </div>
                <button type="submit" class="btn btn-secondary" style="white-space:nowrap;">Cari</button>
                @if($searchFinished)
                    <a href="{{ route('archives.index', ['tab' => 'finished']) }}" class="btn btn-secondary" style="white-space:nowrap;">Reset</a>
                @endif
                <div style="font-size:12px; color:#6b7280; white-space:nowrap;">
                    Total: <strong style="color:#111827;">{{ $finishedCases->count() }}</strong> kasus selesai
                </div>
            </form>

            @if($finishedCases->isEmpty())
                <div class="da-empty">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/></svg>
                    <div class="da-empty-title">Tidak ada kasus selesai</div>
                    <div class="da-empty-sub">Belum ada kasus berstatus selesai atau tidak ada hasil yang cocok.</div>
                </div>
            @else
                <div class="da-card-list">
                    @foreach($finishedCases as $case)
                    @php
                        $fcFiles = [];
                        if (!empty($case->file_selesai)) {
                            $fcFiles[] = [
                                'id'         => null,
                                'label'      => 'Dokumen Selesai',
                                'filename'   => basename($case->file_selesai),
                                'filepath'   => $case->file_selesai,
                                'type'       => 'Selesai',
                                'created_at' => $case->updated_at ?: $case->created_at,
                                'can_delete' => false
                            ];
                        }
                    @endphp
                    <div class="da-card" id="fc-card-{{ $case->id_kasus }}">
                        <div class="da-card-header" onclick="toggleCard('fc-card-{{ $case->id_kasus }}','fc-docs-{{ $case->id_kasus }}')">
                            <div class="da-card-left">
                                <div class="da-folder-icon finished">
                                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                </div>
                                <div class="da-card-meta">
                                    <div class="da-card-name">{{ $case->client_name }}</div>
                                    <div class="da-card-sub">{{ $case->case_name }}</div>
                                    <div class="da-card-badges">
                                        <span class="da-badge">ID: {{ $case->id_kasus }}</span>
                                        <span class="da-badge da-badge-blue">{{ $case->type }}</span>
                                        <span class="da-badge">{{ count($fcFiles) }} dokumen</span>
                                        <span class="da-badge da-badge-green">✓ SELESAI</span>
                                    </div>
                                </div>
                            </div>
                            <div class="da-card-right">
                                <svg class="da-chevron" viewBox="0 0 24 24" style="width:16px;height:16px;stroke:#9ca3af;fill:none;stroke-width:2;flex-shrink:0;">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </div>
                        </div>

                        <div class="da-expanded" id="fc-docs-{{ $case->id_kasus }}" style="display:none;">

                            {{-- Storage box --}}
                            <div class="da-storage-box">
                                <div class="da-storage-left">
                                    <div class="da-storage-icon">
                                        @if($case->is_physical)
                                            <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                        @else
                                            <svg viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="da-storage-label">Path Penyimpanan {{ $case->is_physical ? '(Fisik)' : '(Server)' }}</div>
                                        <div class="da-storage-path" title="{{ $case->folder_location }}">{{ $case->folder_location }}</div>
                                    </div>
                                </div>
                                @if(!$case->is_physical && (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1' || env('APP_ENV') === 'local'))
                                <button class="da-btn" onclick="event.stopPropagation(); openCaseFolder('{{ $case->id_kasus }}')">
                                    <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                    Buka Folder
                                </button>
                                @endif
                            </div>

                            <div class="da-doc-list">
                                @if(count($fcFiles) === 0)
                                    <div style="padding:14px 56px; font-size:13px; color:#9ca3af; display:flex; align-items:center; gap:8px;">
                                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#d1d5db;fill:none;stroke-width:2;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        Belum ada dokumen yang diunggah untuk kasus ini.
                                    </div>
                                @else
                                    @foreach($fcFiles as $f)
                                    @php
                                        $ext = strtolower(pathinfo($f['filename'], PATHINFO_EXTENSION));
                                        $isPdf = $ext==='pdf'; $isImg = in_array($ext,['jpg','jpeg','png','webp','gif']); $isWord = in_array($ext,['doc','docx']);
                                        $iconClass = $isPdf ? 'pdf' : ($isImg ? 'img' : ($isWord ? 'word' : 'other'));
                                    @endphp
                                    <div class="da-doc-item">
                                        <div class="da-doc-left">
                                            <div class="da-doc-icon {{ $iconClass }}">
                                                @if($isPdf)<svg viewBox="0 0 24 24" style="stroke:#ef4444;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @elseif($isImg)<svg viewBox="0 0 24 24" style="stroke:#3b82f6;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @elseif($isWord)<svg viewBox="0 0 24 24" style="stroke:#2563eb;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @else<svg viewBox="0 0 24 24" style="stroke:#6b7280;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>@endif
                                            </div>
                                            <div class="da-doc-meta">
                                                <div class="da-doc-name" title="{{ $f['filename'] }}">{{ $f['filename'] }}</div>
                                                <div class="da-doc-time">
                                                    <span style="background:{{ $f['type']==='Persyaratan'?'#dbeafe':'#dcfce7' }}; color:{{ $f['type']==='Persyaratan'?'#1d4ed8':'#166534' }}; font-size:10px; font-weight:700; padding:1px 6px; border-radius:5px; text-transform:uppercase;">{{ $f['label'] }}</span>
                                                    <span>{{ $f['created_at'] ? $f['created_at']->format('d/m/Y') : '—' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="da-doc-actions">
                                            <a href="{{ asset('storage/' . $f['filepath']) }}" target="_blank" class="da-btn da-btn-view" title="Lihat">
                                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Lihat
                                            </a>
                                            <a href="{{ asset('storage/' . $f['filepath']) }}" download="{{ $f['filename'] }}" class="da-btn" title="Unduh">
                                                <svg viewBox="0 0 24 24"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                                            </a>
                                            @if($f['can_delete'])
                                            <button type="button" class="da-btn da-btn-del" onclick="deleteDirectDocument('{{ $f['id'] }}')" title="Hapus Dokumen" style="color:#ef4444; border-color:#fee2e2; background:#fff1f1;">
                                                <svg viewBox="0 0 24 24" style="stroke:#ef4444; width:12px; height:12px;"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg> Hapus
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Direct upload container for Finished Case --}}
                            @php
                                $canUploadFinished = false;
                                if (auth()->user()->role !== 'freelancer') {
                                    $canUploadFinished = true;
                                } elseif ($case->created_by === auth()->id()) {
                                    $canUploadFinished = true;
                                }
                            @endphp
                            @if($canUploadFinished)
                            <div style="padding: 12px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end;">
                                <button type="button" class="btn btn-primary" onclick="triggerDirectUpload('{{ $case->id_kasus }}', null, true)" style="padding: 6px 16px; font-size: 12.5px; gap: 6px; height: 34px;">
                                    <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" /></svg>
                                    + Upload Dokumen
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

        </div>{{-- /panel-finished --}}

    </div>{{-- /.da-tab-body --}}

</div>

{{-- ── Edit Folder Modal ── --}}
@push('modals')
<div id="arc-edit-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:480px;">
        <div class="modal-header">
            <span class="modal-title">Ubah Lokasi Folder</span>
            <button class="modal-close" onclick="document.getElementById('arc-edit-modal').classList.remove('open')">×</button>
        </div>
        <form id="arc-edit-form" method="POST">
            @csrf @method('PUT')
            <div style="padding:24px; display:flex; flex-direction:column; gap:16px;">
                <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px;">
                    <div id="arc-modal-avatar" style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent-hover));display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:#fff;flex-shrink:0;"></div>
                    <div>
                        <div id="arc-modal-name" style="font-weight:600;color:#111827;font-size:14px;"></div>
                        <div style="display:flex;gap:8px;margin-top:4px;flex-wrap:wrap;">
                            <span style="font-size:11.5px;color:#6b7280;">ID Arsip: <strong id="arc-modal-id" style="font-family:monospace;color:#374151;"></strong></span>
                            <span style="font-size:11.5px;color:#6b7280;">ID Kasus: <strong id="arc-modal-kasus" style="font-family:monospace;color:#374151;"></strong></span>
                        </div>
                    </div>
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                        Lokasi Folder <span style="color:var(--accent);">*</span>
                    </label>
                    <input type="text" name="folder_location" id="arc-edit-folder" required
                        placeholder="Contoh: /arsip/2025/kasus-CS0001" style="width:100%;">
                </div>
                <input type="hidden" name="id_kasus" id="arc-edit-id-kasus">
                <input type="hidden" name="client_name" id="arc-edit-client-name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('arc-edit-modal').classList.remove('open')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
// Switch between tabs (client-side only for instant response, then updates URL)
function switchTab(name) {
    document.querySelectorAll('.da-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.da-tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('btn-tab-' + name).classList.add('active');
    document.getElementById('panel-' + name).classList.add('active');

    // Show/hide the 'Buat Folder Arsip' button
    var createBtn = document.querySelector('.da-header-row .btn-primary');
    if (createBtn) createBtn.style.display = name === 'support' ? '' : 'none';

    // Update URL without full reload
    var url = new URL(window.location.href);
    url.searchParams.set('tab', name);
    history.replaceState({}, '', url.toString());
}

function toggleCard(cardId, docsId) {
    var card = document.getElementById(cardId);
    var docs = document.getElementById(docsId);
    if (!card || !docs) return;
    var isOpen = card.classList.toggle('open');
    docs.style.display = isOpen ? '' : 'none';
}

function openEditFolderModal(id, idKasus, location, client) {
    document.getElementById('arc-edit-form').action = '{{ url("/archives") }}/' + id;
    document.getElementById('arc-modal-name').textContent   = client;
    document.getElementById('arc-modal-id').textContent     = id;
    document.getElementById('arc-modal-kasus').textContent  = idKasus;
    document.getElementById('arc-modal-avatar').textContent = client.charAt(0).toUpperCase();
    document.getElementById('arc-edit-folder').value        = location;
    document.getElementById('arc-edit-id-kasus').value      = idKasus;
    document.getElementById('arc-edit-client-name').value   = client;
    document.getElementById('arc-edit-modal').classList.add('open');
    setTimeout(function() { document.getElementById('arc-edit-folder').focus(); }, 120);
}

function openCaseFolder(id) {
    showToast('Membuka folder explorer...', 'info');
    fetch('/finished-cases/' + id + '/open-folder', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) showToast(res.message, 'success');
        else showToast(res.message || 'Gagal membuka folder.', 'danger');
    })
    .catch(() => showToast('Gagal menghubungi server.', 'danger'));
}

function triggerDirectUpload(caseId, archiveId, isFinished) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf,.jpg,.jpeg,.png,.doc,.docx';
    input.onchange = function(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('document', file);
        
        if (typeof showToast === 'function') {
            showToast('Mengunggah dokumen...', 'info');
        }
        
        fetch(`/cases/${caseId}/documents`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Dokumen berhasil diunggah dari penyimpanan perangkat!', 'success');
                }
                setTimeout(() => window.location.reload(), 800);
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Gagal mengunggah dokumen.', 'danger');
                } else {
                    alert(data.message || 'Gagal mengunggah dokumen.');
                }
            }
        })
        .catch(err => {
            if (typeof showToast === 'function') {
                showToast('Terjadi kesalahan saat mengunggah.', 'danger');
            } else {
                alert('Terjadi kesalahan saat mengunggah.');
            }
        });
    };
    input.click();
}

function deleteDirectDocument(docId) {
    const action = () => {
        if (typeof showToast === 'function') {
            showToast('Menghapus dokumen...', 'info');
        }
        fetch(`/cases/documents/${docId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast('Dokumen berhasil dihapus!', 'success');
                }
                setTimeout(() => window.location.reload(), 800);
            } else {
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Gagal menghapus dokumen.', 'danger');
                } else {
                    alert(data.message || 'Gagal menghapus dokumen.');
                }
            }
        })
        .catch(err => {
            if (typeof showToast === 'function') {
                showToast('Terjadi kesalahan saat menghapus.', 'danger');
            } else {
                alert('Terjadi kesalahan saat menghapus.');
            }
        });
    };

    if (typeof showConfirm === 'function') {
        showConfirm('Hapus Dokumen', 'Apakah Anda yakin ingin menghapus dokumen ini?', action);
    } else if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
        action();
    }
}
</script>
@endpush

@endsection
