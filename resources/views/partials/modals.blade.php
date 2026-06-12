<!-- ─── PROFILE MODAL ─── -->
<style>
    .profile-tabs {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 20px;
        background: #f8fafc;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        padding: 6px;
        gap: 6px;
    }
    .profile-tab-btn {
        flex: 1;
        padding: 10px 14px;
        background: none;
        border: none;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        outline: none;
        border-radius: 12px;
    }
    .profile-tab-btn:hover {
        color: #1e293b;
        background: rgba(0,0,0,0.03);
    }
    .profile-tab-btn.active {
        color: var(--accent);
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }
    .profile-tab-content {
        display: none;
    }
    .profile-tab-content.active {
        display: block;
    }

    /* Premium Form Inputs styling */
    .profile-form-group {
        margin-bottom: 16px;
    }
    .profile-form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #475569;
    }
    .profile-input-wrap {
        position: relative;
    }
    .profile-input-wrap input {
        width: 100%;
        height: 42px;
        padding: 0 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 13.5px;
        font-family: 'Inter', sans-serif;
        color: #1e293b;
        outline: none;
        background: #f8fafc;
        transition: all 0.15s ease;
    }
    .profile-input-wrap input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        background: #fff;
    }

    /* Beautiful Avatar Uploader Frame */
    .avatar-preview-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 16px;
    }
    .avatar-frame {
        position: relative;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
        border: 4px solid #fff;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        transition: transform 0.2s ease;
    }
    .avatar-frame:hover {
        transform: scale(1.03);
    }
    .avatar-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-overlay-pencil {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: rgba(0,0,0,0.5);
        color: #fff;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        backdrop-filter: blur(2px);
    }
    .avatar-overlay-pencil svg {
        width: 12px; height: 12px; stroke: currentColor; fill: none; stroke-width: 2; margin-right: 4px;
    }

    /* Festive Birthday Styles */
    .bday-festive-box {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: none !important;
        position: relative;
        max-width: 480px !important;
        width: 90% !important;
        animation: bday-zoom .35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes bday-zoom {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    .bday-header {
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        padding: 32px 24px;
        text-align: center;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .bday-header::before {
        content: '🎉 🎈 🍰 🎁 ✨ 🥳 🎂 🥂';
        position: absolute;
        top: 8px; left: 0; right: 0;
        font-size: 16px; opacity: 0.25;
        letter-spacing: 8px;
        white-space: nowrap;
        animation: bday-float 5s ease-in-out infinite alternate;
    }
    @keyframes bday-float {
        0% { transform: translateY(0) rotate(-1deg); }
        100% { transform: translateY(-6px) rotate(1deg); }
    }
    .bday-title {
        font-size: 22px;
        font-weight: 800;
        margin-top: 8px;
        text-shadow: 0 2px 6px rgba(0,0,0,0.15);
        font-family: 'Inter', sans-serif;
    }
    .bday-sub {
        font-size: 13.5px;
        opacity: 0.95;
        margin-top: 4px;
        font-weight: 500;
    }
    .bday-list-wrap {
        padding: 20px 24px;
        max-height: 280px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .bday-card-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        padding: 14px 16px;
        border-radius: 16px;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        gap: 12px;
    }
    .bday-card-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.04);
        border-color: #fee2e2;
        background: #fff;
    }
    .bday-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #ffe4e6;
        border: 2px solid #fecdd3;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
        color: #ef4444;
        position: relative;
        flex-shrink: 0;
    }
    .bday-avatar.is-today {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #d97706;
    }
    .bday-crown {
        position: absolute;
        top: -12px;
        font-size: 14px;
    }
    .bday-info {
        flex: 1;
        min-width: 0;
    }
    .bday-client-name {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .bday-case-name {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bday-badge-wrap {
        text-align: right;
        flex-shrink: 0;
    }
    .bday-tag {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .bday-tag.today {
        background: linear-gradient(45deg, #ef4444, #f59e0b);
        color: #fff;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
        animation: pulse 1.5s infinite;
    }
    .bday-tag.upcoming {
        background: #e2e8f0;
        color: #475569;
    }
    .bday-date {
        font-size: 11px;
        color: #64748b;
        font-weight: 600;
        margin-top: 3px;
        display: block;
    }
</style>

<div id="profile-modal" class="modal-overlay" onclick="if(event.target===this)closeProfileModal()">
    <div class="modal-box" style="max-width: 440px; display: flex; flex-direction: column; border-radius: 20px; padding: 0;">
        
        <div class="profile-tabs">
            <button type="button" class="profile-tab-btn active" id="btn-tab-info" onclick="switchProfileTab('info')">Info Profil</button>
            <button type="button" class="profile-tab-btn" id="btn-tab-security" onclick="switchProfileTab('security')">Keamanan</button>
        </div>

        <!-- TAB CONTENT: INFO -->
        <div class="profile-tab-content active" id="tab-profile-info" style="overflow-y: auto; flex: 1;">
            <div style="padding: 0 24px 24px;">
                <div class="avatar-preview-wrap">
                    <label class="avatar-frame" for="avatar-file-input">
                        <img id="avatar-preview"
                            src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=111827&color=CC3300&bold=true&size=160' }}"
                            alt="preview">
                        <div class="avatar-overlay-pencil">
                            <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Ubah Foto
                        </div>
                    </label>
                </div>

                <input type="file" id="avatar-file-input" accept="image/*" style="display:none;"
                    onchange="handleAvatarSelect(event)">

                <canvas id="crop-canvas" width="320" height="320" style="margin: 0 auto 14px; display: none; border-radius: 12px;"></canvas>

                <div id="crop-controls" class="crop-controls" style="display:none; margin-bottom: 16px;">
                    <label style="font-size:12px; font-weight:700; color:#475569;">Zoom</label>
                    <input type="range" id="zoom-range" class="crop-range" min="0.5" max="3" step="0.05" value="1" oninput="drawCrop()">
                    <label style="font-size:12px; font-weight:700; color:#475569; margin-top:8px; display:block;">Putar</label>
                    <input type="range" id="rotate-range" class="crop-range" min="-180" max="180" step="1" value="0" oninput="drawCrop()">
                </div>

                <div id="crop-save-wrap" style="display:none; justify-content: flex-end; gap: 8px; margin-bottom: 16px;">
                    <button type="button" class="btn btn-secondary" onclick="cancelCrop()" style="padding: 6px 12px; min-height:32px; font-size:12px;">Batal</button>
                    <button type="button" class="btn btn-primary" id="save-avatar-btn" onclick="saveAvatar()" style="padding: 6px 12px; min-height:32px; font-size:12px;">Potong & Simpan</button>
                </div>

                <div class="profile-form-group">
                    <label>Nama Lengkap</label>
                    <div class="profile-input-wrap">
                        <input type="text" id="profile_name" value="{{ auth()->user()->name }}">
                    </div>
                </div>
                <div class="profile-form-group" style="margin-bottom: 24px;">
                    <label>Email</label>
                    <div class="profile-input-wrap">
                        <input type="email" id="profile_email" value="{{ auth()->user()->email }}">
                    </div>
                </div>
                <button class="btn btn-primary" id="save-profile-btn" onclick="updateProfileInfo()"
                    style="width:100%; justify-content:center; height: 42px; border-radius: 10px;">Simpan Perubahan</button>
            </div>
        </div>

        <!-- TAB CONTENT: SECURITY -->
        <div class="profile-tab-content" id="tab-profile-security" style="overflow-y: auto; flex: 1;">
            <div style="padding: 0 24px 24px;">
                <div class="profile-form-group">
                    <label>Kata Sandi Sekarang</label>
                    <div class="profile-input-wrap">
                        <input type="password" id="current_password" placeholder="••••••••">
                    </div>
                </div>
                <div class="profile-form-group">
                    <label>Kata Sandi Baru</label>
                    <div class="profile-input-wrap">
                        <input type="password" id="new_password" placeholder="Min. 6 karakter">
                    </div>
                </div>
                <div class="profile-form-group" style="margin-bottom: 24px;">
                    <label>Konfirmasi Sandi Baru</label>
                    <div class="profile-input-wrap">
                        <input type="password" id="confirm_password" placeholder="Ulangi sandi baru">
                    </div>
                </div>
                <button class="btn btn-primary" id="save-password-btn" onclick="updatePassword()"
                    style="width:100%; justify-content:center; height: 42px; border-radius: 10px;">Perbarui Kata Sandi</button>
            </div>
        </div>
    </div>
</div>

<script>
function switchProfileTab(tab) {
    document.querySelectorAll('.profile-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.profile-tab-content').forEach(content => content.classList.remove('active'));
    
    if (tab === 'info') {
        document.getElementById('btn-tab-info').classList.add('active');
        document.getElementById('tab-profile-info').classList.add('active');
    } else if (tab === 'security') {
        document.getElementById('btn-tab-security').classList.add('active');
        document.getElementById('tab-profile-security').classList.add('active');
    }
}

function cancelCrop() {
    document.getElementById('crop-canvas').style.display = 'none';
    document.getElementById('crop-controls').style.display = 'none';
    document.getElementById('crop-save-wrap').style.display = 'none';
    document.getElementById('avatar-preview').style.display = '';
    if (window._originalAvatarSrc) {
        document.getElementById('avatar-preview').src = window._originalAvatarSrc;
    }
    const fileInput = document.getElementById('avatar-file-input');
    if (fileInput) fileInput.value = '';
}
</script>

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
    <div class="modal-box bday-festive-box">
        <div class="bday-header">
            <button class="modal-close" onclick="document.getElementById('birthday-modal').classList.remove('open')" style="position: absolute; right: 14px; top: 12px; color: #fff; font-size: 24px;">×</button>
            <div style="font-size: 40px; margin-top: 4px;">🎂</div>
            <div class="bday-title">Ulang Tahun Klien</div>
            <div class="bday-sub">Mari rayakan hari bahagia bersama klien setia kita!</div>
        </div>
        <div class="bday-list-wrap" id="birthday-list">
            <!-- Dynamically populated via JS -->
        </div>
        <div class="bday-footer-btns">
            <button type="button" class="btn btn-primary" style="width:100%; justify-content:center; height: 42px; border-radius: 12px; font-weight: 700; background: linear-gradient(135deg, #ef4444, #f59e0b); border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);"
                onclick="document.getElementById('birthday-modal').classList.remove('open')">Selesai & Tutup</button>
            <button type="button" style="width:100%; text-align:center; font-size:12px; font-weight: 700; border:none; background:transparent; color:#94a3b8; cursor:pointer; padding: 4px 0;"
                onclick="dismissBirthdayReminder()">Snooze Pengingat 30 Hari</button>
        </div>
    </div>
</div>
