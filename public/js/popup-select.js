/**
 * Converts native <select> elements into bottom-sheet style popup pickers.
 * Preserves form submission and existing onchange handlers.
 */
(function () {
    'use strict';

    var overlay = null;
    var sheet = null;

    function getSelectedLabel(select) {
        var opt = select.options[select.selectedIndex];
        return opt ? opt.textContent.trim() : 'Pilih…';
    }

    function buildTrigger(select) {
        select.dataset.popupSelect = 'init';

        var wrap = document.createElement('div');
        wrap.className = 'popup-select-wrap';

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'popup-select-trigger';
        btn.setAttribute('aria-haspopup', 'listbox');
        btn.disabled = select.disabled;

        var label = document.createElement('span');
        label.className = 'popup-select-label';
        label.textContent = getSelectedLabel(select);

        var chevron = document.createElement('span');
        chevron.className = 'popup-select-chevron';
        chevron.innerHTML = '<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>';

        btn.appendChild(label);
        btn.appendChild(chevron);
        btn.addEventListener('click', function () {
            if (!select.disabled) openPicker(select);
        });

        select.classList.add('popup-select-native');
        if (select.parentNode) {
            select.parentNode.insertBefore(wrap, select);
        }
        wrap.appendChild(btn);
        wrap.appendChild(select);

        select._popupLabel = label;
        select._popupTrigger = btn;
    }

    function ensureOverlay() {
        if (overlay) return;
        overlay = document.createElement('div');
        overlay.className = 'popup-select-overlay';
        overlay.setAttribute('role', 'presentation');
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closePicker();
        });

        sheet = document.createElement('div');
        sheet.className = 'popup-select-sheet';
        sheet.setAttribute('role', 'dialog');
        sheet.setAttribute('aria-modal', 'true');
        sheet.addEventListener('click', function (e) { e.stopPropagation(); });

        overlay.appendChild(sheet);
        document.body.appendChild(overlay);
    }

    function closePicker() {
        if (!overlay) return;
        overlay.classList.remove('open');
        setTimeout(function () {
            if (!overlay.classList.contains('open')) sheet.innerHTML = '';
        }, 280);
    }

    function openPicker(select) {
        ensureOverlay();

        var title = select.getAttribute('data-popup-title');
        if (!title) {
            var row = select.closest('.form-row, .form-group');
            var lbl = row ? row.querySelector('label') : null;
            title = lbl ? lbl.textContent.trim() : 'Pilih opsi';
        }

        var header = document.createElement('div');
        header.className = 'popup-select-sheet-header';

        var titleEl = document.createElement('span');
        titleEl.className = 'popup-select-sheet-title';
        titleEl.textContent = title;

        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'popup-select-close';
        closeBtn.setAttribute('aria-label', 'Tutup');
        closeBtn.textContent = '×';
        closeBtn.addEventListener('click', closePicker);

        header.appendChild(titleEl);
        header.appendChild(closeBtn);

        var list = document.createElement('div');
        list.className = 'popup-select-options';
        list.setAttribute('role', 'listbox');

        Array.prototype.forEach.call(select.options, function (opt) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'popup-select-option'
                + (opt.selected ? ' is-selected' : '')
                + (opt.disabled ? ' is-disabled' : '');
            item.textContent = opt.textContent.trim();
            item.disabled = opt.disabled;

            if (!opt.disabled) {
                item.addEventListener('click', function () {
                    var prev = select.value;
                    select.value = opt.value;
                    if (select._popupLabel) select._popupLabel.textContent = getSelectedLabel(select);
                    list.querySelectorAll('.popup-select-option').forEach(function (el) {
                        el.classList.toggle('is-selected', el === item);
                    });
                    closePicker();
                    if (prev !== opt.value) {
                        select.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
            list.appendChild(item);
        });

        sheet.innerHTML = '';
        sheet.appendChild(header);
        sheet.appendChild(list);

        overlay.classList.add('open');
        var first = list.querySelector('.popup-select-option:not(.is-disabled)');
        if (first) first.focus();
    }

    function initIn(root) {
        (root || document).querySelectorAll('select').forEach(function (select) {
            if (select.dataset.popupSelect === 'init') return;
            if (select.dataset.nativeSelect === 'true') return;
            if (select.multiple) return;
            buildTrigger(select);
        });
    }

    function refresh(root) {
        initIn(root || document);
        (root || document).querySelectorAll('select[data-popup-select="init"]').forEach(function (select) {
            if (select._popupLabel) select._popupLabel.textContent = getSelectedLabel(select);
            if (select._popupTrigger) select._popupTrigger.disabled = select.disabled;
        });
    }

    window.HugoPopupSelect = { init: initIn, refresh: refresh, close: closePicker };

    function watchModals() {
        document.querySelectorAll('.modal-overlay').forEach(function (modal) {
            if (modal.dataset.popupSelectWatch === '1') return;
            modal.dataset.popupSelectWatch = '1';
            var obs = new MutationObserver(function (mutations) {
                var classChanged = Array.prototype.some.call(mutations, function(m) {
                    return m.attributeName === 'class';
                });
                if (classChanged && modal.classList.contains('open')) {
                    refresh(modal);
                }
            });
            obs.observe(modal, { attributes: true, attributeFilter: ['class'], childList: false, subtree: false });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initIn(document);
        watchModals();

        var observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    refresh(node);
                    if (node.matches && node.matches('.modal-overlay')) watchModals();
                    if (node.querySelectorAll) {
                        node.querySelectorAll('.modal-overlay').forEach(function () { watchModals(); });
                    }
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
})();
