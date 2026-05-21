const exportBtn = document.getElementById('export_button');

if (exportBtn) {
    exportBtn.addEventListener('click', function() {
        // Εύρεση της φόρμας βάσει του class που έχει στο Blade
        const filterForm = document.querySelector('.filter-grid');

        if (!filterForm) {
            console.error('Η φόρμα φίλτρων δεν βρέθηκε!');
            return;
        }

        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value !== '') params.append(key, value);
        }

        // Λήψη του URL από το data-url attribute για να είναι δυναμικό
        const baseUrl = exportBtn.getAttribute('data-url');

        // Ανακατεύθυνση στο export route με τις παραμέτρους
        window.location.href = `${baseUrl}?${params.toString()}`;
    });
}