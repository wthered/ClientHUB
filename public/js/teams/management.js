/**
 * Team Management Actions
 */

document.addEventListener('DOMContentLoaded', function() {
    const teamMain = document.querySelector('.team-main');

    // --- 1. Vanilla Toast Notification Definition ---
    window.showToast = function(message) {
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'custom-toast';
        toast.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span class="toast-message">${message}</span>
        `;

        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // --- 2. Add Member Loading State ---
    const addMemberForm = document.querySelector('.add-member-card form');
    if (addMemberForm) {
        addMemberForm.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        });
    }

    // --- 3. Leader Promotion Logic (Fixed & Clean) ---
    if (teamMain) {
        teamMain.addEventListener('submit', function(e) {
            if (e.target.classList.contains('leader-form')) {
                const btn = e.target.querySelector('.btn-star');
                const icon = btn.querySelector('i');

                if (btn.classList.contains('is-leader')) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();
                const userName = e.target.closest('.user-info').querySelector('.bold').textContent;

                if (confirm(`Are you sure? ${userName} will become the new Team Leader.`)) {
                    btn.style.pointerEvents = 'none';
                    btn.classList.add('promoting');
                    icon.className = 'fas fa-spinner fa-spin';
                    icon.style.color = 'var(--warning)';
                    e.target.submit();
                }
            }

            // Προσθήκη για το Delete Member αν δεν το έχεις αλλού
            if (e.target.classList.contains('delete-form')) {
                const userName = e.target.closest('tr').querySelector('.bold').textContent;
                if (!confirm(`Remove ${userName} from the team?`)) {
                    e.preventDefault();
                }
            }
        });
    }
});