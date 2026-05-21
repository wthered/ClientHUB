document.addEventListener('DOMContentLoaded', function() {
    // 1. Elements
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('status');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const tableRows = document.querySelectorAll('.table-custom tbody tr.invoice-row');

    // 2. Filter Function
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusTerm = statusSelect.value.toLowerCase();
        const fromDateValue = dateFrom.value;
        const toDateValue = dateTo.value;

        tableRows.forEach(row => {
            // Data extraction from data-attributes or textContent
            // Tip: Καλό είναι στο <tr> του Blade να έχεις data-status="{{ $invoice->status->value }}"
            const rowText = row.innerText.toLowerCase();
            const rowStatus = row.getAttribute('data-status') || '';
            const rowDate = row.getAttribute('data-date') || ''; // YYYY-MM-DD

            let isVisible = true;

            // Search Filter
            if (searchTerm && !rowText.includes(searchTerm)) {
                isVisible = false;
            }

            // Status Filter
            if (statusTerm && rowStatus !== statusTerm) {
                isVisible = false;
            }

            // Date Range Filter
            if (fromDateValue && rowDate < fromDateValue) {
                isVisible = false;
            }
            if (toDateValue && rowDate > toDateValue) {
                isVisible = false;
            }

            // Toggle visibility
            row.style.display = isVisible ? '' : 'none';
        });

        updateNoResultsMessage();
    }

    // 3. Debounce for Search (300ms delay)
    let timeout = null;
    searchInput.addEventListener('keyup', () => {
        clearTimeout(timeout);
        timeout = setTimeout(filterTable, 300);
    });

    // 4. Instant Listeners for Select/Dates
    [statusSelect, dateFrom, dateTo].forEach(el => {
        el.addEventListener('change', filterTable);
    });

    // 5. No Results Message
    function updateNoResultsMessage() {
        let visibleRows = Array.from(tableRows).filter(r => r.style.display !== 'none').length;
        let noResultsRow = document.getElementById('no-results-row');

        if (visibleRows === 0) {
            if (!noResultsRow) {
                const tbody = document.querySelector('.table tbody');
                const row = tbody.insertRow();
                row.id = 'no-results-row';
                row.innerHTML = `<td colspan="100%" class="text-center py-4 text-muted">No invoices found matching your filters.</td>`;
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    }

    // 6. Reset Filters Logic
    const resetBtn = document.querySelector('.btn-sync-reset'); // Το κουμπί με το εικονίδιο sync

    if (resetBtn) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Αποφυγή του page reload αν είναι <a> ή submit button

            // Καθαρισμός τιμών στα inputs
            searchInput.value = '';
            statusSelect.value = '';
            dateFrom.value = '';
            dateTo.value = '';

            // Επανεμφάνιση όλων των γραμμών
            tableRows.forEach(row => {
                row.style.display = '';
            });

            // Αφαίρεση του "No Results" μηνύματος αν υπάρχει
            const noResultsRow = document.getElementById('no-results-row');
            if (noResultsRow) noResultsRow.remove();

            console.log('Filters cleared!');
        });
    }
});