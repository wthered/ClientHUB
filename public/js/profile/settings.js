document.querySelectorAll('.switch input').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const item = this.closest('.setting-item');
        const sales_box = document.getElementById("checkbox_sales");
        const report_box = document.getElementById("checkbox_report");
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Οπτικό feedback: "κλειδώνουμε" προσωρινά το item
        item.style.opacity = '0.5';
        item.style.pointerEvents = 'none';

        fetch("/profile/settings/preferences", {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                notif_sales: sales_box.checked,
                notif_report: report_box.checked,
            }),
        })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Updated:', data);
                // Επαναφορά opacity με ένα απαλό transition
                item.style.opacity = '1';
                item.style.pointerEvents = 'all';
            })
            .catch(error => {
                this.checked = !this.checked; // Επαναφορά αν αποτύχει
                item.style.opacity = '1';
                item.style.pointerEvents = 'all';
                console.error('Error:', error);
                alert('Παρουσιάστηκε σφάλμα κατά την αποθήκευση της ρύθμισης.');
            });
    });
});