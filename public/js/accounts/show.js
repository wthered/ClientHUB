// 1. GLOBAL MODAL FUNCTIONS (Έξω από το DOMContentLoaded)
window.openContactModal = function() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'flex';
        const input = document.getElementById('first_name');
        if (input) setTimeout(() => input.focus(), 100);
    }
};

window.closeContactModal = function() {
    const modal = document.getElementById('contactModal');
    if (modal) modal.style.display = 'none';
};

document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    // --- TABS LOGIC ---
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('data-tab');
            const targetPane = document.getElementById(targetId);
            if (!targetPane) return;

            tabLinks.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));

            this.classList.add('active');
            targetPane.classList.add('active');

            if (targetPane.dataset.loaded !== 'true' && targetId !== 'overview') {
                loadTabContent(targetId, targetPane);
            }
            history.pushState(null, null, `#${targetId}`);
        });
    });

    // --- HASH CHECK ---
    const hash = window.location.hash.replace('#', '');
    if (hash) {
        const targetTab = document.querySelector(`[data-tab="${hash}"]`);
        if (targetTab) targetTab.click();
    }

    function loadTabContent(type, container) {
        const grid = document.querySelector('.account-content-grid');
        const accountId = grid ? grid.dataset.accountId : null;
        if (!accountId) return;

        container.innerHTML = '<div class="loading-spinner">Φόρτωση...</div>';

        fetch(`/accounts/${accountId}/${type}`).then(function (response) {
            return response.text();
        }).then(html => {
            container.innerHTML = html;
            container.dataset.loaded = 'true';
        }).catch(() => {
            container.innerHTML = '<p>Αποτυχία φόρτωσης.</p>';
        });
    }

    // --- 3. MODAL LOGIC ---
    document.addEventListener('click', function(e) {
        console.log(e.target);
        // Ψάχνουμε αν το στοιχείο που πατήθηκε (ή ο γονέας του) έχει το ID μας
        const addBtn = e.target.closest('#addContactBtn');

        if (addBtn) {
            e.preventDefault();
            window.openContactModal();
        }

        // Κλείσιμο αν πατηθεί το overlay
        if (e.target.id === 'contactModal') {
            window.closeContactModal();
        }
    });
});