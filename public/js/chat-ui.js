/**
 * Chat page: contact search, unread badges, scroll-to-unread / bottom.
 */
(function () {
    'use strict';

    if (!window.HUGO_CHAT) return;

    var cfg = window.HUGO_CHAT;
    var myUserId = cfg.userId;
    var receiverId = cfg.receiverId;
    var isGlobalChat = cfg.isGlobalChat;
    var lastMsgId = cfg.lastMsgId || 0;

    window.lastMsgId = lastMsgId;

    function getChatReads() {
        try { return JSON.parse(localStorage.getItem('hugo_chat_reads') || '{}'); }
        catch (e) { return {}; }
    }

    function getChatReadKey() {
        if (receiverId) return String(receiverId);
        if (isGlobalChat) return 'global';
        return null;
    }

    function markChatRead(key, msgId) {
        if (!key || !msgId) return;
        var reads = getChatReads();
        var cur = parseInt(reads[key] || 0, 10);
        if (msgId > cur) {
            reads[key] = msgId;
            localStorage.setItem('hugo_chat_reads', JSON.stringify(reads));
        }
        var globalRead = parseInt(localStorage.getItem('hugo_read_id') || '0', 10);
        if (msgId > globalRead) localStorage.setItem('hugo_read_id', msgId);
    }

    function updateContactBadge(key, count) {
        if (!key) return;
        var item = document.querySelector('#chat-contact-list .user-list-item[data-contact-key="' + key + '"]');
        if (!item) return;
        var dot = item.querySelector('.chat-unread-dot');
        if (!dot) return;
        if (count > 0) {
            dot.hidden = false;
            dot.textContent = count > 99 ? '99+' : count;
            dot.setAttribute('aria-label', count > 1 ? count + ' pesan baru' : 'Pesan baru');
            item.classList.add('has-unread');
        } else {
            dot.hidden = true;
            dot.textContent = '';
            dot.removeAttribute('aria-label');
            item.classList.remove('has-unread');
        }
    }

    function applyContactBadges(badges) {
        if (!badges) return;
        Object.keys(badges).forEach(function (key) {
            updateContactBadge(key, badges[key] || 0);
        });
    }

    function pollContactBadges() {
        var reads = getChatReads();
        var url = window.HUGO_CONFIG.chatPollUrl + '?contact_badges=1&reads=' + encodeURIComponent(JSON.stringify(reads));
        fetch(url, { headers: { 'ngrok-skip-browser-warning': 'true' } })
            .then(function (r) { return r.json(); })
            .then(function (data) { applyContactBadges(data.badges); })
            .catch(function () {});
    }

    function scrollBottom(force) {
        var el = document.getElementById('chat-msgs-area');
        if (!el) return;
        var atBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 150;
        if (force || atBottom) {
            requestAnimationFrame(function () {
                el.scrollTop = el.scrollHeight;
            });
        }
    }

    function scrollToFirstUnread() {
        var el = document.getElementById('chat-msgs-area');
        if (!el) return false;
        var chatKey = getChatReadKey();
        if (!chatKey) return false;
        var lastRead = parseInt(getChatReads()[chatKey] || 0, 10);
        var rows = el.querySelectorAll('.msg-row[data-id]');
        var target = null;
        for (var i = 0; i < rows.length; i++) {
            var id = parseInt(rows[i].getAttribute('data-id'), 10);
            if (id > lastRead) {
                target = rows[i];
                break;
            }
        }
        if (!target) return false;
        requestAnimationFrame(function () {
            target.scrollIntoView({ block: 'start', behavior: 'auto' });
        });
        return true;
    }

    function initChatScroll() {
        var chatKey = getChatReadKey();
        if (!chatKey) return;
        var hasUnread = scrollToFirstUnread();
        if (!hasUnread) scrollBottom(true);
        if (lastMsgId) {
            setTimeout(function () {
                markChatRead(chatKey, lastMsgId);
                updateContactBadge(chatKey, 0);
            }, 400);
        }
    }

    window.filterChatContacts = function (query) {
        var input = document.getElementById('chat-contact-search');
        if (input && typeof query === 'string' && input.value !== query) {
            input.value = query;
        }
        if (window.HugoListSearch && input) {
            window.HugoListSearch.filter(input);
        }
    };

    window.filterNewChat = function () {
        var input = document.getElementById('new-chat-search');
        if (input && window.HugoListSearch) HugoListSearch.filter(input);
    };

    window.openNewChatModal = function () {
        var modal = document.getElementById('new-chat-modal');
        if (modal) modal.classList.add('open');
        if (window.HugoListSearch) HugoListSearch.init(modal);
        setTimeout(function () {
            var s = document.getElementById('new-chat-search');
            if (s) s.focus();
        }, 100);
    };

    window.goChatList = function () {
        window.location.href = cfg.listUrl;
    };

    if (cfg.initialBadges) {
        applyContactBadges(cfg.initialBadges);
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.HugoListSearch) window.HugoListSearch.init(document);
        // Always force-scroll to bottom on initial page load
        var msgArea = document.getElementById('chat-msgs-area');
        if (msgArea) {
            // Disable smooth scroll momentarily for instant jump
            var prevBehavior = msgArea.style.scrollBehavior;
            msgArea.style.scrollBehavior = 'auto';
            msgArea.scrollTop = msgArea.scrollHeight;
            // Double-tap after layout settles
            requestAnimationFrame(function () {
                msgArea.scrollTop = msgArea.scrollHeight;
                setTimeout(function () {
                    msgArea.scrollTop = msgArea.scrollHeight;
                    msgArea.style.scrollBehavior = prevBehavior;
                }, 150);
            });
        }
        if (getChatReadKey()) {
            if (lastMsgId) {
                setTimeout(function () {
                    markChatRead(getChatReadKey(), lastMsgId);
                    updateContactBadge(getChatReadKey(), 0);
                }, 400);
            }
        }
        pollContactBadges();
    });

    if (document.readyState !== 'loading' && getChatReadKey()) {
        // Already loaded — scroll immediately
        var msgArea2 = document.getElementById('chat-msgs-area');
        if (msgArea2) {
            msgArea2.style.scrollBehavior = 'auto';
            msgArea2.scrollTop = msgArea2.scrollHeight;
            requestAnimationFrame(function () { msgArea2.scrollTop = msgArea2.scrollHeight; });
        }
    }

    setInterval(pollContactBadges, 5000);

    window.HugoChatUi = {
        getChatReadKey: getChatReadKey,
        markChatRead: markChatRead,
        updateContactBadge: updateContactBadge,
        scrollBottom: scrollBottom,
        pollContactBadges: pollContactBadges,
        get lastMsgId() { return lastMsgId; },
        set lastMsgId(v) { lastMsgId = v; window.lastMsgId = v; }
    };
})();
