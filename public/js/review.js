document.addEventListener('DOMContentLoaded', () => {

    // ── DOM refs ─────────────────────────────────────────
    const reviewForm = document.getElementById('reviewForm');
    const reviewCommId = document.getElementById('reviewCommissionId');
    const selectedRatingEl = document.getElementById('selectedRating');
    const starNodes = document.querySelectorAll('.star-node');
    const reviewAlert = document.getElementById('reviewAlert');
    const submitReviewBtn = document.getElementById('submitReviewBtn');
    const reviewBtnLabel = document.getElementById('reviewBtnLabel');
    const reviewBtnSpinner = document.getElementById('reviewBtnSpinner');
    const openPaymentBtn = document.getElementById('openPaymentModalBtn');

    // ── Star rating interaction ───────────────────────────
    starNodes.forEach(star => {
        star.addEventListener('mouseenter', () => highlightStars(star.dataset.value));
        star.addEventListener('mouseleave', () => highlightStars(selectedRatingEl.value));
        star.addEventListener('click', () => {
            selectedRatingEl.value = star.dataset.value;
            highlightStars(star.dataset.value);
        });
    });

    function highlightStars(upTo) {
        starNodes.forEach(s => {
            s.style.color = s.dataset.value <= upTo ? '#f5c518' : '';
        });
    }

    // ── Alert helpers ─────────────────────────────────────
    function showAlert(msg, type = 'danger') {
        reviewAlert.className = `alert alert-${type}`;
        reviewAlert.textContent = msg;
        reviewAlert.classList.remove('d-none');
    }
    function hideAlert() { reviewAlert.classList.add('d-none'); }

    function setLoading(on) {
        submitReviewBtn.disabled = on;
        reviewBtnSpinner.classList.toggle('d-none', !on);
        reviewBtnLabel.textContent = on ? 'Submitting...' : 'Submit Review';
    }

    // ── "Proceed to Payment" ──────────────────────────────
    // Closes the review modal and opens the payment modal,
    // forwarding the same commission_id.
    if (openPaymentBtn) {
        openPaymentBtn.addEventListener('click', () => {
            const commId = reviewCommId.value;
            if (!commId) {
                showAlert('No commission selected. Please try again.');
                return;
            }

            // Hide review modal
            const reviewModalEl = document.getElementById('reviewModal');
            const reviewModal = bootstrap.Modal.getInstance(reviewModalEl);
            if (reviewModal) reviewModal.hide();

            // Forward commission context to payment modal
            const payCommId = document.getElementById('payCommissionId');
            if (payCommId) payCommId.value = commId;

            // Copy amount if available
            const amountSrc = document.getElementById('reviewCommissionId');
            const payAmtEl = document.getElementById('payCommissionId');
            if (amountSrc && payAmtEl) {
                payAmtEl.dataset.amount = amountSrc.dataset.amount || '0';
            }

            const payModalEl = document.getElementById('paymentModal');
            if (payModalEl) {
                const payModal = new bootstrap.Modal(payModalEl);
                payModal.show();
            }
        });
    }

    // ── Review form submission ────────────────────────────
    reviewForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert();

        const commissionId = reviewCommId.value;
        const rating = parseInt(selectedRatingEl.value, 10);
        const comment = document.getElementById('reviewComment').value.trim();

        if (!commissionId) { showAlert('No commission selected.'); return; }
        if (!rating || rating < 1 || rating > 5) {
            showAlert('Please select a star rating before submitting.');
            return;
        }

        setLoading(true);

        try {
            const res = await fetch('/api/reviews/submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ commission_id: commissionId, rating, comment }),
            });
            const data = await res.json();

            if (data.success) {
                showAlert('Your review has been submitted. Thank you!', 'success');
                reviewForm.reset();
                selectedRatingEl.value = '0';
                highlightStars(0);
            } else {
                showAlert(data.message || 'Could not submit review. Please try again.');
            }
        } catch (err) {
            console.error('Review error:', err);
            showAlert('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    });

    // ── Reset on close ────────────────────────────────────
    const reviewModalEl = document.getElementById('reviewModal');
    if (reviewModalEl) {
        reviewModalEl.addEventListener('hidden.bs.modal', () => {
            hideAlert();
            setLoading(false);
            reviewForm.reset();
            selectedRatingEl.value = '0';
            highlightStars(0);
        });
    }
});