/**
 * HUGO Assistant — Global App Scripts v2
 */

/* ─── PAGE LOADER ─── */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var loader = document.getElementById('page-loader');
        if (loader) setTimeout(function(){ loader.classList.remove('show'); }, 400);
    });
})();

/* ─── TOAST ─── */
function showToast(msg, type = 'info') {
    const c = document.getElementById('toast-container');
    if (!c) return;
    const t = document.createElement('div');
    t.className = 'toast';
    t.style.borderLeftColor = type === 'success' ? '#22c55e' : type === 'danger' ? '#ef4444' : '#CC3300';
    t.textContent = msg;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3100);
}

/* ─── PROFILE MODAL ─── */
function openProfileModal() {
    document.getElementById('profile-modal').classList.add('open');
}
function closeProfileModal() {
    document.getElementById('profile-modal').classList.remove('open');
}

/* ─── AVATAR CROP ─── */
var cropImage = null, cropPanX = 0, cropPanY = 0;

function handleAvatarSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        cropImage = new Image();
        cropImage.onload = () => {
            // Save original src so cancelCrop() can restore it
            window._originalAvatarSrc = document.getElementById('avatar-preview').src;
            document.getElementById('crop-canvas').style.display = 'block';
            document.getElementById('crop-controls').style.display = 'flex';
            document.getElementById('crop-save-wrap').style.display = 'flex';
            cropPanX = 0; cropPanY = 0;
            document.getElementById('zoom-range').value = 1;
            document.getElementById('rotate-range').value = 0;
            drawCrop();
        };
        cropImage.src = ev.target.result;
    };
    reader.readAsDataURL(file);
}

function drawCrop() {
    if (!cropImage) return;
    const canvas = document.getElementById('crop-canvas');
    const ctx = canvas.getContext('2d');
    const zoom = parseFloat(document.getElementById('zoom-range').value);
    const rotate = parseFloat(document.getElementById('rotate-range').value) * Math.PI / 180;
    const size = 320;
    ctx.clearRect(0, 0, size, size);
    ctx.save();
    ctx.beginPath();
    ctx.arc(size / 2, size / 2, size / 2, 0, Math.PI * 2);
    ctx.clip();
    ctx.translate(size / 2 + cropPanX, size / 2 + cropPanY);
    ctx.rotate(rotate);
    ctx.scale(zoom, zoom);
    ctx.drawImage(cropImage, -cropImage.naturalWidth / 2, -cropImage.naturalHeight / 2, cropImage.naturalWidth, cropImage.naturalHeight);
    ctx.restore();
    document.getElementById('avatar-preview').src = canvas.toDataURL();
}

// Drag to pan on crop canvas
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('crop-canvas');
        if (!canvas) return;
        let dragging = false, sx = 0, sy = 0;
        canvas.addEventListener('mousedown', e => { dragging = true; sx = e.clientX; sy = e.clientY; });
        window.addEventListener('mousemove', e => {
            if (!dragging) return;
            cropPanX += e.clientX - sx; cropPanY += e.clientY - sy;
            sx = e.clientX; sy = e.clientY; drawCrop();
        });
        window.addEventListener('mouseup', () => dragging = false);
        canvas.addEventListener('touchstart', e => {
            const t = e.touches[0]; dragging = true; sx = t.clientX; sy = t.clientY;
        });
        window.addEventListener('touchmove', e => {
            if (!dragging) return;
            const t = e.touches[0];
            cropPanX += t.clientX - sx; cropPanY += t.clientY - sy;
            sx = t.clientX; sy = t.clientY; drawCrop();
        });
        window.addEventListener('touchend', () => dragging = false);
    });
})();

function saveAvatar() {
    const canvas = document.getElementById('crop-canvas');
    const data = canvas.toDataURL('image/jpeg', 0.9);
    const btn = document.getElementById('save-avatar-btn');
    btn.disabled = true; btn.textContent = 'Menyimpan...';
    fetch(window.HUGO_CONFIG.avatarUrl || window.HUGO_CONFIG.chatSendUrl.replace('/chat','/profile/avatar'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ image: data }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const stamp = '?t=' + Date.now();
            document.querySelectorAll('.profile-avatar, .top-bar-avatar, #avatar-preview, #sidebar-avatar')
                .forEach(el => {
                    if (el.tagName === 'IMG') {
                        el.src = res.url + stamp;
                        el.style.display = ''; // Ensure the image is visible
                    }
                });
            // Hide any visible initials fallbacks
            document.querySelectorAll('#topbar-avatar-fallback, #sidebar-avatar-fallback').forEach(fb => {
                fb.style.display = 'none';
            });
            window._originalAvatarSrc = res.url + stamp; // Update stored original src to the new avatar URL
            cancelCrop();
            closeProfileModal();
            showToast('Foto profil berhasil disimpan ✓', 'success');
        } else { showToast(res.message || 'Gagal menyimpan foto', 'danger'); }
    })
    .catch(() => showToast('Gagal menghubungi server', 'danger'))
    .finally(() => { btn.disabled = false; btn.textContent = 'Potong & Simpan'; });
}

/* ─── CHANGE PASSWORD ─── */
function updatePassword() {
    const current = document.getElementById('current_password');
    const newp = document.getElementById('new_password');
    const conf = document.getElementById('confirm_password');
    const btn = document.getElementById('save-password-btn');
    if (!current.value || !newp.value || !conf.value) return showToast('Harap isi semua field sandi', 'danger');
    if (newp.value.length < 6) return showToast('Sandi baru minimal 6 karakter', 'danger');
    if (newp.value !== conf.value) return showToast('Konfirmasi sandi tidak cocok', 'danger');
    btn.disabled = true; btn.textContent = 'Memperbarui...';
    fetch(window.HUGO_CONFIG.passwordUrl || window.HUGO_CONFIG.chatSendUrl.replace('/chat','/profile/password'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ current_password: current.value, new_password: newp.value, new_password_confirmation: conf.value }),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false; btn.textContent = 'Perbarui Kata Sandi';
        if (res.success) {
            current.value = ''; newp.value = ''; conf.value = '';
            showToast('Kata sandi berhasil diperbarui ✓', 'success');
        } else { showToast(res.message || 'Gagal memperbarui sandi', 'danger'); }
    })
    .catch(() => { btn.disabled = false; btn.textContent = 'Perbarui Kata Sandi'; showToast('Terjadi kesalahan koneksi', 'danger'); });
}

/* ─── UPDATE PROFILE INFO ─── */
function updateProfileInfo() {
    const nameInput = document.getElementById('profile_name');
    const emailInput = document.getElementById('profile_email');
    const btn = document.getElementById('save-profile-btn');
    
    if (!nameInput.value || !emailInput.value) return showToast('Nama dan Email tidak boleh kosong', 'danger');
    
    btn.disabled = true; btn.textContent = 'Menyimpan...';
    
    const updateUrl = window.HUGO_CONFIG.baseUrl + '/profile/update';
    
    fetch(updateUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.HUGO_CONFIG.csrf, 'Accept': 'application/json' },
        body: JSON.stringify({ name: nameInput.value, email: emailInput.value }),
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false; btn.textContent = 'Simpan Perubahan Profil';
        if (res.success) {
            // Update visible names in UI
            document.querySelectorAll('.profile-name').forEach(el => el.textContent = res.name);
            showToast('Profil berhasil diperbarui ✓', 'success');
        } else { showToast(res.message || 'Gagal memperbarui profil', 'danger'); }
    })
    .catch(() => { btn.disabled = false; btn.textContent = 'Simpan Perubahan Profil'; showToast('Gagal menghubungi server', 'danger'); });
}

/* ─── CONFIRM MODAL ─── */
// showConfirm and closeConfirm are defined in scripts.blade.php
// Re-binding the OK button click here as a fallback
var _confirmCb = null;
document.addEventListener('DOMContentLoaded', function(){
    var okBtn=document.getElementById('confirm-ok-btn');
    if(okBtn) okBtn.addEventListener('click',function(){ if(window._confirmCb) window._confirmCb(); var o=document.getElementById('confirm-modal'); if(o) o.classList.remove('open'); window._confirmCb=null; });
});

/* ─── DYNAMIC MODAL ─── */
function openDynamicModal(title, bodyHtml, wide = false) {
    document.getElementById('dynamic-modal-title').textContent = title;
    document.getElementById('dynamic-modal-body').innerHTML = bodyHtml;
    document.getElementById('dynamic-modal-box').style.maxWidth = wide ? '640px' : '480px';
    document.getElementById('dynamic-modal').classList.add('open');
}
function closeDynamicModal() { document.getElementById('dynamic-modal').classList.remove('open'); }

/* ─── DRAGGABLE CHAT BUBBLE ─── */
(function () {
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('chat-bubble');
        if (!el) return;
        var dragging = false, didDrag = false, ox = 0, oy = 0;

        // Repositions the popup panel to sit above/beside the bubble
        window.syncPopupPosition = function() {
            var popup = document.getElementById('chat-popup');
            if (!popup || !popup.classList.contains('open')) return;
            var rect = el.getBoundingClientRect();
            var pw = popup.offsetWidth  || 320;
            var ph = popup.offsetHeight || 420;
            var margin = 8;
            // default: above-left of bubble
            var top  = rect.top  - ph - margin;
            var left = rect.left - pw + rect.width;
            // clamp to viewport
            if (top  < 8)                        top  = rect.bottom + margin;
            if (left < 8)                        left = 8;
            if (left + pw > window.innerWidth - 8) left = window.innerWidth - pw - 8;
            popup.style.position = 'fixed';
            popup.style.bottom   = 'auto';
            popup.style.right    = 'auto';
            popup.style.top      = top  + 'px';
            popup.style.left     = left + 'px';
        };

        function startDrag(x, y) {
            var rect = el.getBoundingClientRect();
            dragging = true; didDrag = false;
            el.style.cursor = 'grabbing';
            ox = x - rect.left; oy = y - rect.top;
        }
        function moveDrag(x, y) {
            if (!dragging) return;
            didDrag = true;
            var lx = Math.max(0, Math.min(window.innerWidth  - el.offsetWidth,  x - ox));
            var ly = Math.max(0, Math.min(window.innerHeight - el.offsetHeight, y - oy));
            el.style.right = 'auto'; el.style.bottom = 'auto';
            el.style.left = lx + 'px'; el.style.top = ly + 'px';
            window.syncPopupPosition();
        }
        function endDrag() {
            if (!dragging) return;
            dragging = false; el.style.cursor = 'pointer';
        }

        el.addEventListener('mousedown', function(e) { if (e.button !== 0) return; e.preventDefault(); startDrag(e.clientX, e.clientY); });
        window.addEventListener('mousemove', function(e) { moveDrag(e.clientX, e.clientY); });
        window.addEventListener('mouseup', endDrag);
        el.addEventListener('touchstart', function(e) { var t = e.touches[0]; startDrag(t.clientX, t.clientY); }, { passive: true });
        window.addEventListener('touchmove', function(e) { var t = e.touches[0]; moveDrag(t.clientX, t.clientY); }, { passive: true });
        window.addEventListener('touchend', endDrag);

        el.addEventListener('click', function(e) {
            if (didDrag) { didDrag = false; e.preventDefault(); e.stopImmediatePropagation(); }
        });
    });
})();
