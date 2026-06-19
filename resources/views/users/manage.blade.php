@extends('layout')
@section('page-title', 'Account Manage')
@section('content')
    <div class="animate-slide-up">
    <style>
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
            gap: 14px;
            width: 100%;
        }

        .user-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: box-shadow .15s, transform .15s;
        }

        .user-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, .09);
            transform: translateY(-2px);
        }

        .user-card-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
        }

        .user-card-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .user-card-username {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .role-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .role-admin {
            background: #fee2e2;
            color: #dc2626;
        }

        .role-notaris {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .role-staff {
            background: #dcfce7;
            color: #16a34a;
        }

        .role-freelancer {
            background: #f3e8ff;
            color: #9333ea;
        }


        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap input {
            padding-right: 40px !important;
        }

        .toggle-eye {
            position: absolute;
            right: 11px;
            cursor: pointer;
            color: #9ca3af;
            display: flex;
            align-items: center;
        }

        .toggle-eye svg {
            width: 17px;
            height: 17px;
        }

        .toggle-eye:hover {
            color: var(--accent);
        }

    </style>

    <div class="page-header-row">
        <span style="font-size:13.5px;color:var(--text-muted);">{{ $users->count() }} pengguna terdaftar</span>
        <button class="btn btn-primary" onclick="document.getElementById('add-user-modal').classList.add('open')">
            <svg viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            Tambah Akun
        </button>
    </div>

    <div class="user-grid">
        @foreach ($users as $u)
            @php
                $roleColors = [
                    'admin' => 'var(--accent)',
                    'notaris' => '#3b82f6',
                    'staff' => '#22c55e',
                    'freelancer' => '#9333ea',
                ];

                $roleEmoji = ['admin' => '', 'notaris' => '', 'staff' => '', 'freelancer' => ''];
                $avatarColor = $roleColors[$u->role] ?? '#6b7280';
                $isDeactivated = !$u->is_active;
            @endphp
            <div class="user-card" style="{{ $isDeactivated ? 'opacity: 0.7; filter: grayscale(100%);' : '' }}">
                @if ($u->avatar_url)
                    <img src="{{ $u->avatar_url . '?v=' . time() }}" class="user-card-avatar" style="object-fit:cover;"
                        id="av-{{ $u->id }}"
                        onerror="this.style.display='none';document.getElementById('av-fallback-{{ $u->id }}').style.display='flex';">
                    <div id="av-fallback-{{ $u->id }}" class="user-card-avatar"
                        style="display:none;background:linear-gradient(135deg,{{ $avatarColor }},{{ $avatarColor }}99);">
                        {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                    </div>
                @else
                    <div class="user-card-avatar"
                        style="background:linear-gradient(135deg,{{ $avatarColor }},{{ $avatarColor === 'var(--accent)' ? 'var(--accent)' : $avatarColor }}99);">
                        {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                    </div>

                @endif
                <div class="user-card-name">{{ $u->name }}</div>
                <div class="user-card-username">{{ '@' . $u->username }}</div>
                <span class="role-badge role-{{ $u->role }}">{{ $roleEmoji[$u->role] ?? '' }}
                    {{ ucfirst($u->role) }}
                    @if($isDeactivated) (Nonaktif) @endif
                </span>
                @if(!empty($u->google_id))
                <span style="display:inline-flex;align-items:center;gap:4px;background:#e8f5e9;color:#2e7d32;font-size:10.5px;font-weight:600;padding:3px 10px;border-radius:20px;margin-bottom:10px;">
                    <svg width="12" height="12" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.35-8.16 2.35-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                    Google Terhubung
                </span>
                @endif

                <div style="display:flex; gap: 8px; margin-top: auto; flex-wrap: wrap; justify-content: center;">
                    <button class="btn btn-secondary" style="font-size:12px;padding:6px 14px;" 
                            onclick="openEditUser('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->username) }}', '{{ addslashes($u->email) }}', '{{ $u->role }}')">
                        <svg viewBox="0 0 24 24" style="width:14px;height:14px;margin-bottom:-2px;margin-right:2px;">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        Edit
                    </button>

                    @if ($u->id !== auth()->id())
                        @if($isDeactivated)
                            <button type="button" class="btn" style="font-size:12px;padding:6px 14px; background: #22c55e; color: white;" 
                                    onclick="toggleUserStatus('{{ route('users.restore', $u->id) }}', 'Aktifkan Akun', 'Yakin ingin mengaktifkan akun {{ addslashes($u->name) }} kembali?')">
                                <svg viewBox="0 0 24 24" style="width:14px;height:14px;margin-bottom:-2px;margin-right:2px;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                Aktifkan
                            </button>
                        @else
                            <button type="button" class="btn btn-danger" style="font-size:12px;padding:6px 14px;" 
                                    onclick="toggleUserStatus('{{ route('users.deactivate', $u->id) }}', 'Nonaktifkan Akun', 'Yakin nonaktifkan akun {{ addslashes($u->name) }}? Pengguna tidak akan bisa login.')">
                                <svg viewBox="0 0 24 24" style="width:14px;height:14px;margin-bottom:-2px;margin-right:2px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                                Nonaktif
                            </button>
                        @endif

                        {{-- Hard Delete: Admin only --}}
                        @if(auth()->user()->role === 'admin')
                            <form action="{{ route('users.destroy', $u->id) }}" method="POST"
                                  onsubmit="event.preventDefault(); showConfirm('Hapus Akun Permanen', 'Yakin hapus permanen akun <strong>{{ addslashes($u->name) }}</strong>? Tindakan ini tidak dapat dibatalkan.', () => this.submit(), '!');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn" style="font-size:12px;padding:6px 14px;background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">
                                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;margin-bottom:-2px;margin-right:2px;stroke:#dc2626;fill:none;stroke-width:2;">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                        <path d="M10 11v6"></path><path d="M14 11v6"></path>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        @endif
                    @else
                        <span style="font-size:11.5px;color:#9ca3af; padding-top: 6px;">(akun anda)</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @push('modals')
    <!-- ADD USER MODAL -->
    <div id="add-user-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title">Tambah Akun Baru</span>
                <button class="modal-close"
                    onclick="document.getElementById('add-user-modal').classList.remove('open')">×</button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="form-row"><label>Nama Lengkap *</label><input type="text" name="name"
                            placeholder="Nama pengguna" required></div>
                    <div class="form-row"><label>Username *</label><input type="text" name="username"
                            placeholder="Username unik" required></div>
                    <div class="form-row"><label>Email *</label><input type="email" name="email"
                            placeholder="email@domain.com" required></div>
                    <div class="form-row">
                        <label>Password *</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="new-user-password" placeholder="Min. 8 karakter + simbol (@#!%)" required
                                minlength="8">
                            <span class="toggle-eye" onclick="toggleNewPass()" title="Tampilkan/Sembunyikan">
                                <svg id="np-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="np-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <label>Role / Akses *</label>
                        <select name="role" data-popup-title="Role / Akses" required>
                            <option value="notaris">Notaris (Full Akses Kasus)</option>
                            <option value="staff">Staff (Input & Edit)</option>
                            <option value="freelancer">Freelancer (Terbatas)</option>
                            <option value="admin">Admin (Kelola Akun)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('add-user-modal').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div id="edit-user-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-box" style="max-width:440px;">
            <div class="modal-header">
                <span class="modal-title">Edit Akun <span id="edit-title-name" style="color:var(--accent);"></span></span>
                <button class="modal-close"
                    onclick="document.getElementById('edit-user-modal').classList.remove('open')">×</button>
            </div>
            <form id="edit-user-form" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-row"><label>Nama Lengkap *</label><input type="text" name="name" id="edit-name"
                            required></div>
                    <div class="form-row"><label>Username *</label><input type="text" name="username" id="edit-username"
                            required></div>
                    <div class="form-row"><label>Email *</label><input type="email" name="email" id="edit-email"
                            required></div>
                    <div class="form-row">
                        <label>Password (Kosongkan jika tidak diubah)</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="edit-user-password" placeholder="Min. 8 karakter + simbol (@#!%)"
                                minlength="8">
                            <span class="toggle-eye" onclick="toggleEditPass()" title="Tampilkan/Sembunyikan">
                                <svg id="ep-eye-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="ep-eye-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                    <path
                                        d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="form-row">
                        <label>Role / Akses *</label>
                        <select name="role" id="edit-role" data-popup-title="Role / Akses" required>
                            <option value="notaris">Notaris (Full Akses Kasus)</option>
                            <option value="staff">Staff (Input & Edit)</option>
                            <option value="freelancer">Freelancer (Terbatas)</option>
                            <option value="admin">Admin (Kelola Akun)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        onclick="document.getElementById('edit-user-modal').classList.remove('open')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endpush


    @if ($errors->any())
        @push('scripts')
            <script>
                window.addEventListener('DOMContentLoaded', () => {
                    showToast(@json($errors->first()), 'danger');
                    document.getElementById('add-user-modal').classList.add('open');
                });
            </script>
        @endpush
    @endif
    @push('scripts')
        <script>
            function toggleNewPass() {
                const input = document.getElementById('new-user-password');
                const show = document.getElementById('np-eye-show');
                const hide = document.getElementById('np-eye-hide');
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

            function toggleEditPass() {
                const input = document.getElementById('edit-user-password');
                const show = document.getElementById('ep-eye-show');
                const hide = document.getElementById('ep-eye-hide');
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

            function openEditUser(id, name, username, email, role) {
                const modal = document.getElementById('edit-user-modal');
                const form = document.getElementById('edit-user-form');
                
                // Use the standardized baseUrl from config
                const baseUrl = window.HUGO_CONFIG.baseUrl || '';
                form.action = `${baseUrl}/users/${id}`.replace(/\/+$/, '');
                
                document.getElementById('edit-title-name').textContent = name;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-username').value = username;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-role').value = role;
                document.getElementById('edit-user-password').value = '';
                modal.classList.add('open');
            }

            function toggleUserStatus(url, title, msg) {
                showConfirm(title, msg, () => {
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        window.location.reload();
                    }).catch(err => {
                        showToast('Gagal memproses permintaan.', 'danger');
                    });
                });
            }

        </script>
    @endpush

@endsection
