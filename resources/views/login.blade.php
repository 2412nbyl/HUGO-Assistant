<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUGO - Assistant | Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── CSS VARIABLES (required - no parent stylesheet) ── */
        :root {
            --accent:       #dc2626;
            --accent-hover: #b91c1c;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0f1117;
            overflow: hidden;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 64px;
            background: linear-gradient(145deg, #111827 0%, #1a1f2e 60%, #0f1117 100%);
            overflow: hidden;
        }

        /* subtle geometric SVG pattern */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(204, 51, 0, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(204, 51, 0, 0.05) 0%, transparent 40%);
            pointer-events: none;
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .left-content {
            position: relative;
            z-index: 2;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 60px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent), #ff5722);

            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 700;
            color: #f9fafb;
            letter-spacing: -0.3px;
        }

        .brand-name span {
            color: var(--accent);

        }

        .hero-heading {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 700;
            color: #f9fafb;
            line-height: 1.25;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .hero-sub {
            font-size: 16px;
            color: #9ca3af;
            line-height: 1.7;
            max-width: 380px;
        }

        .feature-list {
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #d1d5db;
            font-size: 14px;
        }

        .feature-dot {
            width: 8px;
            height: 8px;
            background: var(--accent);

            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 480px;
            flex-shrink: 0;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 48px;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.4px;
            margin-bottom: 8px;
        }

        .form-header p {
            font-size: 14px;
            color: #6b7280;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg.field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #9ca3af;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: #fff;
            color: #111827;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .input-wrap input:focus {
            border-color: var(--accent);

            box-shadow: 0 0 0 3px rgba(204, 51, 0, 0.10);
        }

        .input-wrap .toggle-eye {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }

        .input-wrap .toggle-eye:hover {
            color: #374151;
        }

        .input-wrap .toggle-eye svg {
            width: 18px;
            height: 18px;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .form-row label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #6b7280;
            cursor: pointer;
        }

        .form-row input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--accent);

            border-radius: 4px;
            padding: 0;
            margin: 0;
        }

        .form-row a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: color 0.15s, text-decoration 0.15s;
        }
        .form-row a:hover {
            text-decoration: underline;
            color: #b91c1c;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent) 0%, #e63d00 100%);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: transform 0.15s ease, opacity 0.15s, box-shadow 0.15s;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        .btn-login:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .error-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #b91c1c;
            font-size: 13px;
        }

        .footer-note {
            margin-top: 32px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                width: 100%;
                padding: 40px 28px;
            }
        }
    </style>
</head>

<body>

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="grid-pattern"></div>
        <div class="left-content">
            <div class="brand">
                <div style="position:relative; width:48px; height:48px;">
                    <img src="{{ url('/favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}" alt="HUGO Logo"
                        style="width:48px;height:48px;border-radius:10px;object-fit:cover;display:block;"
                        onerror="this.style.display='none'; document.getElementById('login-logo-fallback').style.display='flex';">
                    <div id="login-logo-fallback"
                        style="display:none; width:48px; height:48px; background:linear-gradient(135deg,#dc2626,#e63d00); border-radius:10px; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:22px;">
                        H</div>
                </div>
                <span class="brand-name"><span>HUGO</span> - Assistant</span>
            </div>

            <h1 class="hero-heading">Advanced Notarial Records & Reporting System.</h1>
            <p class="hero-sub"></p>

            <div class="feature-list">
                <div class="feature-item"><span class="feature-dot"></span> Cases Manajement &amp; Archived Document
                </div>
                <div class="feature-item"><span class="feature-dot"></span> Appointment Calendar &amp; Automatic
                    Deadline
                </div>
                <div class="feature-item"><span class="feature-dot"></span> Report &amp; Archive Export
                </div>
                <div class="feature-item"><span class="feature-dot"></span> Access Control by Roles</div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="form-header">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                <img src="{{ url('/favicon.png') }}" alt="HUGO" style="width:36px;height:36px;border-radius:8px;"
                    onerror="this.style.display='none'">
                <span style="font-size:13px;font-weight:600;color:#6b7280;">HUGO Assistant</span>
            </div>
            <h2>Selamat Datang Kembali</h2>
            <p>Silakan masuk untuk melanjutkan ke sistem</p>
        </div>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="username">Username / Email</label>
                <div class="input-wrap">
                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                    <input type="text" id="username" name="username" placeholder="Masukkan username"
                        value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="pass">Kata Sandi</label>
                <div class="input-wrap">
                    <svg class="field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg>
                    <input type="password" id="pass" name="password" placeholder="Masukkan kata sandi" required>
                    <span class="toggle-eye" onclick="togglePass()" title="Tampilkan/sembunyikan">
                        <svg id="eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg id="eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path
                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </span>
                </div>
            </div>

            <div class="form-row">
                <label><input type="checkbox" name="remember"> Ingat saya</label>
                <a href="{{ url('/password/reset') }}">Lupa kata sandi?</a>
            </div>

            <button type="submit" class="btn-login">
                Masuk ke Sistem
                <svg style="width:16px;height:16px;fill:none;stroke:#fff;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;margin-left:6px;vertical-align:middle;" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>

        <!-- Google Login Divider -->
        <div style="display:flex;align-items:center;gap:12px;margin:24px 0 0;">
            <div style="flex:1;height:1px;background:#e5e7eb;"></div>
            <span style="font-size:12px;color:#9ca3af;white-space:nowrap;">atau masuk dengan</span>
            <div style="flex:1;height:1px;background:#e5e7eb;"></div>
        </div>

        <a href="{{ route('auth.google') }}" style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:12px 16px;margin-top:14px;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;font-weight:500;color:#374151;text-decoration:none;transition:border-color 0.2s,box-shadow 0.2s;font-family:'Inter',sans-serif;" 
           onmouseover="this.style.borderColor='#CC3300';this.style.boxShadow='0 0 0 3px rgba(204,51,0,0.08)';"
           onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none';">
            <svg width="18" height="18" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.35-8.16 2.35-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Masuk dengan Google
        </a>

        <div class="footer-note">© 2026 <strong style="color:#374151;">Misfits</strong> — HUGO Assistant · Sistem Notaris</div>
    </div>


    <script>
        function togglePass() {
            const input = document.getElementById('pass');
            const show = document.getElementById('eye-show');
            const hide = document.getElementById('eye-hide');
            if (input.type === 'password') {
                input.type = 'text';
                show.style.display = 'none';
                hide.style.display = '';
            } else {
                input.type = 'password';
                show.style.display = '';
                hide.style.display = 'none';
            }
        }
    </script>
</body>

</html>
