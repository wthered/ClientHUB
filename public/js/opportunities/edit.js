document.addEventListener('DOMContentLoaded', function () {
    const stageSelect = document.getElementById('stage_id');
    const lossGroup = document.getElementById('loss-reason-group');
    const probInput = document.getElementById('probability');
    const probVal = document.getElementById('prob-val');

    /**
     * Ελέγχει αν το επιλεγμένο στάδιο υποδηλώνει "Απώλεια" ή "Νίκη"
     */
    const getStageStatus = (selectedOption) => {
        // Διαβάζουμε τα IDs από το attribute που βάλαμε στο Blade
        const lostIds = JSON.parse(stageSelect.getAttribute('data-lost-ids') || '[]');
        const currentId = parseInt(selectedOption.value);
        const text = selectedOption.text.toLowerCase();

        return {
            // Είναι lost αν το ID περιλαμβάνεται στη λίστα που ήρθε από τη βάση
            isLost: lostIds.includes(currentId),
            // Για το Won, μπορούμε να κρατήσουμε το text check ή να κάνουμε το ίδιο με data-won-ids
            isWon: text.includes('won') || text.includes('κερδήθηκε')
        };
    };

    /**
     * Ενημερώνει το UI (Visibility & Probability)
     */
    function updateUI() {
        if (!stageSelect) return;

        const selectedOption = stageSelect.options[stageSelect.selectedIndex];
        const { isLost, isWon } = getStageStatus(selectedOption);

        // 1. Διαχείριση εμφάνισης του Loss Reason
        if (lossGroup) {
            lossGroup.style.display = isLost ? 'block' : 'none';
        }

        // 2. Αυτόματη ρύθμιση Πιθανότητας (Probability)
        if (isLost) {
            updateProbability(0);
        } else if (isWon) {
            updateProbability(100);
        }
    }

    /**
     * Βοηθητική συνάρτηση για την ενημέρωση του Slider και του Label
     */
    function updateProbability(val) {
        if (probInput && probVal) {
            probInput.value = val;
            probVal.innerText = val;
        }
    }

    // Event Listener για την αλλαγή του σταδίου
    stageSelect.addEventListener('change', updateUI);

    // Event Listener για το manual σύρσιμο του slider (να ενημερώνεται το νούμερο)
    if (probInput) {
        probInput.addEventListener('input', function() {
            probVal.innerText = this.value;
        });
    }

    // Εκτέλεση κατά το φόρτωμα της σελίδας
    updateUI();
});