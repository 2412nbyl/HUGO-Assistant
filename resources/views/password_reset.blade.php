<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi – HUGO Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent:       #dc2626;
            --accent-hover: #b91c1c;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f1117;
            position: relative;
            overflow: hidden;
        }

        /* Subtle background pattern like login */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(220, 38, 38, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(220, 38, 38, 0.05) 0%, transparent 40%);
            pointer-events: none;
        }

        .card {
            background: #1a1f2e;
            border: 1px solid #374151;
            border-radius: 18px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
            position: relative;
            z-index: 2;
        }

        .logo-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent), #e63d00);
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

        .logo-row span {
            font-size: 20px;
            font-weight: 700;
            color: #f9fafb;
        }

        .logo-row span b {
            color: var(--accent);
        }

        h2 {
            font-size: 24px;
            font-weight: 700;
            color: #f9fafb;
            margin-bottom: 10px;
            letter-spacing: -0.4px;
        }

        p.sub {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 26px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #d1d5db;
            margin-bottom: 8px;
        }

        input[type=text] {
            width: 100%;
            padding: 12px 14px;
            background: #0f1117;
            border: 1.5px solid #374151;
            border-radius: 10px;
            color: #f9fafb;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color .15s, box-shadow 0.15s;
            outline: none;
        }

        input[type=text]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
        }

        .btn {
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
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
        }

        .btn:hover {
            opacity: 0.95;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: #9ca3af;
            text-decoration: none;
            transition: color 0.15s;
        }

        .back-link:hover {
            color: var(--accent);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .alert-err {
            background: #7f1d1d33;
            border: 1px solid #991b1b;
            color: #fca5a5;
        }

        .alert-ok {
            background: #064e3b33;
            border: 1px solid #059669;
            color: #6ee7b7;
        }

        .new-pass-box {
            background: #0f1117;
            border: 1.5px solid #059669;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 24px;
        }

        .new-pass-box .label {
            font-size: 12px;
            color: #9ca3af;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .new-pass-box .pass {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 4px;
            font-family: monospace;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo-row">
            <div style="position:relative; width:44px; height:44px;">
                <img src="{{ url('/favicon.png') }}?v={{ time() }}" alt="HUGO Logo"
                    style="width:44px;height:44px;border-radius:10px;object-fit:cover;display:block;"
                    onerror="this.style.display='none'; document.getElementById('reset-logo-fallback').style.display='flex';">
                <div id="reset-logo-fallback"
                    style="display:none; width:44px; height:44px; background:linear-gradient(135deg,#dc2626,#e63d00); border-radius:10px; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:20px;">
                    H</div>
            </div>
            <span><b>HUGO</b> Assistant</span>
        </div>

        <h2>Lupa Kata Sandi?</h2>
        <p class="sub">Masukkan username dan nama lengkap akun kamu untuk verifikasi. Kata sandi baru akan diberikan.
        </p>

        @if (session('error'))
            <div class="alert alert-err">{{ session('error') }}</div>
        @endif

        @if (session('success_msg'))
            <div class="alert alert-ok" style="display:flex;align-items:flex-start;gap:10px;">
                <span style="font-size:20px;flex-shrink:0;">✅</span>
                <div>
                    <strong style="display:block;margin-bottom:4px;">Permintaan Terkirim!</strong>
                    {{ session('success_msg') }}
                </div>
            </div>
            <div class="new-pass-box" style="border-color:#22c55e;">
                <div class="label">Langkah Selanjutnya</div>
                <div style="font-size:13px; color:#d1d5db; line-height:1.7; text-align:left; margin-top:8px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span style="background:#22c55e;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">1</span>
                        Admin akan menerima pesan chat pribadi
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                        <span style="background:#f59e0b;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">2</span>
                        Admin akan mereset kata sandi Anda
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="background:#6366f1;color:#fff;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;">3</span>
                        Hubungi Admin untuk mendapatkan kata sandi baru
                    </div>
                </div>
            </div>
            <a href="{{ url('/login') }}" class="btn"
                style="display:block;text-align:center;text-decoration:none;">Kembali ke Login</a>
        @elseif (session('new_password'))
            <div class="alert alert-ok">Verifikasi berhasil! Kata sandi baru kamu:</div>
            <div class="new-pass-box">
                <div class="label">Kata Sandi Baru</div>
                <div class="pass">{{ session('new_password') }}</div>
            </div>
            <p class="sub" style="margin-bottom:20px;">Segera ganti setelah login. Admin juga sudah diberitahu.</p>
            <a href="{{ url('/login') }}" class="btn"
                style="display:block;text-align:center;text-decoration:none;">Kembali ke Login</a>
        @else
            <form method="POST" action="{{ url('/password/verify') }}">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="username kamu" required
                        value="{{ old('username') }}">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" placeholder="Nama lengkap sesuai akun" required
                        value="{{ old('name') }}">
                </div>
                <button type="submit" class="btn">Kirim Permintaan Reset</button>
            </form>
        @endif

        <a href="{{ url('/login') }}" class="back-link">← Kembali ke halaman login</a>
    </div>
</body>

</html>
