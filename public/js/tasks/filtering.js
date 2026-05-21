/**
 * Tasks Filtering Logic - Updated
 * Διαχειρίζεται το αυτόματο φιλτράρισμα (Select & Dates)
 * και την αναζήτηση με debounce.
 */

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filters-bar');
    if (!filterForm) return;

    // Επιλέγουμε όλα τα controls που θέλουμε να προκαλούν αυτόματο submit
    // Συμπεριλαμβάνουμε πλέον και τα input[type="date"]
    const autoSubmitControls = filterForm.querySelectorAll('select.filter-control, input[type="date"]');
    const searchInput = filterForm.querySelector('input[name="search"]');

    /**
     * 1. Listener για Select & Date Inputs
     * Υποβάλλει τη φόρμα αμέσως μόλις αλλάξει η τιμή.
     */
    autoSubmitControls.forEach(control => {
        control.addEventListener('change', () => {
            submitFilters();
        });
    });

    /**
     * 2. Αναζήτηση με Debounce
     * Αποφεύγει τις συνεχόμενες κλήσεις στον server όσο ο χρήστης πληκτρολογεί.
     */
    if (searchInput) {
        // Διατηρούμε το focus μετά το reload αν υπήρχε κείμενο
        if (searchInput.value.length > 0) {
            searchInput.focus();
            // Μετακίνηση του κέρσορα στο τέλος του κειμένου
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }

        searchInput.addEventListener('input', debounce(() => {
            submitFilters();
        }, 500));
    }

    /**
     * Συνάρτηση υποβολής των φίλτρων με Visual Feedback
     */
    function submitFilters() {
        const tableCard = document.querySelector('.card');

        if (tableCard) {
            // Οπτική ένδειξη ότι τα δεδομένα ανανεώνονται
            tableCard.style.opacity = '0.5';
            tableCard.style.pointerEvents = 'none';
            tableCard.style.transition = 'opacity 0.2s ease-in-out';
        }

        filterForm.submit();
    }

    /**
     * Helper: Debounce function
     * Καθυστερεί την εκτέλεση μιας συνάρτησης μέχρι να περάσει
     * ο καθορισμένος χρόνος (wait) από την τελευταία φορά που κλήθηκε.
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});