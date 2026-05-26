{{-- HUGO Config: stored in hidden element data-attributes, read by JS below --}}
<div id="hugo-config" style="display:none;"
    data-csrf="{{ csrf_token() }}"
    data-chat-send="{{ url('/chat') }}"
    data-chat-poll="{{ url('/chat/poll') }}"
    data-chat-url="{{ url('/chat') }}"
    data-birthday-url="{{ url('/birthdays/today') }}"
    data-avatar-url="{{ url('/profile/avatar') }}"
    data-password-url="{{ url('/profile/password') }}"
    data-user-id="{{ auth()->id() }}"
    data-is-chat="{{ request()->is('chat*') ? '1' : '0' }}"
    data-base-url="{{ url('/') }}"
></div>
<script>
(function(){
    var c = document.getElementById('hugo-config');
    var d = c ? c.dataset : {};
    window.HUGO_CONFIG = {
        csrf:        d.csrf        || '',
        chatSendUrl: d.chatSend   || '',
        chatPollUrl: d.chatPoll   || '',
        chatUrl:     d.chatUrl    || '',
        birthdayUrl: d.birthdayUrl|| '',
        avatarUrl:   d.avatarUrl  || '',
        passwordUrl: d.passwordUrl|| '',
        userId:      d.userId || '',
        isChatPage:  d.isChat === '1',
        baseUrl:     d.baseUrl    || '',
    };
})();
function esc(t){if(!t)return'';return String(t).replace(/[&<>"']/g,function(s){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]);});}
</script>

<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
<script src="{{ asset('js/list-filters.js') }}?v={{ filemtime(public_path('js/list-filters.js')) }}"></script>
<script src="{{ asset('js/list-search.js') }}?v={{ filemtime(public_path('js/list-search.js')) }}"></script>
<script src="{{ asset('js/popup-select.js') }}?v={{ filemtime(public_path('js/popup-select.js')) }}"></script>

<script>
/* ─── Skeleton / Loader ─────────────────────────────────────────────── */
(function(){
    var skeleton = document.getElementById('global-skeleton');
    var content  = document.getElementById('main-content-yield');
    function hide(){
        if(!skeleton) return;
        skeleton.classList.remove('show'); skeleton.style.display='none';
        if(content){ content.style.visibility='visible'; content.style.opacity='1'; }
    }
    window.addEventListener('load', hide);
    setTimeout(hide, 1500); // fallback

    @if(session('just_logged_in'))
    var ldr = document.getElementById('page-loader');
    if(ldr){ ldr.classList.add('show'); setTimeout(function(){ ldr.style.opacity='0'; setTimeout(function(){ ldr.classList.remove('show'); ldr.style.opacity=''; },300); },1200); }
    @else
    var ldr2 = document.getElementById('page-loader');
    if(ldr2){ ldr2.classList.remove('show'); ldr2.style.display='none'; }
    @endif
})();


/* ─── Toast ──────────────────────────────────────────────────────────── */
function showToast(msg, type, title){
    var c=document.getElementById('toast-container'); if(!c) return;
    var cfg={
        success:{ icon:'✔', bg:'#14532d', border:'#22c55e', title:'Berhasil' },
        danger: { icon:'✘', bg:'#450a0a', border:'#ef4444', title:'Gagal'    },
        warning:{ icon:'⚠', bg:'#451a03', border:'#f59e0b', title:'Peringatan'},
        info:   { icon:'ℹ', bg:'#0c1a3a', border:'#3b82f6', title:'Info'     }
    };
    var s=cfg[type]||cfg.info;
    var t=document.createElement('div');
    t.className='toast'; t.setAttribute('data-type',type||'info');
    t.style.cssText='background:'+s.bg+';border-left:4px solid '+s.border+';';
    t.innerHTML=
        '<span class="toast-icon">'+s.icon+'</span>'
        +'<div class="toast-text">'
        +  '<div class="toast-title">'+(title||s.title)+'</div>'
        +  '<div class="toast-body">'+esc(msg)+'</div>'
        +'</div>';
    c.appendChild(t);
    setTimeout(function(){ t.style.transition='opacity 0.35s,transform 0.35s'; t.style.opacity='0'; t.style.transform='translateY(-8px) scale(0.96)'; setTimeout(function(){ t.remove(); },380); },4200);
}

function showChatToast(name, message, isPrivate){
    var c=document.getElementById('toast-container'); if(!c) return;
    var t=document.createElement('div'); t.className='chat-toast';
    t.innerHTML='<div class="chat-toast-avatar">'+esc(name).charAt(0).toUpperCase()+'</div>'
        +'<div class="chat-toast-body">'
        +  '<div class="chat-toast-name">'+(isPrivate?'[Pribadi] Pesan dari ':'')+esc(name)+'</div>'
        +  '<div class="chat-toast-msg">'+esc(message)+'</div>'
        +'</div>'
        +'<div style="font-size:10px;color:#6b7280;flex-shrink:0;align-self:flex-start;margin-top:2px;">CHAT</div>';
    t.onclick=function(){ window.location.href=window.HUGO_CONFIG.chatUrl; };
    c.appendChild(t);
    setTimeout(function(){ t.style.transition='opacity 0.35s,transform 0.35s'; t.style.opacity='0'; t.style.transform='translateY(-8px) scale(0.96)'; setTimeout(function(){ t.remove(); },380); },4700);
}

// Confirm modal
function showConfirm(title, msg, onOk, icon, onCancel){
    var overlay=document.getElementById('confirm-modal');
    var okBtn  =document.getElementById('confirm-ok-btn');
    document.getElementById('confirm-title').textContent   = title||'Konfirmasi';
    document.getElementById('confirm-message').innerHTML   = msg||'';
    document.getElementById('confirm-icon').textContent    = icon||'!';
    overlay.classList.add('open');
    var newBtn=okBtn.cloneNode(true); okBtn.parentNode.replaceChild(newBtn,okBtn);
    newBtn.addEventListener('click',function(){ overlay.classList.remove('open'); if(typeof onOk==='function') onOk(); });
    overlay.onclick=function(e){ if(e.target===overlay){ overlay.classList.remove('open'); if(typeof onCancel==='function') onCancel(); } };
}
function closeConfirm(){ var o=document.getElementById('confirm-modal'); if(o) o.classList.remove('open'); }


/* ─── Chat Notification Badge Poller ────────────────────────────────── */
(function(){
    if(!window.HUGO_CONFIG) return;

    function getReadId(){ return parseInt(localStorage.getItem('hugo_read_id') || '0'); }
    function getNotifiedId(){
        var id = parseInt(localStorage.getItem('hugo_notified_id') || '0');
        if (id === 0) {
            id = getReadId();
        }
        return id;
    }
    function setNotifiedId(id){
        localStorage.setItem('hugo_notified_id', id);
    }

    // If we're ON the chat page, mark everything as read immediately
    if(window.HUGO_CONFIG.isChatPage){
        var navB=document.getElementById('chat-nav-badge');
        var mobB=document.getElementById('mob-nav-badge-chat');
        if(navB) navB.classList.remove('show');
        if(mobB) mobB.style.display='none';
        
        // Ensure global state knows we've seen messages on this page
        var lastIdOnPage = parseInt(window.lastMsgId || '0');
        if(lastIdOnPage > getReadId()) localStorage.setItem('hugo_read_id', lastIdOnPage);
        return; 
    }

    function poll(){
        if(document.hidden) return;
        var notifyId = getNotifiedId();
        var reads = localStorage.getItem('hugo_chat_reads') || '{}';
        
        fetch(window.HUGO_CONFIG.chatPollUrl+'?last_id='+notifyId+'&badge=1&reads='+encodeURIComponent(reads), {headers:{'ngrok-skip-browser-warning':'true'}})
        .then(function(r){ return r.json(); })
        .then(function(data){
            var unread=data.unread||0;
            var navB=document.getElementById('chat-nav-badge');
            var topB=document.getElementById('topbar-chat-badge');
            var mobB=document.getElementById('mob-nav-badge-chat');
            
            if(navB){ if(unread>0){navB.textContent=unread>99?'99+':unread;navB.classList.add('show');}else{navB.classList.remove('show');} }
            if(topB){ if(unread>0){topB.textContent=unread>99?'99+':unread;topB.classList.add('show');}else{topB.classList.remove('show');} }
            if(mobB){ if(unread>0){mobB.textContent=unread>99?'99+':unread;mobB.style.display='flex';}else{mobB.style.display='none';} }
            
            if(data.latest&&data.latest.length){
                var maxId = notifyId;
                data.latest.forEach(function(m){ 
                    if(m.id > notifyId){ 
                        showChatToast(m.name,m.message,m.is_private); 
                        if (m.id > maxId) maxId = m.id;
                    } 
                });
                if (maxId > notifyId) {
                    setNotifiedId(maxId);
                }
            }
            if (data.last_id && data.last_id > notifyId) {
                setNotifiedId(data.last_id);
            }
        }).catch(function(){});
    }
    poll(); setInterval(poll,15000);
})();


    /* ─── Global Modal Scroll Lock ─── */
    const modalObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.attributeName === 'class') {
                const anyOpen = document.querySelector('.modal-overlay.open');
                document.body.classList.toggle('modal-open', !!anyOpen);
            }
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(m => {
        modalObserver.observe(m, { attributes: true });
    });

    window.dismissBirthdayReminder = function() {
        const until = new Date();
        until.setDate(until.getDate() + 30);
        localStorage.setItem('hugo_bday_ignore_until', until.toISOString());
        const modal = document.getElementById('birthday-modal');
        if (modal) modal.classList.remove('open');
        showToast('Pengingat dimatikan selama 30 hari.', 'info');
    };

    (function(){
        if(!window.HUGO_CONFIG) return;

        var bdayPromise = fetch(window.HUGO_CONFIG.birthdayUrl, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json','ngrok-skip-browser-warning':'true'} })
            .then(function(r){ return r.ok ? r.json() : []; });

        function checkAndShow() {
            bdayPromise.then(function(data){
                if(!data||!data.length) return;
                var badge=document.getElementById('case-birthday-badge');
                if(badge) badge.style.display='flex';
                
                // Check snooze
                const ignoreUntil = localStorage.getItem('hugo_bday_ignore_until');
                if (ignoreUntil && new Date(ignoreUntil) > new Date()) return;

                // Show modal once per "login session" if on dashboard
                if(!sessionStorage.getItem('hugo_bday_shown_today')){
                    if (!window.location.pathname.includes('/dashboard')) return;

                    var list=document.getElementById('birthday-list'); if(!list) return;
                    list.innerHTML=data.map(function(b){
                        var isToday = b.is_today;
                        var crownHtml = isToday ? '<span class="bday-crown">👑</span>' : '';
                        var tagClass = isToday ? 'bday-tag today' : 'bday-tag upcoming';
                        var tagText = isToday ? 'Hari Ini 🎂' : b.days_until + ' hari lagi 🎈';
                        var avatarClass = isToday ? 'bday-avatar is-today' : 'bday-avatar';

                        return '<div class="bday-card-item">'
                            + '<div class="' + avatarClass + '">'
                            +   crownHtml
                            +   esc(b.client_name).charAt(0).toUpperCase()
                            + '</div>'
                            + '<div class="bday-info">'
                            +   '<div class="bday-client-name">' + esc(b.client_name) + '</div>'
                            +   '<div class="bday-case-name">' + esc(b.case_name || 'Kasus') + '</div>'
                            + '</div>'
                            + '<div class="bday-badge-wrap">'
                            +   '<span class="' + tagClass + '">' + tagText + '</span>'
                            +   '<span class="bday-date">' + esc(b.birth_date) + '</span>'
                            + '</div>'
                            + '</div>';
                    }).join('');
                    var modal=document.getElementById('birthday-modal');
                    if(modal) modal.classList.add('open');
                    sessionStorage.setItem('hugo_bday_shown_today','1');
                }
            }).catch(function(){});
        }

        if (document.readyState === 'complete') {
            checkAndShow();
        } else {
            window.addEventListener('load', checkAndShow);
        }
    })();


/* ─── Chat Mini Popup ────────────────────────────────────────────────── */
window._cppOpen=false; window._cppLastId=0; window._cppTimer=null;

window.toggleChatPopup=function(){ window._cppOpen?window.closeChatPopup():window.openChatPopup(); };

window.openChatPopup=function(){
    if(window._cppOpen) return; window._cppOpen=true;
    var p=document.getElementById('chat-popup'); if(!p) return;
    p.classList.add('open');
    var inp=document.getElementById('popup-chat-input');
    if(inp) setTimeout(function(){ inp.focus(); },100);
    _popupLoadHistory(); _popupStartPoll();
};

window.closeChatPopup=function(){
    window._cppOpen=false;
    var p=document.getElementById('chat-popup'); if(p) p.classList.remove('open');
    if(window._cppTimer){ clearInterval(window._cppTimer); window._cppTimer=null; }
};

function getReadId(){ return parseInt(localStorage.getItem('hugo_read_id') || '0'); }

function _popupLoadHistory(){
    var area=document.getElementById('popup-chat-area'); if(!area) return;
    area.innerHTML='<div class="cpp-system-msg">Memuat...</div>';
    fetch(window.HUGO_CONFIG.chatPollUrl+'?last_id=0', {
        headers:{'ngrok-skip-browser-warning':'true','Accept':'application/json'}
    })
    .then(function(r){ 
        if(!r.ok) throw new Error('HTTP ' + r.status);
        return r.json(); 
    })
    .then(function(data){
        var msgs=(data.messages||[]).slice(-50);
        area.innerHTML=msgs.length?'':'<div class="cpp-system-msg">Belum ada pesan. Mulai percakapan!</div>';
        msgs.forEach(function(m){ _popupAppend(m); });
        window._cppLastId=data.last_id||0; 
        area.scrollTop=area.scrollHeight;
        
        // Mark as read in global state
        var rId = getReadId();
        if(window._cppLastId > rId) localStorage.setItem('hugo_read_id', window._cppLastId);
    }).catch(function(err){ 
        console.error('Popup Load Error:', err);
        area.innerHTML='<div class="cpp-system-msg">Gagal memuat pesan.</div>'; 
    });
}

function _popupAppend(m){
    var area=document.getElementById('popup-chat-area'); if(!area) return;
    var isMe=m.sender&&m.sender.id===window.HUGO_CONFIG.userId;
    var d=document.createElement('div');
    d.className='mini-msg '+(isMe?'mine':'theirs');
    d.innerHTML='<div class="mini-name '+(isMe?'mine':'theirs')+'">'+esc(m.sender?m.sender.name:'')+'</div>'+esc(m.message);
    area.appendChild(d); area.scrollTop=area.scrollHeight;
}

window.sendPopupMsg=function(){
    var inp=document.getElementById('popup-chat-input');
    var btn=document.getElementById('popup-send-btn');
    var txt=inp?inp.value.trim():''; if(!txt) return;
    inp.value=''; if(btn) btn.disabled=true;
    fetch(window.HUGO_CONFIG.chatSendUrl,{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.HUGO_CONFIG.csrf,'ngrok-skip-browser-warning':'true','Accept':'application/json'},
        body:JSON.stringify({message:txt})
    }).then(function(r){ return r.json(); })
    .then(function(res){ if(res.success){ _popupAppend(res.message); window._cppLastId=res.message.id; } })
    .catch(function(){ showToast('Gagal mengirim pesan','danger'); })
    .finally(function(){ if(btn) btn.disabled=false; });
};

function _popupStartPoll(){
    if(window._cppTimer) return;
    window._cppTimer=setInterval(function(){
        if(!window._cppOpen||document.hidden) return;
        fetch(window.HUGO_CONFIG.chatPollUrl+'?last_id='+window._cppLastId, {
            headers:{'ngrok-skip-browser-warning':'true','Accept':'application/json'}
        })
        .then(function(r){ if(!r.ok) return {messages:[]}; return r.json(); })
        .then(function(data){
            if(data.messages&&data.messages.length){
                data.messages.filter(function(m){ return !m.sender||m.sender.id!==window.HUGO_CONFIG.userId; })
                             .forEach(function(m){ _popupAppend(m); });
                window._cppLastId=data.last_id;
                
                // Mark as read while popup is open
                localStorage.setItem('hugo_read_id', window._cppLastId);
            }
        }).catch(function(){});
    },8000);
}

/* Profile modal helpers */
function openProfileModal(){ var m=document.getElementById('profile-modal'); if(m) m.classList.add('open'); }
function closeProfileModal(){ var m=document.getElementById('profile-modal'); if(m) m.classList.remove('open'); }
function closeDynamicModal(){ var m=document.getElementById('dynamic-modal'); if(m) m.classList.remove('open'); }


/* ─── Hamburger ───────────────────────────────────────────────────────── */
(function(){
    var b=document.getElementById('hamburger');
    var s=document.querySelector('.sidebar');
    var o=document.getElementById('sidebar-overlay');
    if(!b||!s) return;
    b.onclick=function(){ s.classList.toggle('open'); if(o) o.classList.toggle('show'); };
    if(o) o.onclick=function(){ s.classList.remove('open'); o.classList.remove('show'); };
})();
</script>
