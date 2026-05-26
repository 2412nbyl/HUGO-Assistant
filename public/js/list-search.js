/**
 * Client-side list filtering for search inputs.
 * Usage: <input data-list-search="#chat-contact-list .user-list-item">
 * Items need data-search="..." or are matched against textContent.
 */
(function () {
    'use strict';

    function normalize(s) {
        return String(s || '').toLowerCase().trim();
    }

    function filterList(input) {
        var selector = input.getAttribute('data-list-search');
        if (!selector) return;

        var query = normalize(input.value);
        document.querySelectorAll(selector).forEach(function (item) {
            var hay = normalize(item.getAttribute('data-search') || item.textContent);
            var hide = query.length > 0 && hay.indexOf(query) === -1;
            item.classList.toggle('is-filtered-hidden', hide);
        });
    }

    function bindInput(input) {
        if (input.dataset.listSearchBound === '1') return;
        input.dataset.listSearchBound = '1';

        var run = function () { filterList(input); };
        input.addEventListener('input', run);
        input.addEventListener('search', run);
        input.addEventListener('keyup', run);

        var id = input.id;
        if (id) {
            window['filterList_' + id.replace(/-/g, '_')] = run;
        }
    }

    function initIn(root) {
        (root || document).querySelectorAll('input[data-list-search]').forEach(bindInput);
    }

    window.HugoListSearch = { init: initIn, filter: filterList };

    document.addEventListener('DOMContentLoaded', function () {
        initIn(document);
    });
})();
