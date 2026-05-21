/**
 * Activities Index Logic
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.filter-grid');
    const tableContainer = document.getElementById('activity_table_container');
    const loader = document.getElementById('table_loader');
    const rowsContainer = document.getElementById('activity_rows');
    const paginationContainer = document.getElementById('pagination_container');

    // Safety first
    if (!form) return;

    // Pagination Click Handler
    paginationContainer.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link) {
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page');
            if (page) {
                loadData(page);
                // Ομαλό scroll πάνω
                window.scrollTo({ top: tableContainer.offsetTop - 100, behavior: 'smooth' });
            }
        }
    });

    function loadData(page = 1) {
        if (!loader) return;
        loader.classList.remove('hidden');

        // 1. Καθαρισμός προηγούμενων λαθών πριν το νέο request
        clearErrors();

        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (typeof value === 'string' && value.trim() !== '') {
                params.append(key, value);
            }
        }
        params.append('page', page);

        const newUrl = `${window.location.pathname}?${params.toString()}`;
        window.history.pushState({}, '', newUrl);

        fetch(newUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(response => {
                // Αν το status είναι 422, η Laravel έστειλε validation errors
                if (response.status === 422) {
                    return response.json().then(errData => {
                        showErrors(errData.errors);
                        throw new Error('Validation failed');
                    });
                }
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.count > 0) {
                    rowsContainer.innerHTML = data.html;
                    paginationContainer.innerHTML = data.pagination || '';
                    tableContainer.classList.remove('hidden');
                } else {
                    tableContainer.classList.add('hidden');
                    paginationContainer.innerHTML = '';
                }
            })
            .catch(error => {
                if (error.message !== 'Validation failed') {
                    console.error('Error fetching activities:', error);
                }
            })
            .finally(() => {
                loader.classList.add('hidden');
            });
    }

    /**
     * Εμφανίζει τα λάθη στα σωστά inputs
     */
    function showErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');

                // Δημιουργία του error span
                const errorSpan = document.createElement('span');
                errorSpan.className = 'error-text ajax-error';
                errorSpan.innerText = messages[0];

                // Αν είναι ημερομηνία, τη βάζουμε στο flex container
                if (input.type === 'date') {
                    input.closest('.flex-align-center').appendChild(errorSpan);
                } else {
                    input.closest('.filter-group').appendChild(errorSpan);
                }
            }
        }
    }

    /**
     * Καθαρίζει όλα τα ενεργά errors
     */
    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.ajax-error').forEach(el => el.remove());
    }

    // Listeners για τα φίλτρα
    form.addEventListener('change', (e) => {
        // Αν η αλλαγή έγινε σε text input, αγνόησέ την (θα το αναλάβει το debounce)
        if (e.target.type === 'text') return;
        loadData(1);
    });

    // Debounce Search
    let typingTimer;
    form.addEventListener('input', (e) => {
        if (e.target.tagName === 'INPUT' && e.target.type === 'text') {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => loadData(1), 500);
        }
    });

    // Initial Load - Εκτελείται ΜΟΝΟ αν ο πίνακας είναι άδειος
    const urlParams = new URLSearchParams(window.location.search);
    const initialPage = urlParams.get('page') || 1;

    // Αν το tbody δεν έχει rows (δηλαδή ο server έστειλε κενό πίνακα), τότε τράβα δεδομένα
    if (rowsContainer && rowsContainer.children.length === 0) {
        loadData(initialPage);
    } else {
        // Αν έχει ήδη δεδομένα, απλά δείχνουμε το container (αν ήταν hidden)
        tableContainer?.classList.remove('hidden');
    }

});