<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\BirthdayController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\WebhookController;

// ── Auth ────────────────────────────────────────────────────────────────────

Route::get('/',       fn() => view('login'));
Route::get('/login',  fn() => view('login'))->name('login');

// ─── ONE-TIME SETUP: sets first case birthday to today ─────────────────────
// Visit: http://localhost/HUGO-Assistant/public/setup/birthday-today
// DELETE this route after using it!
Route::get('/setup/birthday-today', function () {
    $case = \App\Models\NotarisCase::first();
    if (!$case) return 'No cases found.';
    $case->birth_date = now()->format('Y-m-d');
    $case->save();
    return "✅ Birthday set to today (".now()->format('d M Y').') for: '.$case->client_name;
});

// ─── ONE-TIME SETUP: copies logo from images/ to favicon.png ─────────────────
Route::get('/setup/copy-logo', function () {
    $src  = public_path('images/hugo-logo.png');
    $dest = public_path('favicon.png');
    if (!file_exists($src)) return '❌ images/hugo-logo.png not found';
    copy($src, $dest);
    return '✅ favicon.png created from images/hugo-logo.png';
});
// ─── GITHUB WEBHOOK (no auth — uses HMAC signature verification internally) ───
// Must be excluded from CSRF middleware in app/Http/Middleware/VerifyCsrfToken.php
Route::post('/webhook/github', [WebhookController::class, 'github'])->name('webhook.github');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ── Forgot Password (Lupa Kata Sandi) ──────────────────────────────────
Route::get('/password/reset', fn() => view('password_reset'))->name('password.reset');
Route::post('/password/verify', function (Request $request) {
    $request->validate(['username' => 'required', 'name' => 'required']);

    $user = \App\Models\User::where('username', $request->username)
                            ->where('name', $request->name)
                            ->first();

    if (!$user) {
        return back()->with('error', 'Username dan nama tidak cocok dengan akun manapun.')->withInput();
    }

    // Send a password reset request message to admin's chat
    $adminUsers = \App\Models\User::where('role', 'admin')->get();
    foreach ($adminUsers as $admin) {
        \App\Models\ChatMessage::create([
            'sender_id'   => $user->id,
            'receiver_id' => $admin->id,
            'message'     => "🔐 Permintaan Reset Kata Sandi\n\nPengguna @{$user->username} ({$user->name}) meminta reset kata sandi.\nSilakan ubah kata sandi mereka melalui menu Kelola Akun.",
            'type'        => 'request',
            'meta'        => [
                'user_id'  => $user->id,
                'username' => $user->username,
                'name'     => $user->name,
            ],
        ]);
    }

    return back()->with('success_msg', 'Permintaan reset kata sandi telah dikirim ke Admin. Silakan hubungi Admin untuk mendapatkan kata sandi baru Anda.');
})->name('password.verify');

Route::post('/login', function (Request $request) {
    $request->validate([
        'username' => 'required', // This field can be username or email
        'password' => 'required'
    ]);

    $loginType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $credentials = [
        $loginType => $request->username,
        'password' => $request->password
    ];

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $request->session()->flash('just_logged_in', true);
        return redirect()->route('dashboard');
    }

    return back()->withErrors(['username' => 'Username/Email atau password salah.'])->withInput();
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// ── App Area (Auth required) ─────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // ── Dashboard — accessible to ALL authenticated roles ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Admin-Only Routes ──
    Route::middleware('role:admin')->group(function () {
        Route::get('/users/manage', [UserController::class, 'manage'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    // ── Staff Library (admin + notaris) ──
    Route::middleware('role:admin,notaris')->group(function () {
        Route::resource('staff', StaffController::class);
    });

    // ── Shared Routes (Chat & Profile) ──
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/approve/{id}', [ChatController::class, 'approve']);

    // FIX: corrected method names (was updateAvatar / updatePassword)
    Route::post('/profile/avatar', [UserController::class, 'uploadAvatar']);
    Route::post('/profile/password', [UserController::class, 'changePassword']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);

    // Birthday helper — accessible to ALL roles so the sidebar works for everyone
    Route::get('/birthdays/today', [BirthdayController::class, 'today']);

    // ── Operational Routes (admin, notaris, staff, freelancer) ──
    // Admin is included so they can view all case/payment/client data
    Route::middleware('role:admin,notaris,staff,freelancer')->group(function () {

        // Cases — calendar MUST come before resource() to avoid route conflict
        Route::get('/cases/calendar', [CaseController::class, 'calendar'])->name('cases.calendar');
        Route::resource('cases', CaseController::class);
        Route::post('/cases/{id}/status', [CaseController::class, 'updateStatus'])->name('cases.update-status');
        Route::post('/cases/{id}/note',   [CaseController::class, 'updateNote'])->name('cases.update-note');

        // Document Management
        Route::post('/cases/{id}/documents', [CaseController::class, 'uploadDocument'])->name('cases.documents.upload');
        Route::delete('/cases/documents/{id}', [CaseController::class, 'deleteDocument'])->name('cases.documents.delete');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payment.index');
        Route::put('/payment/{id}/status', [PaymentController::class, 'updateStatus'])->name('payment.update-status');

        // Clients
        Route::resource('clients', ClientController::class);

        // Archives (notaris & staff only)
        Route::middleware('role:notaris,staff')->group(function () {
            Route::resource('archives', ArchiveController::class);
        });

        // Exports
        Route::get('/export/cases/{type}', [ExportController::class, 'exportCases'])->name('export.cases');
        Route::get('/export/payments/{format}', [ExportController::class, 'exportPayments'])->name('export.payments');
    });
});
