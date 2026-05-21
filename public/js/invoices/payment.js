/**
 * Quick Payment Logic (payment.js)
 */
document.addEventListener('DOMContentLoaded', function() {
    const btnRecord = document.getElementById('btn-record-payment');
    const invoiceForm = document.getElementById('invoice-form');

    if (!btnRecord || !invoiceForm) return;

    const invoiceId = invoiceForm.dataset.id;

    btnRecord.addEventListener('click', async function() {
        const amountInput = document.getElementById('quick-payment-amount');
        const methodSelect = document.getElementById('quick-payment-method');

        const amount = amountInput.value;
        const method = methodSelect ? methodSelect.value : 'bank_transfer';

        if (!amount || amount <= 0) return alert('Δώστε έγκυρο ποσό');

        // Disable κουμπιού για να αποφύγουμε διπλά κλικ (double-tap)
        btnRecord.disabled = true;
        btnRecord.innerText = 'Καταχώρηση...';

        try {
            const response = await fetch(`/invoices/${invoiceId}/payments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    payment_date: new Date().toISOString().split('T')[0],
                    method: method
                })
            });

            if (response.ok) {
                location.reload();
            } else {
                const error = await response.json();
                alert('Σφάλμα: ' + (error.message || 'Αποτυχία καταχώρησης'));
                btnRecord.disabled = false;
                btnRecord.innerText = 'Καταχώρηση';
            }
        } catch (err) {
            console.error('Payment Error:', err);
            btnRecord.disabled = false;
            btnRecord.innerText = 'Καταχώρηση';
        }
    });
});