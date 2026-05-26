@extends('layout')
@section('html-class', 'is-chat-shell')
@section('page-title', 'Chat')
@php
    $chatConversationOpen = $receiverId || request()->query('global');
    $chatBodyClass = 'chat-page ' . ($chatConversationOpen ? 'wa-show-chat' : 'wa-show-list');
    if ($receiverId) {
        $chatBodyClass .= ' chat-dm';
    }
@endphp
@section('body-class', $chatBodyClass)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chat-whatsapp.css') }}?v={{ filemtime(public_path('css/chat-whatsapp.css')) }}">
    <style>
        /* ── Chat Page wrapper takes full remaining height ── */
        .chat-page-wrap {
            display: flex;
            margin: -12px;
            height: calc(100vh - 100px); /* Fallback */
            height: calc(100dvh - 100px);
            background: #f0f2f5;
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
            position: relative;
            isolation: isolate;
        }

        .chat-sidebar {
            width: 320px;
            max-width: min(320px, 92vw);
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 0;
            overflow: hidden;
        }

        #chat-contact-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
        }

        .chat-sidebar-header {
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            background: rgba(255, 255, 255, 0.9);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .user-list-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 20px;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
            transition: background 0.15s;
            text-decoration: none;
            color: inherit;
            position: relative;
        }

        .user-list-item:hover {
            background: #f3f4f6;
        }

        .user-list-item.active {
            background: linear-gradient(90deg, rgba(204, 51, 0, 0.16), rgba(204, 51, 0, 0.05));
            box-shadow: inset 4px 0 0 0 var(--accent), inset 0 0 0 1px rgba(204, 51, 0, 0.18);
        }

        .user-list-item.active .user-list-name {
            color: #111827;
            font-weight: 700;
        }

        .user-list-item.active .user-list-role {
            color: #374151;
            font-weight: 600;
        }

        .user-list-item.is-filtered-hidden {
            display: none !important;
        }

        .chat-unread-dot {
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 10px;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px #fff;
            line-height: 1;
        }
        .chat-unread-dot[hidden] {
            display: none !important;
        }

        .user-list-item.has-unread .user-list-name {
            font-weight: 700;
        }

        .user-list-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #d1d5db;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px #fff, 0 0 0 3px rgba(0,0,0,0.06);
        }

        .user-list-info {
            flex: 1;
            min-width: 0;
        }

        .user-list-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-list-role {
            font-size: 11.5px;
            color: #6b7280;
            margin-top: 2px;
            text-transform: capitalize;
        }

        .chat-main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
            background: #f0f2f5;
        }

        /* ── Top bar inside chat ── */
        .chat-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
        }

        .chat-topbar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-topbar-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-topbar-icon svg {
            width: 18px;
            height: 18px;
            fill: #fff;
            flex-shrink: 0;
            display: block;
        }

        .chat-topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .chat-topbar-sub {
            font-size: 11.5px;
            color: #6b7280;
            margin-top: 1px;
        }

        .online-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            display: inline-block;
            margin-right: 4px;
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25);
        }

        /* ── Messages area ── */
        .chat-msgs-area {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
            scroll-behavior: smooth;
        }

        .chat-msgs-area::-webkit-scrollbar {
            width: 5px;
        }

        .chat-msgs-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-msgs-area::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        /* ── Date divider ── */
        .date-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 12px 0;
            color: #9ca3af;
            font-size: 11px;
        }

        .date-divider::before,
        .date-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        /* ── Message row ── */
        .msg-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            max-width: 72%;
            overflow: visible;
            animation: msgIn 0.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        @keyframes msgIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg-row.mine {
            margin-left: auto;
            flex-direction: row-reverse;
        }

        .msg-row.system-row {
            max-width: 90%;
            margin: 4px auto;
        }

        /* ── Avatar ── */
        .msg-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 0;
        }

        /* ── Bubble group ── */
        .msg-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .msg-row.mine .msg-group {
            align-items: flex-end;
        }

        .msg-name {
            font-size: 10.5px;
            color: #6b7280;
            font-weight: 600;
            padding: 0 2px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .role-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ── Bubble ── */
        .msg-bubble {
            padding: 9px 13px;
            font-size: 13.5px;
            line-height: 1.55;
            word-break: break-word;
            overflow-wrap: anywhere;
            position: relative;
            max-width: 100%;
            overflow: visible;
            overflow-x: visible;
        }

        /* theirs */
        .msg-bubble.theirs {
            background: #fff;
            border: 1px solid #e5e7eb;
            color: #111827;
            border-radius: 16px 16px 16px 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        /* mine */
        .msg-bubble.mine {
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));

            color: #fff;
            border-radius: 16px 16px 4px 16px;
            box-shadow: 0 2px 8px rgba(204, 51, 0, 0.3);
        }

        /* system / approval */
        .msg-bubble.system {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            border-radius: 10px;
            font-size: 12.5px;
            text-align: center;
            padding: 8px 16px;
            width: 100%;
        }

        /* request card */
        .request-card {
            background: #fff;
            border: 1.5px solid #fed7aa;
            border-left: 4px solid #f97316;
            border-radius: 12px;
            padding: 14px 16px;
            min-width: 240px;
        }

        .request-card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .request-card-header .req-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fff7ed;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .request-card-header .req-label {
            font-weight: 700;
            font-size: 13px;
            color: #92400e;
        }

        .request-card-header .req-sub {
            font-size: 11.5px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .request-card-user {
            background: #fafafa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .request-card-user .rcu-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #92400e;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .request-card-user .rcu-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #111827;
        }

        .request-card-user .rcu-username {
            font-size: 11px;
            color: #6b7280;
        }

        .req-approved {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #15803d;
            padding: 4px 0;
        }

        /* ── Timestamp ── */
        .msg-ts {
            font-size: 10px;
            color: #9ca3af;
            padding: 0 2px;
            margin-top: 2px;
        }

        .msg-row.mine .msg-ts {
            text-align: right;
        }

        /* ── Input area ── */
        .chat-input-wrap {
            background: #fff;
            border-top: 1px solid #e5e7eb;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .chat-txt-input {
            flex: 1;
            padding: 11px 18px;
            border: 1.5px solid #e5e7eb;
            border-radius: 24px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: #f9fafb;
            color: #111827;
        }

        .chat-txt-input:focus {
            border-color: var(--accent);

            box-shadow: 0 0 0 3px rgba(204, 51, 0, 0.08);
            background: #fff;
        }

        .chat-txt-input::placeholder {
            color: #9ca3af;
        }

        .chat-send-btn {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--accent-hover));

            border: none;
            border-radius: 50%;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(204, 51, 0, 0.35);
            transition: transform 0.15s, box-shadow 0.15s;
        }

        .chat-send-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 14px rgba(204, 51, 0, 0.45);
        }

        .chat-send-btn svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #fff;
            stroke-width: 2.5;
            pointer-events: none;
        }

        /* Typing dots */
        .typing-dots {
            display: flex;
            gap: 3px;
            padding: 11px 14px;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #9ca3af;
            animation: typBounce 1.2s infinite;
        }

        .typing-dots span:nth-child(2) {
            animation-delay: 0.15s;
        }

        .typing-dots span:nth-child(3) {
            animation-delay: 0.3s;
        }

        @keyframes typBounce {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
        }

        /* ── Mobile responsive (sidebar open/close lives in app.css — avoid conflicting rules) ── */
        @media (max-width: 768px) {
            .chat-page-wrap {
                margin: 0;
                border-radius: 0;
                border: none;
                height: calc(100dvh - 58px - 72px - env(safe-area-inset-bottom, 0px));
                min-height: 320px;
            }
            .chat-topbar {
                padding: 8px 10px;
                min-height: 48px;
                height: auto;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }
            .chat-topbar-info {
                gap: 8px;
                min-width: 0;
                flex: 1;
            }
            .chat-topbar-title {
                font-size: 13px;
                line-height: 1.25;
                word-break: break-word;
            }
            .chat-topbar-sub {
                font-size: 10.5px;
            }
            .chat-msgs-area {
                padding: 10px 12px;
            }
            .msg-row {
                max-width: min(92%, calc(100vw - 24px));
            }
            .msg-bubble {
                padding: 8px 12px;
                font-size: 12.5px;
            }
            .chat-input-wrap {
                padding: 8px 10px;
                gap: 8px;
            }
            .chat-txt-input {
                min-width: 0;
                font-size: 16px;
            }
            .chat-send-btn {
                width: 40px;
                height: 40px;
                flex-shrink: 0;
            }
            .user-list-item {
                padding: 12px 14px;
                min-height: 48px;
            }
            .request-card {
                min-width: 0;
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="chat-page-wrap glass-card animate-slide-up">

        {{-- Sidebar for Users --}}
        <div class="chat-sidebar">
            <div class="wa-list-header">
                <h2>Chat</h2>
                <div class="wa-header-actions">
                    <button type="button" class="wa-icon-btn" onclick="openNewChatModal()" title="Chat baru" aria-label="Chat baru">
                        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    </button>
                </div>
            </div>
            <div class="wa-search-wrap">
                <div class="wa-search-inner">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="search" id="chat-contact-search" placeholder="Cari kontak..." autocomplete="off"
                        data-list-search="#chat-contact-list .user-list-item"
                        aria-label="Cari kontak">
                </div>
            </div>
            <div class="chat-sidebar-header" style="display:flex; align-items:center; justify-content:space-between;">
                <span>Kontak Chat</span>
                <button class="btn btn-primary" style="padding:4px 8px; font-size:11px;" onclick="openNewChatModal()">+ Baru</button>
            </div>
            <div id="chat-contact-list">
            <a href="{{ route('chat.index', ['global' => 1]) }}" class="user-list-item {{ !$receiverId && request()->query('global') ? 'active' : '' }}" data-search="grup global semua anggota" data-contact-key="global">
                <div class="user-list-avatar" style="background: linear-gradient(135deg, var(--accent), var(--accent-hover));">
                    <svg viewBox="0 0 24 24" style="width:18px;height:18px;fill:#fff;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="user-list-info" style="display:flex; align-items:center; justify-content:space-between; width:100%; gap: 12px;">
                    <div style="flex: 1; min-width: 0;">
                        <div class="user-list-name" style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px;">Grup Global</div>
                        <div class="user-list-role" style="font-size: 12px; color: #64748b; margin-top: 2px;">Semua Anggota</div>
                    </div>
                    <span class="chat-unread-dot" hidden aria-hidden="true" style="margin-left: auto;"></span>
                </div>
            </a>
            @php
                $roleColors = [
                    'admin' => 'var(--accent)',
                    'notaris' => '#3b82f6',
                    'staff' => '#0ea5e9',
                    'freelancer' => '#9333ea',
                    'klien' => '#f59e0b',
                ];
            @endphp
            @foreach($users as $u)
                @php
                    $isActive = $receiverId == $u->id;
                    $clr = $roleColors[$u->role] ?? '#d1d5db';
                    $init = mb_strtoupper(mb_substr($u->name, 0, 1));
                @endphp
                <a href="{{ route('chat.index', ['user_id' => $u->id]) }}" class="user-list-item {{ $isActive ? 'active' : '' }}" data-search="{{ strtolower($u->name . ' ' . $u->role . ' ' . $u->username) }}" data-contact-key="{{ $u->id }}">
                    <div class="user-list-avatar" style="background: {{ $clr }};">{{ $init }}</div>
                    <div class="user-list-info" style="display:flex; align-items:center; justify-content:space-between; width:100%; gap: 12px;">
                        <div style="flex: 1; min-width: 0;">
                            <div class="user-list-name" style="font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 14px;">{{ $u->name }}</div>
                            <div class="user-list-role" style="font-size: 12px; color: #64748b; margin-top: 2px; text-transform: capitalize;">{{ $u->role }}</div>
                        </div>
                        <span class="chat-unread-dot" hidden aria-hidden="true" style="margin-left: auto;"></span>
                    </div>
                </a>
            @endforeach
            </div>
        </div>

        {{-- Main Chat Area --}}
        <div class="chat-main-area">

            {{-- WhatsApp-style mobile conversation header --}}
            <div class="wa-chat-header">
                <button type="button" class="wa-back-btn" onclick="goChatList()" aria-label="Kembali">
                    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <div class="wa-chat-peer">
                    @if($activeReceiver)
                        @php $waClr = $roleColors[$activeReceiver->role] ?? '#3b82f6'; @endphp
                        <div class="wa-peer-avatar" style="background:{{ $waClr }};">{{ mb_strtoupper(mb_substr($activeReceiver->name, 0, 1)) }}</div>
                        <div>
                            <div class="wa-peer-name">{{ $activeReceiver->name }}</div>
                            <div class="wa-peer-status">{{ $activeReceiver->role }}</div>
                        </div>
                    @else
                        <div class="wa-peer-avatar" style="background:var(--accent);">
                            <svg viewBox="0 0 24 24" style="width:20px;height:20px;fill:#fff;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div>
                            <div class="wa-peer-name">Grup Global</div>
                            <div class="wa-peer-status">Semua anggota</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top info bar (desktop) --}}
            <div class="chat-topbar" style="background: rgba(255,255,255,0.4); backdrop-filter: none; border-bottom: 1px solid rgba(0,0,0,0.05);">
                <div class="chat-topbar-info">
                    @if($activeReceiver)
                        <div class="chat-topbar-icon" style="background: {{ $roleColors[$activeReceiver->role] ?? '#3b82f6' }};">
                            <span style="font-weight:700; font-size:16px;">{{ mb_strtoupper(mb_substr($activeReceiver->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="chat-topbar-title">{{ $activeReceiver->name }}</div>
                            <div class="chat-topbar-sub" style="text-transform: capitalize;">{{ $activeReceiver->role }}</div>
                        </div>
                    @else
                        <div class="chat-topbar-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="chat-topbar-title">Chat HUGO Assistant</div>
                            <div class="chat-topbar-sub"><span class="online-dot"></span>Live — semua anggota dapat melihat</div>
                        </div>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600;">{{ $messages->count() }} pesan</div>
                    {{-- Mobile: toggle chat contacts sidebar --}}
                    <button id="chat-sidebar-toggle-btn"
                        class="chat-sidebar-toggle"
                        style="display:none; align-items:center; gap:6px; padding:6px 12px; background:#f3f4f6; border:none; border-radius:8px; font-size:12px; font-weight:600; color:#374151; cursor:pointer;"
                        onclick="toggleChatSidebar()">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Kontak
                    </button>
                </div>
            </div>

        {{-- Messages --}}
        <div class="chat-msgs-area" id="chat-msgs-area" style="background: transparent;">

            <div class="date-divider">Hari ini</div>

            @foreach ($messages as $msg)
                @php
                    $isMe = $msg->sender_id === auth()->id();
                    $isAd = in_array(auth()->user()->role, ['admin', 'notaris']);
                    $roleColors = [
                        'admin' => 'var(--accent)',

                        'notaris' => '#3b82f6',
                        'staff' => '#0ea5e9',
                        'freelancer' => '#9333ea',
                        'klien' => '#f59e0b',
                    ];
                    $clr = $roleColors[$msg->sender->role] ?? '#374151';
                    $init = mb_strtoupper(mb_substr($msg->sender->name, 0, 1));
                    $roleLabels = [
                        'admin' => 'Admin',
                        'notaris' => 'Notaris',
                        'staff' => 'Staff',
                        'freelancer' => 'Freelancer',
                        'klien' => 'Klien',
                    ];
                    $roleLabel = $roleLabels[$msg->sender->role] ?? $msg->sender->role;
                @endphp

                @if ($msg->type === 'system' || $msg->type === 'approval')
                    <div class="msg-row system-row">
                        <div class="msg-bubble system">{{ $msg->message }}</div>
                    </div>
                @elseif ($msg->type === 'request')
                    <div class="msg-row {{ $isMe ? 'mine' : '' }}" style="max-width:320px;" data-id="{{ $msg->id }}">
                        @if (!$isMe)
                            <div class="msg-avatar" style="background:{{ $clr }};">{{ $init }}</div>
                        @endif
                        <div class="msg-group">
                            @if (!$isMe)
                                <div class="msg-name">
                                    {{ $msg->sender->name }}
                                    <span class="role-badge"
                                        style="background:{{ $clr }}22; color:{{ $clr }};">{{ $roleLabel }}</span>
                                </div>
                            @endif
                            <div class="request-card" id="req-card-{{ $msg->id }}">
                                <div class="request-card-header">
                                    <div class="req-icon">🔐</div>
                                    <div>
                                        <div class="req-label">Permintaan Reset Sandi</div>
                                        <div class="req-sub">{{ $msg->created_at->format('H:i') }}</div>
                                    </div>
                                </div>
                                <div class="request-card-user">
                                    <div class="rcu-avatar">{{ mb_strtoupper(mb_substr($msg->meta['name'] ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="rcu-name">{{ $msg->meta['name'] ?? '?' }}</div>
                                        <div class="rcu-username">{{ $msg->meta['username'] ?? '?' }}</div>
                                    </div>
                                </div>
                                @if ($msg->is_approved)
                                    <div class="req-approved">✅ Sudah disetujui admin</div>
                                @elseif ($isAd && !$isMe)
                                    <button class="btn btn-success"
                                        style="width:100%;justify-content:center;font-size:12.5px;padding:8px;"
                                        onclick="approveReset({{ $msg->id }}, this)">
                                        ✔ Setujui &amp; Reset Sandi
                                    </button>
                                @else
                                    <div style="font-size:12px;color:#b45309;display:flex;align-items:center;gap:6px;">
                                        <span>⏳</span> Menunggu persetujuan admin...
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="msg-row {{ $isMe ? 'mine' : '' }}" data-id="{{ $msg->id }}">
                        @if (!$isMe)
                            <div class="msg-avatar" style="background:{{ $clr }};">{{ $init }}</div>
                        @endif
                        <div class="msg-group">
                            @if (!$isMe)
                                <div class="msg-name">
                                    {{ $msg->sender->name }}
                                    <span class="role-badge"
                                        style="background:{{ $clr }}22; color:{{ $clr }};">{{ $roleLabel }}</span>
                                </div>
                            @endif
                            <div class="msg-bubble {{ $isMe ? 'mine' : 'theirs' }} premium-chat-bubble">{{ $msg->message }}</div>
                            <div class="msg-ts">{{ $msg->created_at->format('H:i') }}</div>
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Typing indicator (hidden) --}}
            <div class="msg-row" id="typing-row" style="display:none;">
                <div class="msg-avatar" style="background:#6b7280;">…</div>
                <div class="msg-group">
                    <div class="msg-bubble theirs" style="padding:0;">
                        <div class="typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            {{-- Input --}}
            <div class="chat-input-wrap">
                <input type="text" class="chat-txt-input" id="chat-page-input"
                    placeholder="Tulis pesan..." autocomplete="off"
                    onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); sendPageChat(); }">
                <button class="chat-send-btn" onclick="sendPageChat()" title="Kirim">
                    <svg viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13" />
                        <polygon points="22 2 15 22 11 13 2 9 22 2" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- New Chat Modal -->
    <div id="new-chat-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:400px; background:#fff;">
            <div class="modal-header">
                <span class="modal-title" style="font-size:16px;">Mulai Chat Baru</span>
                <button class="modal-close" onclick="document.getElementById('new-chat-modal').classList.remove('open')">×</button>
            </div>
            <div style="padding:16px;">
                <input type="search" id="new-chat-search" placeholder="Cari nama pengguna..." autocomplete="off"
                    data-list-search="#new-chat-list .user-list-item"
                    style="width:100%; padding:10px 14px; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:12px; outline:none; font-family:'Inter', sans-serif;"
                    aria-label="Cari pengguna">
                <div id="new-chat-list" style="max-height:min(320px,52vh); overflow-y:auto; margin:-16px; padding:16px; display:flex; flex-direction:column; gap:6px;-webkit-overflow-scrolling:touch;">
                    @foreach($users as $u)
                        <a href="{{ route('chat.index', ['user_id' => $u->id]) }}" class="user-list-item" style="border-radius:8px; border:none;" data-search="{{ strtolower($u->name . ' ' . $u->role . ' ' . $u->username) }}">
                            <div class="user-list-avatar" style="background: {{ $roleColors[$u->role] ?? '#d1d5db' }};">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</div>
                            <div class="user-list-info">
                                <div class="user-list-name">{{ $u->name }}</div>
                                <div class="user-list-role" style="text-transform: capitalize;">{{ $u->role }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
        window.HUGO_CHAT = {
            userId: @json(auth()->id()),
            receiverId: @json($receiverId),
            isGlobalChat: {{ request()->query('global') ? 'true' : 'false' }},
            lastMsgId: {{ $messages->isNotEmpty() ? (int) $messages->last()->id : 0 }},
            listUrl: @json(route('chat.index'))
        };
        var myUserId = window.HUGO_CHAT.userId;
        var isAdmin = {{ in_array(auth()->user()->role, ['admin', 'notaris']) ? 'true' : 'false' }};
        var receiverId = window.HUGO_CHAT.receiverId;
        var isGlobalChat = window.HUGO_CHAT.isGlobalChat;
        var lastMsgId = window.HUGO_CHAT.lastMsgId;
        window.lastMsgId = lastMsgId;

        var roleColors = {
            admin: 'var(--accent)',

            notaris: '#3b82f6',
            staff: '#0ea5e9',
            freelancer: '#9333ea',
            klien: '#f59e0b'
        };
        var roleLabels = {
            admin: 'Admin',
            notaris: 'Notaris',
            staff: 'Staff',
            freelancer: 'Freelancer',
            klien: 'Klien'
        };

        function esc(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // ── Mobile: chat sidebar toggle ──────────────────────────────────────
        function toggleChatSidebar() {
            var sb = document.querySelector('.chat-sidebar');
            var wrap = document.querySelector('.chat-page-wrap');
            var btn = document.getElementById('chat-sidebar-toggle-btn');
            if (!sb || !wrap) return;
            var isOpen = sb.classList.toggle('open');
            var kontakBtn = '<svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> Kontak';
            if (btn) btn.innerHTML = isOpen ? '✕ Tutup' : kontakBtn;

            var ov = document.getElementById('chat-mob-overlay');
            if (isOpen) {
                if (!ov) {
                    ov = document.createElement('div');
                    ov.id = 'chat-mob-overlay';
                    ov.setAttribute('aria-hidden', 'true');
                    ov.onclick = function () {
                        sb.classList.remove('open');
                        ov.remove();
                        if (btn) btn.innerHTML = kontakBtn;
                    };
                    /* Inside .chat-page-wrap so z-index stacks above .content-area (10); body overlay was blocking the drawer */
                    wrap.appendChild(ov);
                }
            } else if (ov) {
                ov.remove();
            }
        }

        function ucf(s) {
            return s ? s.charAt(0).toUpperCase() + s.slice(1) : '';
        }

        function scrollBottom(force) {
            if (window.HugoChatUi) HugoChatUi.scrollBottom(force);
        }

        function renderMsg(msg) {
            const isMe = msg.sender.id === myUserId;
            const clr = roleColors[msg.sender.role] || '#374151';
            const init = esc(msg.sender.name).charAt(0).toUpperCase();
            const rLabel = roleLabels[msg.sender.role] || ucf(msg.sender.role);

            if (msg.type === 'system' || msg.type === 'approval') {
                const isBirthday = msg.message.includes('🎂') || (msg.meta && msg.meta.birthday);
                if (isBirthday) {
                    return `<div class="msg-row system-row animate-slide-up">
                        <div class="msg-bubble system glass-card" style="border-left:4px solid #f59e0b; background:rgba(255,247,237,0.8); color:#b45309; max-width:400px; padding:12px 16px;">
                            <div style="font-size:24px; margin-bottom:8px;">🎂</div>
                            <div style="font-weight:700; font-size:14px; margin-bottom:4px;">Notifikasi Ulang Tahun</div>
                            <div style="font-size:13px; line-height:1.5;">${msg.message}</div>
                        </div>
                    </div>`;
                }
                return `<div class="msg-row system-row"><div class="msg-bubble system">${msg.message}</div></div>`;
            }

            const avatarHtml = !isMe ?
                `<div class="msg-avatar" style="background:${clr};">${init}</div>` :
                '';
            const nameHtml = !isMe ?
                `<div class="msg-name">${esc(msg.sender.name)}
            <span class="role-badge" style="background:${clr}22;color:${clr};">${rLabel}</span>
           </div>` :
                '';

            if (msg.type === 'request') {
                const meta = msg.meta || {};
                const metaInit = esc(meta.name || '?').charAt(0).toUpperCase();
                let actionHtml;
                if (msg.is_approved) {
                    actionHtml = `<div class="req-approved">✅ Sudah disetujui admin</div>`;
                } else if (isAdmin && !isMe) {
                    actionHtml = `<button class="btn btn-success" style="width:100%;justify-content:center;font-size:12.5px;padding:8px;"
                onclick="approveReset(${msg.id}, this)">✔ Setujui &amp; Reset Sandi</button>`;
                } else {
                    actionHtml =
                        `<div style="font-size:12px;color:#b45309;display:flex;align-items:center;gap:6px;"><span>⏳</span> Menunggu persetujuan admin...</div>`;
                }
                return `<div class="msg-row${isMe?' mine':''}" style="max-width:320px;" data-id="${msg.id}">
            ${avatarHtml}
            <div class="msg-group">
                ${nameHtml}
                <div class="request-card" id="req-card-${msg.id}">
                    <div class="request-card-header">
                        <div class="req-icon">🔐</div>
                        <div><div class="req-label">Permintaan Reset Sandi</div><div class="req-sub">${msg.created_at}</div></div>
                    </div>
                    <div class="request-card-user">
                        <div class="rcu-avatar">${metaInit}</div>
                        <div>
                            <div class="rcu-name">${esc(meta.name||'?')}</div>
                            <div class="rcu-username">@${esc(meta.username||'?')}</div>
                        </div>
                    </div>
                    ${actionHtml}
                </div>
            </div>
        </div>`;
            }

            return `<div class="msg-row${isMe?' mine':''}" data-id="${msg.id}">
        ${avatarHtml}
        <div class="msg-group">
            ${nameHtml}
            <div class="msg-bubble ${isMe?'mine':'theirs'} premium-chat-bubble">${esc(msg.message)}</div>
            <div class="msg-ts">${msg.created_at}</div>
        </div>
    </div>`;
        }

        function sendPageChat() {
            var input = document.getElementById('chat-page-input');
            var txt = input.value.trim();
            if (!txt) return;
            var sendBtn = document.querySelector('.chat-send-btn');
            input.value = '';
            input.focus();
            if (sendBtn) sendBtn.disabled = true;

            fetch(window.HUGO_CONFIG.chatSendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf,
                    'Accept': 'application/json',
                    'ngrok-skip-browser-warning': 'true'
                },
                body: JSON.stringify({ message: txt, receiver_id: receiverId }),
            })
            .then(function(r) {
                var ok = r.ok; // 200-299
                return r.text().then(function(text) {
                    try { return { ok: ok, data: JSON.parse(text) }; }
                    catch(e) { return { ok: ok, data: null }; }
                });
            })
            .then(function(result) {
                if (result.data && result.data.success && result.data.message) {
                    document.getElementById('chat-msgs-area')
                        .insertAdjacentHTML('beforeend', renderMsg(result.data.message));
                    lastMsgId = result.data.message.id;
                    
                    // Move active contact to top in sidebar
                    if (receiverId) {
                        var sidebar = document.querySelector('.chat-sidebar');
                        var globalItem = sidebar.querySelector('a[href*="chat"]');
                        var sItem = sidebar.querySelector('.user-list-item.active');
                        if (sItem && globalItem) globalItem.after(sItem);
                    }
                    
                    scrollBottom(true);
                } else if (!result.ok) {
                    // Real HTTP error (4xx/5xx)
                    showToast('Gagal mengirim pesan', 'danger');
                    // Re-populate input so user doesn't lose message
                }
                // If ok but no JSON data → message was likely saved, poll will catch it
            })
            .catch(function() {
                // Network error only — message may or may not have sent
                showToast('Koneksi bermasalah, coba lagi', 'warning');
            })
            .finally(function() {
                if (sendBtn) sendBtn.disabled = false;
            });
        }

        function approveReset(msgId, btn) {
            var newPass = prompt("Masukkan kata sandi baru untuk pengguna ini:");
            if (newPass === null) return; // User cancelled
            newPass = newPass.trim();
            if (!newPass) {
                showToast('Kata sandi tidak boleh kosong', 'danger');
                return;
            }
            if (newPass.length < 6) {
                showToast('Sandi minimal 6 karakter', 'danger');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Memproses...';
            fetch(`{{ url('/chat/approve') }}/${msgId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ password: newPass }),
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        const card = document.getElementById('req-card-' + msgId);
                        if (card) {
                            const actionEl = card.querySelector('.btn, div[style*="b45309"]');
                            if (actionEl) actionEl.outerHTML =
                                `<div class="req-approved">✅ Disetujui. Sandi baru: <strong>${esc(res.temp_password)}</strong></div>`;
                        }
                        showToast('Sandi berhasil direset ✓', 'success');
                    } else {
                        showToast(res.message || 'Gagal', 'danger');
                        btn.disabled = false;
                        btn.textContent = '✔ Setujui & Reset Sandi';
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan', 'danger');
                    btn.disabled = false;
                    btn.textContent = '✔ Setujui & Reset Sandi';
                });
        }

        function pollPageChat() {
            var url = window.HUGO_CONFIG.chatPollUrl + '?last_id=' + lastMsgId;
            if (receiverId) url += '&user_id=' + receiverId;
            fetch(url, { headers:{'ngrok-skip-browser-warning':'true'} })
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    if (data.messages && data.messages.length > 0) {
                        var area = document.getElementById('chat-msgs-area');
                        var sidebar = document.querySelector('.chat-sidebar');
                        var globalItem = sidebar.querySelector('a[href*="chat"]'); // Global group

                        var needBadgeRefresh = false;
                        data.messages.forEach(function(m) {
                            if (m.sender.id !== myUserId) {
                                if (window.HugoChatUi) {
                                    var peerKey = m.receiver_id ? String(m.sender.id) : 'global';
                                    if (peerKey !== HugoChatUi.getChatReadKey()) needBadgeRefresh = true;
                                }
                                // Only append if it matches current view (Global vs Private)
                                var isGlobalView = !receiverId;
                                var isMsgGlobal = !m.receiver_id;
                                
                                if (isGlobalView && isMsgGlobal) {
                                    area.insertAdjacentHTML('beforeend', renderMsg(m));
                                } else if (!isGlobalView && !isMsgGlobal) {
                                    // Private view: check if message belongs to this specific conversation
                                    if (m.sender.id == receiverId || m.receiver_id == receiverId) {
                                        area.insertAdjacentHTML('beforeend', renderMsg(m));
                                    }
                                }
                                
                                // Move sidebar item to top (after global)
                                var sItem = sidebar.querySelector('.user-list-item[href*="user_id=' + m.sender.id + '"]');
                                if (sItem && globalItem) {
                                    globalItem.after(sItem);
                                }
                            }
                        });
                        lastMsgId = data.last_id;
                        window.lastMsgId = lastMsgId;
                        if (window.HugoChatUi) HugoChatUi.lastMsgId = lastMsgId;
                        var chatKey = window.HugoChatUi ? HugoChatUi.getChatReadKey() : null;
                        if (chatKey && window.HugoChatUi) {
                            HugoChatUi.markChatRead(chatKey, lastMsgId);
                            HugoChatUi.updateContactBadge(chatKey, 0);
                        }
                        scrollBottom(false);
                        if (needBadgeRefresh && window.HugoChatUi) HugoChatUi.pollContactBadges();
                    }
                }).catch(function(){});
        }
        setInterval(pollPageChat, 4000);
    </script>
    <script src="{{ asset('js/chat-ui.js') }}?v={{ filemtime(public_path('js/chat-ui.js')) }}"></script>
@endpush
