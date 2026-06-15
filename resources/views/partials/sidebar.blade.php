{{-- Sidebar --}}
<!-- ─── SIDEBAR ─── -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-brand-slot">
            <img src="{{ url('/favicon.png') }}?v={{ @filemtime(public_path('favicon.png')) ?: 1 }}"
                alt="HUGO Logo"
                onerror="this.style.display='none'; var el=document.getElementById('sidebar-logo-fallback'); if(el){ el.style.display='flex'; }">
            <div id="sidebar-logo-fallback" class="sidebar-brand-fallback">H</div>
        </div>
        <span class="logo-text"><span>HUGO</span> - Assistant</span>
    </div>

    <nav class="sidebar-nav">
        {{-- ── Dashboard: visible to ALL roles ── --}}
        <div class="nav-section-label">Menu Utama</div>

        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" /></svg>
            Main Dashboard
        </a>

        {{-- ── Chat: all roles ── --}}
        <a href="{{ url('/chat') }}" class="nav-item {{ request()->is('chat*') ? 'active' : '' }}" id="nav-chat-link">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
            Chat
            <span class="nav-badge" id="chat-nav-badge">0</span>
        </a>

        {{-- ── Non-Admin Operational menus (Notaris, Staff, Freelancer) ── --}}
        @if(in_array(auth()->user()->role, ['notaris', 'staff', 'freelancer']))

            <a href="{{ route('cases.calendar') }}" class="nav-item {{ request()->is('cases/calendar') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" /></svg>
                Case Calendar
            </a>

            <a href="{{ route('cases.index') }}" class="nav-item {{ (request()->is('cases*') && !request()->is('cases/calendar')) ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" /><polyline points="14 2 14 8 20 8" /><line x1="9" y1="15" x2="15" y2="15" /><line x1="9" y1="11" x2="15" y2="11" /></svg>
                Cases
                <span id="case-birthday-badge" class="nav-badge" style="display:none; background:#f59e0b; color:white; margin-left:auto; font-size:10px; padding:1px 6px;">!</span>
            </a>

            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->is('clients*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Client List
            </a>

            <a href="{{ route('payment.index') }}" class="nav-item {{ request()->is('payments*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" /><line x1="1" y1="10" x2="23" y2="10" /></svg>
                Payment Management
            </a>

            {{-- Staff Library: notaris only in operational group --}}
            @if(auth()->user()->role === 'notaris')
                <a href="{{ route('staff.index') }}" class="nav-item {{ request()->is('staff*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                    Staff Management
                </a>
            @endif

            {{-- Document Arsip Dropdown --}}
            <div class="nav-dropdown {{ (request()->is('archives*') || request()->is('finished-cases*')) ? 'open' : '' }}">
                <div class="nav-item nav-dropdown-toggle {{ (request()->is('archives*') || request()->is('finished-cases*')) ? 'active' : '' }}" onclick="toggleNavDropdown(this)" style="user-select: none;">
                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <span style="flex: 1;">Document Arsip</span>
                    <svg class="nav-dropdown-chevron" viewBox="0 0 24 24" style="width:12px; height:12px; stroke-width:3; margin-left:auto; transition: transform 0.2s;"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="nav-dropdown-items" style="{{ (request()->is('archives*') || request()->is('finished-cases*')) ? 'display:flex;' : 'display:none;' }}">
                    @if(auth()->user()->role !== 'freelancer')
                        <a href="{{ route('archives.index', ['tab' => 'support']) }}" class="nav-sub-item {{ (request()->is('archives*') && request()->get('tab', 'support') === 'support') ? 'active' : '' }}">
                            Archive Document
                        </a>
                    @endif
                    <a href="{{ route('finished-cases.index') }}" class="nav-sub-item {{ (request()->is('finished-cases*') || (request()->is('archives*') && request()->get('tab') === 'finished')) ? 'active' : '' }}">
                        Finished Cases
                    </a>
                </div>
            </div>

            @if(auth()->user()->role !== 'freelancer')
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->is('reports') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Report Archive
            </a>
            @endif

        @endif

        {{-- ── Admin-only Menu (Account Manage & Staff Management) ── --}}
        @if(auth()->user()->role === 'admin')
            <div class="nav-section-label">Administrasi</div>
            <a href="{{ route('staff.index') }}" class="nav-item {{ request()->is('staff*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                Staff Management
            </a>
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->is('users*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></svg>
                Account Manage
            </a>
        @endif

    </nav>


    <!-- Profile -->
    <div class="sidebar-profile">
        <div class="profile-btn" onclick="openProfileModal()">
            <div class="sidebar-profile-avatar-slot">
                <img src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url . '?v=' . time() : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=111827&color=dc2626&bold=true&size=80' }}"
                    alt="avatar" class="profile-avatar" id="sidebar-avatar"
                    onerror="this.style.display='none'; var f=document.getElementById('sidebar-avatar-fallback'); if(f){ f.style.display='flex'; }">

                <div id="sidebar-avatar-fallback" class="profile-avatar">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="profile-info">
                <div class="profile-name">{{ auth()->user()->name }}</div>
                <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px;">
            @csrf
            <button type="submit" class="btn btn-secondary"
                style="width:100%;justify-content:center;font-size:12.5px;padding:7px;">
                <svg viewBox="0 0 24 24" style="width:13px;height:13px;">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<script>
function toggleNavDropdown(toggleEl) {
    var dropdown = toggleEl.closest('.nav-dropdown');
    var items = dropdown.querySelector('.nav-dropdown-items');
    if (!dropdown || !items) return;
    
    var isOpen = dropdown.classList.toggle('open');
    if (isOpen) {
        items.style.display = 'flex';
    } else {
        items.style.display = 'none';
    }
}
</script>
