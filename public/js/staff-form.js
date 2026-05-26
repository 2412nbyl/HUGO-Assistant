/**
 * Staff create/edit — auto-fill fields when linking an existing user account.
 */
(function () {
    'use strict';

    function initStaffForm() {
        var select = document.getElementById('staff-id-user');
        if (!select || !window.STAFF_USER_MAP) return;

        var map = window.STAFF_USER_MAP;
        var fields = {
            name: document.querySelector('form#staff-form [name="name"]'),
            email: document.querySelector('form#staff-form [name="email"]'),
            position: document.querySelector('form#staff-form [name="position"]'),
            work_status: document.querySelector('form#staff-form [name="work_status"]'),
        };

        var roleLabels = {
            admin: 'Administrator',
            notaris: 'Notaris',
            staff: 'Staff Operasional',
            freelancer: 'Freelancer',
        };

        function fillFromUser(userId) {
            if (!userId || !map[userId]) return;
            var u = map[userId];
            if (fields.name) fields.name.value = u.name || '';
            if (fields.email) fields.email.value = u.email || '';
            if (fields.position) fields.position.value = u.position || roleLabels[u.role] || '';
            if (fields.work_status) {
                fields.work_status.value = 'Aktif';
                if (window.HugoPopupSelect) {
                    HugoPopupSelect.refresh(fields.work_status.closest('.popup-select-wrap') || fields.work_status.parentNode);
                }
            }
        }

        select.addEventListener('change', function () {
            var id = select.value;
            if (!id) {
                if (fields.name) fields.name.value = '';
                if (fields.email) fields.email.value = '';
                if (fields.position) fields.position.value = '';
                return;
            }
            fillFromUser(id);
        });

        if (select.value) {
            fillFromUser(select.value);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStaffForm);
    } else {
        initStaffForm();
    }
})();
