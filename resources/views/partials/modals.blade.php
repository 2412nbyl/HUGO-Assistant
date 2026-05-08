<!-- ─── PROFILE MODAL ─── -->
<div id="profile-modal" class="modal-overlay" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Ubah Foto Profil</span>
            <button class="modal-close" onclick="closeProfileModal()">×</button>
        </div>

        <div class="avatar-preview-wrap">
            <img id="avatar-preview" class="avatar-preview-large"
                src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=111827&color=CC3300&bold=true&size=160' }}"
                alt="preview">
        </div>

        <label class="upload-btn" for="avatar-file-input">
            <svg viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                <polyline points="17 8 12 3 7 8" />
                <line x1="12" y1="3" x2="12" y2="15" />
            </svg>
            Pilih Foto dari Perangkat
        </label>
        <input type="file" id="avatar-file-input" accept="image/*" style="display:none;"
            onchange="handleAvatarSelect(event)">

        <canvas id="crop-canvas" width="320" height="320"></canvas>

        <div id="crop-controls" class="crop-controls" style="display:none;">
            <label>Zoom</label>
            <input type="range" id="zoom-range" class="crop-range" min="0.5" max="3"
                step="0.05" value="1" oninput="drawCrop()">
            <label>Putar</label>
            <input type="range" id="rotate-range" class="crop-range" min="-180" max="180"
                step="1" value="0" oninput="drawCrop()">
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeProfileModal()">Batal</button>
            <button class="btn btn-primary" id="save-avatar-btn" onclick="saveAvatar()"
                style="display:none;">Simpan Foto</button>
        </div>

        <div class="modal-header" style="padding-top:0;">
            <span class="modal-title">Informasi Profil</span>
        </div>
        <div style="padding:0 24px 10px;">
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; margin-bottom:4px; color:#374151;">Nama Lengkap</label>
                <input type="text" id="profile_name" class="chat-input"
                    style="width:100%; height:38px; padding:0 12px; border-radius:8px;" 
                    value="{{ auth()->user()->name }}">
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; margin-bottom:4px; color:#374151;">Email</label>
                <input type="email" id="profile_email" class="chat-input"
                    style="width:100%; height:38px; padding:0 12px; border-radius:8px;" 
                    value="{{ auth()->user()->email }}">
            </div>
            <button class="btn btn-primary" id="save-profile-btn" onclick="updateProfileInfo()"
                style="width:100%; justify-content:center; margin-bottom:10px;">Simpan Perubahan Profil</button>
        </div>

        <hr style="margin:10px 0; border:0; border-top:1px solid #e5e7eb;">

        <div class="modal-header" style="padding-top:0;">
            <span class="modal-title">Keamanan & Kata Sandi</span>
        </div>
        <div style="padding:0 24px 20px;">
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; margin-bottom:4px; color:#374151;">Kata Sandi Sekarang</label>
                <input type="password" id="current_password" class="chat-input"
                    style="width:100%; height:38px; padding:0 12px; border-radius:8px;" placeholder="••••••••">
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:12.5px; margin-bottom:4px; color:#374151;">Kata Sandi Baru</label>
                <input type="password" id="new_password" class="chat-input"
                    style="width:100%; height:38px; padding:0 12px; border-radius:8px;"
                    placeholder="Min. 6 karakter">
            </div>
            <div style="margin-bottom:18px;">
                <label style="display:block; font-size:12.5px; margin-bottom:4px; color:#374151;">Konfirmasi Sandi Baru</label>
                <input type="password" id="confirm_password" class="chat-input"
                    style="width:100%; height:38px; padding:0 12px; border-radius:8px;"
                    placeholder="Ulangi sandi baru">
            </div>
            <button class="btn btn-primary" id="save-password-btn" onclick="updatePassword()"
                style="width:100%; justify-content:center;">Perbarui Kata Sandi</button>
        </div>
    </div>
</div>

<!-- ─── CONFIRM MODAL ─── -->
<div id="confirm-modal" class="modal-overlay" onclick="if(event.target===this)closeConfirm()">
    <div class="modal-box">
        <div class="confirm-icon" id="confirm-icon" style="font-size:32px;">!</div>
        <div class="modal-header"
            style="justify-content:center; flex-direction:column; text-align:center; gap:6px;">
            <span class="modal-title" id="confirm-title">Konfirmasi</span>
            <p id="confirm-message" style="font-size:13.5px; color:#6b7280;"></p>
        </div>
        <div class="modal-footer" style="justify-content:center;">
            <button class="btn btn-secondary" onclick="closeConfirm()">Batal</button>
            <button class="btn btn-danger" id="confirm-ok-btn">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<!-- ─── DYNAMIC MODAL ─── -->
<div id="dynamic-modal" class="modal-overlay" onclick="if(event.target===this)closeDynamicModal()">
    <div class="modal-box" id="dynamic-modal-box">
        <div class="modal-header">
            <span class="modal-title" id="dynamic-modal-title"></span>
            <button class="modal-close" onclick="closeDynamicModal()">×</button>
        </div>
        <div id="dynamic-modal-body"></div>
    </div>
</div>

<!-- ─── BIRTHDAY POPUP MODAL ─── -->
<div id="birthday-modal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('open')">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <span class="modal-title">🎂 Ulang Tahun Klien!</span>
            <button class="modal-close" onclick="document.getElementById('birthday-modal').classList.remove('open')">×</button>
        </div>
        <div style="padding:24px;">
            <p style="font-size:13.5px;color:#6b7280;margin-bottom:16px;">Berikut adalah klien yang berulang tahun dalam waktu dekat:</p>
            <div class="birthday-list" id="birthday-list"></div>
        </div>
        <div class="modal-footer" style="flex-direction:column; gap:8px;">
            <button type="button" class="btn btn-primary" style="width:100%;justify-content:center;"
                onclick="document.getElementById('birthday-modal').classList.remove('open')">Tutup</button>
            <button type="button" class="btn btn-secondary" style="width:100%;justify-content:center;font-size:11.5px;border:none;background:transparent;color:#9ca3af;"
                onclick="dismissBirthdayReminder()">Jangan ingatkan saya lagi bulan ini</button>
        </div>
    </div>
</div>
