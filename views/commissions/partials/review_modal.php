<!-- =====================================================
     review_modal.php
     Handles STEP 1 only: star rating + comment submission.
     Payment is handled separately in payment_modal.php.
     ===================================================== -->

     <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Commission Completed!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- Alert box -->
                <div id="reviewAlert" class="alert d-none" role="alert"></div>

                <!-- "Go to payment" shortcut -->
                <div class="payment-section mb-4 text-center">
                    <button type="button" id="openPaymentModalBtn" class="btn btn-success btn-lg w-100 fw-bold">
                        <i class="bi bi-credit-card-fill me-2"></i>Proceed to Payment
                    </button>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-lock-fill"></i> Secure checkout &mdash; choose your payment method.
                    </small>
                </div>

                <hr class="my-4 text-muted">

                <form id="reviewForm" novalidate>
                    <!-- Commission ID and amount are set by JS when the modal opens -->
                    <input type="hidden" id="reviewCommissionId" value="">

                    <div class="mb-3">
                        <label class="form-label d-block fw-bold">Rate the Artist:</label>
                        <div class="star-rating d-flex gap-2 fs-2 text-secondary" style="cursor: pointer;">
                            <span class="star-node" data-value="1">&#9733;</span>
                            <span class="star-node" data-value="2">&#9733;</span>
                            <span class="star-node" data-value="3">&#9733;</span>
                            <span class="star-node" data-value="4">&#9733;</span>
                            <span class="star-node" data-value="5">&#9733;</span>
                        </div>
                        <input type="hidden" id="selectedRating" value="0">
                    </div>

                    <div class="mb-3">
                        <label for="reviewComment" class="form-label fw-bold">Leave a Comment (Optional):</label>
                        <textarea class="form-control" id="reviewComment" rows="4"
                            placeholder="Tell us about your experience with the artist..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitReviewBtn">
                        <span id="reviewBtnLabel">Submit Review</span>
                        <span id="reviewBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── DOM refs ─────────────────────────────────────────
    const reviewForm        = document.getElementById('reviewForm');
    const reviewCommId      = document.getElementById('reviewCommissionId');
    const selectedRatingEl  = document.getElementById('selectedRating');
    const starNodes         = document.querySelectorAll('.star-node');
    const reviewAlert       = document.getElementById('reviewAlert');
    const submitReviewBtn   = document.getElementById('submitReviewBtn');
    const reviewBtnLabel    = document.getElementById('reviewBtnLabel');
    const reviewBtnSpinner  = document.getElementById('reviewBtnSpinner');
    const openPaymentBtn    = document.getElementById('openPaymentModalBtn');

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
        submitReviewBtn.disabled      = on;
        reviewBtnSpinner.classList.toggle('d-none', !on);
        reviewBtnLabel.textContent    = on ? 'Submitting...' : 'Submit Review';
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
            const reviewModal   = bootstrap.Modal.getInstance(reviewModalEl);
            if (reviewModal) reviewModal.hide();

            // Forward commission ID to payment modal
            const payCommId = document.getElementById('payCommissionId');
            if (payCommId) {
                payCommId.value = commId;
                // Payment modal fetches the real price itself — just reset dataset.amount
                payCommId.dataset.amount = '0';
            }

            const payModalEl = document.getElementById('paymentModal');
            if (payModalEl) {
                bootstrap.Modal.getOrCreateInstance(payModalEl).show();
            }
        });
    }

    // ── Review form submission ────────────────────────────
    reviewForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideAlert();

        const commissionId = reviewCommId.value;
        const rating       = parseInt(selectedRatingEl.value, 10);
        const comment      = document.getElementById('reviewComment').value.trim();

        if (!commissionId) { showAlert('No commission selected.'); return; }
        if (!rating || rating < 1 || rating > 5) {
            showAlert('Please select a star rating before submitting.');
            return;
        }

        setLoading(true);

        try {
            const res  = await fetch((window.BASE_URL ?? '/') + 'api/commissions/submit_review.php', {
                method : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body   : JSON.stringify({ commission_id: commissionId, rating, comment }),
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
</script>