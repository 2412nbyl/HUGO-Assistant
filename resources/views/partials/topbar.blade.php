<div class="top-bar">
    <!-- Hamburger (mobile only) -->
    <button type="button" id="hamburger" aria-label="Menu">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <h1 id="page-title">@yield('page-title', 'Dashboard')</h1>
    <div class="top-bar-right">

        {{-- Chat Icon Button (replaces floating bubble) --}}
        @if(!request()->is('chat*'))
        <div id="topbar-chat-btn" class="topbar-chat-wrap" title="Buka Chat">
            <button type="button" class="topbar-icon-btn topbar-icon-btn--chat" onclick="toggleChatPopup()" aria-label="Buka chat">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span id="topbar-chat-badge" class="topbar-chat-badge">0</span>
            </button>
        </div>
        @endif

        {{-- Profile Avatar --}}
        <div class="topbar-avatar-wrap">
            <img src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url . '?v=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=111827&color=CC3300&bold=true&size=80' }}"
                alt="avatar" class="top-bar-avatar" onclick="openProfileModal()"
                onerror="this.style.display='none'; document.getElementById('topbar-avatar-fallback').style.display='flex';">
            <div id="topbar-avatar-fallback" class="top-bar-avatar"
                style="display:none; background:#f3f4f6; color:var(--accent); align-items:center; justify-content:center; font-weight:700; cursor:pointer;"
                onclick="openProfileModal()">
                {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </div>
</div>
