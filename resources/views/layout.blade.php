<!DOCTYPE html>
<html lang="id" class="@yield('html-class', '')">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>HUGO - Assistant</title>
    <link rel="icon" href="{{ url('/favicon.png') }}?v={{ time() }}" type="image/png">
    <link rel="shortcut icon" href="{{ url('/favicon.ico') }}?v={{ time() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('styles')
    <style>
        .sidebar { z-index: 2000 !important; }
        #sidebar-overlay { z-index: 1900 !important; }
    </style>
</head>

<body class="@yield('body-class')">
    @include('partials.loader')
    @include('partials.chat-popup')

    <div class="app-shell">
        @include('partials.sidebar')

        <div class="content-area">
            @include('partials.topbar')
            <div class="main-content">
                <!-- Global Skeleton Loader (Every menu) -->
                <div id="global-skeleton" class="content-skeleton show">
                    <div class="skeleton" style="width: 30%; height: 24px; margin-bottom: 12px;"></div>
                    <div class="stat-grid-skeleton" style="margin-bottom: 16px;">
                        @for($i=0; $i<4; $i++)
                            <div class="skeleton-card">
                                <div class="skeleton skeleton-avatar" style="width: 40px; height: 40px; margin-bottom: 12px;"></div>
                                <div class="skeleton skeleton-line" style="width: 60%; height: 20px;"></div>
                                <div class="skeleton skeleton-line" style="width: 40%; height: 16px;"></div>
                            </div>
                        @endfor
                    </div>
                    <div class="skeleton" style="width: 100%; height: 300px; border-radius: 14px;"></div>
                </div>

                <div id="main-content-yield" class="page-yield" style="visibility: hidden; opacity: 0; transition: opacity 0.3s ease;">
                    <div class="page-container">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── MOBILE SIDEBAR OVERLAY ─── --}}
    <div id="sidebar-overlay"></div>

    {{-- ─── MOBILE BOTTOM NAV ─── --}}
    <nav id="mobile-bottom-nav">
        <a href="{{ url('/dashboard') }}" class="mob-nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Dashboard</span>
        </a>
        @auth
        @if(in_array(auth()->user()->role, ['notaris','staff','freelancer']))
        <a href="{{ url('/cases') }}" class="mob-nav-item {{ request()->is('cases*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
            <span>Kasus</span>
        </a>
        @endif
        <a href="{{ url('/chat') }}" class="mob-nav-item {{ request()->is('chat*') ? 'active' : '' }}" style="position:relative;">
            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span id="mob-nav-badge-chat" class="mob-badge" style="display:none;"></span>
            <span>Chat</span>
        </a>
        @if(in_array(auth()->user()->role, ['notaris','staff','freelancer']))
        <a href="{{ url('/clients') }}" class="mob-nav-item {{ request()->is('clients*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Klien</span>
        </a>
        @endif
        @endauth
        <a href="#" class="mob-nav-item" onclick="event.preventDefault(); openProfileModal();">
            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Profil</span>
        </a>
    </nav>

    @include('partials.modals')
    <div id="toast-container"></div>

    @stack('modals')
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
