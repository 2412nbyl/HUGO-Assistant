/**
 * Keeps filter pills in sync with search inputs and select fields (month/year, etc.)
 */
(function () {
    function mergeFormIntoUrl(pillHref, form) {
        var url = new URL(pillHref, window.location.origin);

        var search = form.querySelector('input[name="search"]');
        if (search && search.value.trim()) {
            url.searchParams.set('search', search.value.trim());
        } else {
            url.searchParams.delete('search');
        }

        form.querySelectorAll('select[name]').forEach(function (sel) {
            if (sel.value) {
                url.searchParams.set(sel.name, sel.value);
            }
        });

        return url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : '');
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.filter-bar .pill[href], .filter-card .pill[href]').forEach(function (pill) {
            pill.addEventListener('click', function (e) {
                var bar = this.closest('.filter-bar, .filter-card');
                var form = bar ? bar.querySelector('form') : null;
                if (!form) return;

                e.preventDefault();
                window.location.href = mergeFormIntoUrl(this.getAttribute('href'), form);
            });
        });

        document.querySelectorAll('.filter-bar form, .filter-card form').forEach(function (form) {
            form.addEventListener('submit', function () {
                var search = form.querySelector('input[name="search"]');
                if (search && !search.value.trim()) {
                    search.removeAttribute('name');
                }
            });

            var searchInput = form.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (typeof form.requestSubmit === 'function') {
                            form.requestSubmit();
                        } else {
                            form.submit();
                        }
                    }
                });
            }
        });
    });
})();
