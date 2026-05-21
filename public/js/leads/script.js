/**
 * LEADS MODULE - SHOW VIEW SCRIPT
 * Handles Modal Logic, UI Toggles, and State Management
 */

const LeadManager = {
    // Selectors
    modalId: 'conversionModal',

    /**
     * Open the Conversion Modal
     */
    openConversionModal: function() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.style.display = 'flex';
            // Prevent background scrolling for a "premium" feel
            document.body.style.overflow = 'hidden';

            // Add an event listener for the Escape key to close the modal
            document.addEventListener('keydown', this.handleEscKey);
        }
    },

    /**
     * Close the Conversion Modal
     */
    closeConversionModal: function() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.removeEventListener('keydown', this.handleEscKey);
        }
    },

    /**
     * Handle Escape Key Press
     */
    handleEscKey: function(e) {
        if (e.key === 'Escape') {
            LeadManager.closeConversionModal();
        }
    },

    /**
     * Initialize Global Listeners for the Lead Module
     */
    init: function() {
        const modal = document.getElementById(this.modalId);

        // Exit early if the modal isn't on this page
        if (!modal) return;

        // Close modal if clicking on the blurred overlay
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal-overlay')) {
                    this.closeConversionModal();
                }
            });
        }

        // Auto-hide alerts/toasts after 5 seconds if they exist
        const toasts = document.querySelectorAll('.toast');

        toasts.forEach(toast => {
            // 1. Handle the "Slide In" (automatic via your CSS)

            // 2. Schedule the "Fade Out" after 4 seconds
            setTimeout(() => {
                toast.classList.add('fade-out');

                // 3. Physically remove from DOM after animation finishes
                toast.addEventListener('animationend', (e) => {
                    if (e.animationName === 'toastFadeOut') {
                        toast.remove();
                    }
                });
            }, 4000);
        });
    }
};

// Start the engine when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    LeadManager.init();
});

// Global helper for the Blade button onclick
window.openConversionModal = () => LeadManager.openConversionModal();
window.closeConversionModal = () => LeadManager.closeConversionModal();