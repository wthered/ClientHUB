/**
 * js/invoices/edit.js
 * Διαχείριση Γραμμών Τιμολογίου & Υπολογισμός Συνόλων
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.querySelector('#items-table tbody');
    const addRowBtn = document.getElementById('add-item');

    // Αρχικοποίηση index με βάση τις υπάρχουσες γραμμές
    let rowIdx = tableBody.querySelectorAll('tr').length;

    /**
     * 1. Προσθήκη Νέας Γραμμής (AJAX)
     */
    if (addRowBtn) {
        addRowBtn.addEventListener('click', async function() {
            try {
                const response = await fetch(`/invoices/add-row?index=${rowIdx}`);
                const html = await response.text();
                const newRow = document.createElement('tr');
                newRow.innerHTML = html;
                tableBody.appendChild(newRow);
                rowIdx++;
                calculateTotals();
            } catch (error) {
                console.error('Error fetching new row:', error);
            }
        });
    }

    /**
     * 2. Διαγραφή Γραμμής (Event Delegation)
     */
    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            if (tableBody.querySelectorAll('tr').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                alert('Το τιμολόγιο πρέπει να έχει τουλάχιστον μία γραμμή.');
            }
        }
    });

    /**
     * 3. Αλλαγή Προϊόντος (Αυτόματη Τιμή & Υπολογισμός)
     */
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            const selected = e.target.options[e.target.selectedIndex];
            const price = selected.getAttribute('data-price');
            if (price) {
                row.querySelector('.unit-price-input').value = parseFloat(price).toFixed(2);
                calculateTotals();
            }
        }
    });

    /**
     * 4. Real-time Υπολογισμοί κατά την πληκτρολόγηση
     */
    tableBody.addEventListener('input', function(e) {
        if (e.target.matches('.qty-input, .unit-price-input')) {
            calculateTotals();
        }
    });

    /**
     * 5. Κεντρική Συνάρτηση Υπολογισμών
     */
    function calculateTotals() {
        let net = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input')?.value) || 0;
            const up = parseFloat(row.querySelector('.unit-price-input')?.value) || 0;
            const amountInput = row.querySelector('.amount-input');

            const rowAmount = parseFloat((qty * up).toFixed(2));
            if (amountInput) amountInput.value = rowAmount.toFixed(2);
            net += rowAmount;
        });

        const tax = parseFloat((net * 0.24).toFixed(2));
        const total = parseFloat((net + tax).toFixed(2));

        // Ασφαλής ανάκτηση του ήδη πληρωμένου ποσού
        const paidEl = document.getElementById('already-paid-display');
        const alreadyPaid = paidEl ? parseFloat(paidEl.dataset.paid) : 0;
        const balance = parseFloat((total - alreadyPaid).toFixed(2));

        const format = (val) => val.toLocaleString('el-GR', { minimumFractionDigits: 2 });

        // Σύνδεση με το DOM
        const netDisplay = document.getElementById('preview-net');
        const taxDisplay = document.getElementById('preview-tax');
        const totalDisplay = document.getElementById('preview-total');
        const balanceDisplay = document.getElementById('preview-balance');

        // Ενημέρωση κειμένων
        if (netDisplay) netDisplay.textContent = format(net) + ' €';
        if (taxDisplay) taxDisplay.textContent = format(tax) + ' €';
        if (totalDisplay) totalDisplay.textContent = format(total) + ' €';

        if (balanceDisplay) {
            balanceDisplay.textContent = format(balance) + ' €';
            // UI Logic: Αν το υπόλοιπο είναι μηδενικό ή αρνητικό (υπέρβαση), το κάνουμε πράσινο
            balanceDisplay.style.color = balance <= 0 ? 'var(--success)' : 'var(--primary)';
        }
    }

    // Αρχικός υπολογισμός κατά το load
    calculateTotals();
});