/**
 * Activities Modal Logic - Property Diff Renderer
 */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('details_modal');
    const container = document.getElementById('properties_container');
    const closeBtn = document.querySelector('.close-modal');

    if (!modal || !container) return;

    // Event Delegation για να πιάνουμε τα clicks και στα AJAX rows
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-view-details');

        // Αν δεν είναι το κουμπί των λεπτομερειών, αγνόησέ το
        if (!btn || !btn.hasAttribute('data-props')) return;

        try {
            const props = JSON.parse(btn.getAttribute('data-props'));
            renderProperties(props);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Lock scroll
        } catch (error) {
            console.error('Parsing error on properties:', error);
        }
    });

    /**
     * Κατασκευάζει τον πίνακα συγκρίσεων
     */
    function renderProperties(props) {
        container.innerHTML = '';

        const oldVals = props.old || {};
        const newVals = props.attributes || props;

        // Συλλέγουμε όλα τα κλειδιά (keys) και από τα δύο objects
        const allKeys = [...new Set([...Object.keys(oldVals), ...Object.keys(newVals)])];

        // Τεχνικά πεδία που θέλουμε να κρύψουμε
        const hiddenKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'email_verified_at'];

        let html = `
            <table class="data-table" style="font-size: 0.8rem;">
                <thead>
                    <tr>
                        <th>Πεδίο</th>
                        <th>Παλιά Τιμή</th>
                        <th>Νέα Τιμή</th>
                    </tr>
                </thead>
                <tbody>
        `;

        allKeys.forEach(key => {
            if (hiddenKeys.includes(key)) return;

            const oldVal = formatValue(oldVals[key]);
            const newVal = formatValue(newVals[key]);

            // Αν η τιμή άλλαξε, βάλε κλάση προειδοποίησης
            const rowClass = oldVal !== newVal ? 'table-warning' : '';

            html += `
                <tr class="${rowClass}">
                    <td class="bold text-muted">${key}</td>
                    <td class="text-danger">${oldVal}</td>
                    <td class="text-success">${newVal}</td>
                </tr>
            `;
        });

        html += `</tbody></table>`;
        container.innerHTML = html;
    }

    /**
     * Format τιμών για σωστή απεικόνιση
     */
    function formatValue(val) {
        if (val === null || val === undefined) return '<span class="text-muted">null</span>';
        if (typeof val === 'boolean') return val ? 'ΝΑΙ' : 'ΟΧΙ';
        if (typeof val === 'object') return JSON.stringify(val);
        return val;
    }

    // Κλείσιμο Modal
    const closeModal = () => {
        modal.classList.add('hidden');
        document.body.style.overflow = ''; // Unlock scroll
    };

    closeBtn.addEventListener('click', closeModal);

    // Κλείσιμο με click έξω από το modal
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Κλείσιμο με το Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
});