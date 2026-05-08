<div class="top-bar">
    <!-- Hamburger (mobile only) -->
    <button id="hamburger" aria-label="Menu">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <h1 id="page-title">@yield('page-title', 'Dashboard')</h1>
    <div class="top-bar-right">

        {{-- Chat Icon Button (replaces floating bubble) --}}
        @if(!request()->is('chat*'))
        <div id="topbar-chat-btn" style="position:relative;" title="Buka Chat">
            <button onclick="toggleChatPopup()" style="
                width:36px; height:36px; border-radius:50%; border:none; cursor:pointer;
                background:linear-gradient(135deg,var(--accent),#e63d00);
                display:flex; align-items:center; justify-content:center;
                box-shadow:0 2px 8px rgba(204,51,0,0.35);
                transition:transform .2s, box-shadow .2s;
                position:relative;
            " onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                <svg viewBox="0 0 24 24" style="width:17px;height:17px;fill:#fff;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <span id="topbar-chat-badge" style="
                    display:none; position:absolute; top:-4px; right:-4px;
                    background:#22c55e; color:#fff; font-size:9px; font-weight:800;
                    min-width:17px; height:17px; border-radius:9px; border:2px solid #fff;
                    align-items:center; justify-content:center; padding:0 3px;
                ">0</span>
            </button>
        </div>
        @endif

        {{-- Profile Avatar --}}
        <div style="position:relative; width:34px; height:34px;">
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
