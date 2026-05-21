document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.filter-grid');
    const container = document.getElementById('payments-table-container');
    // Πάρε το CSRF token από το meta tag του head
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!form || !container) return;

    const updateTable = async () => {
        const formData = new FormData(form);

        // URL για το POST filtering
        const url = "/payments/filter";

        container.classList.add('is-loading');

        try {
            console.log("Fetching data via POST from", url);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) throw new Error('Network response was not ok');

            // Ενημέρωση του container με το περιεχόμενο του partial
            container.innerHTML = await response.text();

            // Ενημέρωση του URL στον browser για να δουλεύει το Refresh (χρησιμοποιούμε τα params για το display μόνο)
            const params = new URLSearchParams(formData).toString();
            window.history.pushState({}, '', `/payments?${params}`);

        } catch (error) {
            console.error('Filtering error:', error);
        } finally {
            container.classList.remove('is-loading');
        }
    };

    // Auto-submit on change
    form.querySelectorAll('select, input[type="date"]').forEach(input => {
        input.addEventListener('change', updateTable);
    });

    // Debounce search
    let typingTimer;
    document.getElementById('invoice_id')?.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(updateTable, 500);
    });

    // Handle form submission (Enter key)
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        updateTable().then(response => {
            console.log("[Payments Index - Line 63]",response);
        });
    });
});