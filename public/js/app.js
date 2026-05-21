/**
 * CRM Core Engine - Modular Architecture
 */
const CRMApp = {
    // 1. Configuration & Constants
    config: {
        mobileBreakpoint: 768,
        animDuration: 300,
        storageKey: 'crm_sidebar_collapsed',
        classes: {
            collapsed: 'collapsed',
            expanded: 'expanded',
            show: 'show',
            mobileOpen: 'mobile-open'
        }
    },

    // 2. Application State
    state: {
        isAnimating: false,
        get isMobile() {
            return window.innerWidth <= CRMApp.config.mobileBreakpoint;
        }
    },

    // 3. Initialize Everything
    init() {
        this.cacheElements();
        if (!this.dom.sidebar) return;

        this.initSidebarState();
        this.bindEvents();

        // ΝΕΟ: Αρχικοποίηση των settings toggles αν υπάρχουν στη σελίδα
        this.initSettingsToggles();

        console.log('CRM Engine Initialized 🚀');
    },

    // 4. DOM Cache - Μαζεμένα όλα τα selectors
    cacheElements() {
        this.dom = {
            body: document.body,
            sidebar: document.querySelector('.sidebar'),
            mainArea: document.querySelector('.main-area'),
            sidebarToggle: document.getElementById('sidebar-toggle'),
            userBtn: document.querySelector('.user-menu-btn'),
            userMenu: document.querySelector('.user-dropdown-menu'),

            // Language Section
            langBtn: document.getElementById('lang-menu-toggle'),
            langMenu: document.getElementById('lang-dropdown'),

            // Notifications
            notificationsBtn: document.getElementById('notifications-toggle-btn'),
            notificationsMenu: document.getElementById('notifications-dropdown'),
            notificationsList: document.getElementById('notifications-list'),
            notificationsBadge: document.getElementById('noti-badge-count'),
            markReadBtn: document.getElementById('mark-all-read-btn'),

            searchBar: document.querySelector('.top-bar-search input')
        };
    },

    // 5. Sidebar Logic
    initSidebarState() {
        const wasCollapsed = localStorage.getItem(this.config.storageKey) === 'true';

        // Εφαρμογή μόνο αν είμαστε σε desktop. Σε mobile ξεκινάμε πάντα closed.
        if (wasCollapsed && !this.state.isMobile) {
            this.setSidebarState(true);
        }
    },

    setSidebarState(collapsed) {
        const { sidebar, mainArea } = this.dom;
        const { classes } = this.config;

        if (collapsed) {
            sidebar.classList.add(classes.collapsed);
            mainArea?.classList.add(classes.expanded);
        } else {
            sidebar.classList.remove(classes.collapsed);
            mainArea?.classList.remove(classes.expanded);
        }
    },

    toggleSidebar() {
        if (this.state.isAnimating) return;

        // Mobile vs Desktop logic
        if (this.state.isMobile) {
            this.dom.sidebar.classList.toggle(this.config.classes.mobileOpen);
        } else {
            this.state.isAnimating = true;
            const isNowCollapsed = this.dom.sidebar.classList.toggle(this.config.classes.collapsed);
            this.dom.mainArea?.classList.toggle(this.config.classes.expanded);

            localStorage.setItem(this.config.storageKey, isNowCollapsed);

            setTimeout(() => this.state.isAnimating = false, this.config.animDuration);
        }
    },

    // 6. Event Listeners
    bindEvents() {
        // Sidebar Toggle (Desktop & Mobile)
        this.dom.sidebarToggle?.addEventListener('click', (e) => {
            e.preventDefault();
            this.toggleSidebar();
        });

        // Notifications Toggle
        this.dom.notificationsBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.dom.userMenu?.classList.remove(this.config.classes.show);
            this.dom.langMenu?.classList.remove(this.config.classes.show);
            this.dom.notificationsMenu?.classList.toggle(this.config.classes.show);
        });

        this.dom.markReadBtn?.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = this.dom.markReadBtn.getAttribute('data-url');

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    this.dom.notificationsBadge?.remove(); // Σβήνουμε το κόκκινο κυκλάκι
                    this.dom.notificationsList.innerHTML = `
                <div class="notifications-empty" style="padding: 20px; text-align: center; color: var(--text-muted);">
                    <p>Δεν υπάρχουν νέες ειδοποιήσεις</p>
                </div>`;
                    this.showToast('Όλες οι ειδοποιήσεις διαβάστηκαν!');
                }
            } catch (error) {
                this.showToast('Σφάλμα κατά την ενημέρωση', 'error');
            }
        });

        // Language Dropdown Toggle
        this.dom.langBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.dom.userMenu?.classList.remove(this.config.classes.show); // Κλείνουμε το άλλο
            this.dom.langMenu?.classList.toggle(this.config.classes.show);
        });

        // User Dropdown Toggle
        this.dom.userBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.dom.langMenu?.classList.remove(this.config.classes.show); // Κλείνουμε το άλλο
            this.dom.userMenu?.classList.toggle(this.config.classes.show);
        });

        // Ενοποιημένο Global Click handling
        window.addEventListener('click', (e) => {
            const { userMenu, userBtn, langMenu, langBtn, sidebar, sidebarToggle } = this.dom;
            const { show, mobileOpen } = this.config.classes;

            // Κλείσιμο User Menu αν κλικάρεις έξω
            if (userMenu?.classList.contains(show) && !userBtn.contains(e.target)) {
                userMenu.classList.remove(show);
            }

            // Κλείσιμο Language Menu αν κλικάρεις έξω
            if (langMenu?.classList.contains(show) && !langBtn.contains(e.target)) {
                langMenu.classList.remove(show);
            }

            // Κλείσιμο Notifications Menu αν κλικάρεις έξω
            if (this.dom.notificationsMenu?.classList.contains(show) && !this.dom.notificationsBtn.contains(e.target)) {
                this.dom.notificationsMenu.classList.remove(show);
            }

            // Mobile: Κλείσιμο sidebar αν κλικάρεις στο main area
            if (this.state.isMobile && sidebar?.classList.contains(mobileOpen)) {
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove(mobileOpen);
                }
            }
        });

        // Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.altKey && e.key.toLowerCase() === 'b') {
                this.toggleSidebar();
            }
            if (e.key === '/' && document.activeElement !== this.dom.searchBar) {
                e.preventDefault();
                this.dom.searchBar?.focus();
            }
        });

        // Resize Handler
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => this.handleResize(), 150);
        });
    },

    handleResize() {
        // Αν γυρίσουμε σε mobile, αφαιρούμε τα desktop classes για να μην "σπάσει" το layout
        if (this.state.isMobile) {
            this.setSidebarState(false);
        }
    },

    // --- 7. TOAST SYSTEM ---
    showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        // Επιλογή εικονιδίου βάσει τύπου
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Auto-remove
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    },

    // --- 8. SETTINGS & AJAX HANDLERS ---
    initSettingsToggles() {
        const toggles = document.querySelectorAll('.setting-toggle');
        toggles.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => this.handleSettingChange(e.target));
        });
    },

    async handleSettingChange(checkbox) {
        const settingName = checkbox.getAttribute('data-setting');
        const isChecked = checkbox.checked ? 1 : 0;
        const parentItem = checkbox.closest('.setting-item');

        // Visual loading state
        if (parentItem) parentItem.style.opacity = '0.5';

        try {
            const response = await fetch('/profile/settings/preferences', { // Βεβαιώσου ότι το URL είναι σωστό
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ [settingName]: isChecked })
            });

            const data = await response.json();

            if (response.ok) {
                this.showToast(data.message || 'Η ρύθμιση ενημερώθηκε!');
            } else {
                throw new Error();
            }
        } catch (error) {
            // Rollback αν αποτύχει
            checkbox.checked = !checkbox.checked;
            this.showToast('Σφάλμα κατά την αποθήκευση', 'error');
        } finally {
            if (parentItem) parentItem.style.opacity = '1';
        }
    },
};

// Initialization
document.addEventListener('DOMContentLoaded', () => CRMApp.init());