{{-- Floating Chat Bubble + Mini Popup (hidden when on chat page) --}}
@if(!request()->is('chat*'))
<div id="chat-bubble" title="Buka Chat" onclick="toggleChatPopup()">
    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
    <span id="chat-bubble-badge" class="bubble-badge" style="display:none;">0</span>
</div>
@endif

{{-- ─── MINI CHAT POPUP ─── --}}
<div id="chat-popup" role="dialog" aria-label="Mini Chat">
    <div class="cpp-header">
        <div class="cpp-header-left">
            <span class="cpp-icon">💬</span>
            <div>
                <div class="cpp-title">Live Chat</div>
                <div class="cpp-sub" id="cpp-online-txt">Team HUGO</div>
            </div>
        </div>
        <div class="cpp-header-actions">
            <a href="{{ url('/chat') }}" class="cpp-fullscreen-btn" title="Buka halaman Chat penuh">
                <svg viewBox="0 0 24 24"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
            </a>
            <button class="cpp-close-btn" onclick="closeChatPopup()" title="Tutup">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <div class="cpp-msgs" id="popup-chat-area">
        <div class="cpp-system-msg">Memuat percakapan...</div>
    </div>

    <div class="cpp-input-bar">
        <input type="text" id="popup-chat-input"
               placeholder="Ketik pesan..."
               autocomplete="off"
               onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); sendPopupMsg(); }">
        <button id="popup-send-btn" onclick="sendPopupMsg()" title="Kirim">
            <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
        </button>
    </div>
</div>
