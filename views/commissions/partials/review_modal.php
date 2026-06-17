<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">

            <div class="modal-header border-bottom-0 pt-4 px-4 pb-2">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="reviewModalLabel">
                    <i class="bi bi-patch-check-fill text-success me-2 fs-4"></i>Commission Completed!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pb-4 pt-2">

                <div id="reviewAlert" class="alert d-none" role="alert"></div>

                <div id="proofDownloadSection" class="mb-4 p-3 rounded-3 theme-border text-center d-none">
                    <span class="d-block small fw-bold mb-2 text-uppercase tracking-wider" style="letter-spacing: 0.05em;">
                        Artist's Completed Work
                    </span>

                    <div class="mb-3 position-relative bg-dark rounded-2 overflow-hidden d-flex align-items-center justify-content-center border shadow-sm" style="min-height: 180px; max-height: 260px;">
                        <img id="proofImagePreview" src="" alt="Artwork Preview" class="img-fluid w-100 h-100 d-none" style="object-fit: contain;">

                        <div id="proofFileIconFallback" class="text-white py-4 d-none">
                            <i class="bi bi-file-earmark-pdf-fill text-danger" style="font-size: 3.5rem;"></i>
                            <span id="proofFileNameSpan" class="d-block small mt-2 text-muted text-truncate px-3" style="max-width: 280px;">document.pdf</span>
                        </div>
                    </div>

                    <a href="#" id="downloadProofBtn" download class="btn btn-fill-static btn-sm px-4 fw-semibold w-100 shadow-sm">
                        <i class="bi bi-download me-2"></i>Download Final Artwork File
                    </a>
                </div>

                <div class="payment-section mb-4 text-center">
                    <button type="button" id="openPaymentModalBtn" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-2">
                        <i class="bi bi-credit-card-fill me-2"></i>Proceed to Checkout & Payment
                    </button>
                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-lock-fill me-1"></i>Secure checkout &bull; Release funds securely to the artist.
                    </small>
                </div>

                <div class="position-relative my-4">
                    <hr class="text-muted m-0">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 text-uppercase font-monospace text-muted small" style="font-size: 0.75rem;">Leave Feedback</span>
                </div>

                <form id="reviewForm" novalidate>
                    <input type="hidden" id="reviewCommissionId" value="">

                    <div class="mb-3 text-center">
                        <label class="form-label d-block fw-bold text-secondary mb-1">Rate your experience:</label>
                        <div class="star-rating d-flex justify-content-center gap-2 fs-1 text-muted" style="cursor: pointer; user-select: none;">
                            <span class="star-node" data-value="1" style="transition: transform 0.15s ease;">&#9733;</span>
                            <span class="star-node" data-value="2" style="transition: transform 0.15s ease;">&#9733;</span>
                            <span class="star-node" data-value="3" style="transition: transform 0.15s ease;">&#9733;</span>
                            <span class="star-node" data-value="4" style="transition: transform 0.15s ease;">&#9733;</span>
                            <span class="star-node" data-value="5" style="transition: transform 0.15s ease;">&#9733;</span>
                        </div>
                        <input type="hidden" id="selectedRating" value="0">
                    </div>

                    <div class="mb-4">
                        <label for="reviewComment" class="form-label fw-bold text-secondary small">Review comment (Optional):</label>
                        <textarea class="form-control rounded-2 border-muted" id="reviewComment" rows="3"
                            placeholder="Share some feedback regarding the quality, timing, and communication..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-outline w-100 py-2 fw-semibold" id="submitReviewBtn">
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
        const reviewForm = document.getElementById('reviewForm');
        const reviewCommId = document.getElementById('reviewCommissionId');
        const selectedRatingEl = document.getElementById('selectedRating');
        const starNodes = document.querySelectorAll('.star-node');
        const reviewAlert = document.getElementById('reviewAlert');
        const submitReviewBtn = document.getElementById('submitReviewBtn');
        const reviewBtnLabel = document.getElementById('reviewBtnLabel');
        const reviewBtnSpinner = document.getElementById('reviewBtnSpinner');
        const openPaymentBtn = document.getElementById('openPaymentModalBtn');

        const proofDownloadSection = document.getElementById('proofDownloadSection');
        const downloadProofBtn = document.getElementById('downloadProofBtn');
        const proofImagePreview = document.getElementById('proofImagePreview');
        const proofFileIconFallback = document.getElementById('proofFileIconFallback');
        const proofFileNameSpan = document.getElementById('proofFileNameSpan');

        // ── Star Interactivity Engine ────────────────────────────────
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
                const isActive = s.dataset.value <= upTo;
                s.style.color = isActive ? '#ffc107' : '#dee2e6';
                s.style.transform = isActive ? 'scale(1.15)' : 'scale(1)';
            });
        }

        function showAlert(msg, type = 'danger') {
            reviewAlert.className = `alert alert-${type} py-2 small`;
            reviewAlert.textContent = msg;
            reviewAlert.classList.remove('d-none');
        }

        function hideAlert() {
            reviewAlert.classList.add('d-none');
        }

        function setLoading(on) {
            submitReviewBtn.disabled = on;
            reviewBtnSpinner.classList.toggle('d-none', !on);
            reviewBtnLabel.textContent = on ? 'Submitting...' : 'Submit Review';
        }

        // ── Payment Modal Link Switcher ──────────────────────────────
        if (openPaymentBtn) {
            openPaymentBtn.addEventListener('click', () => {
                const commId = reviewCommId.value;
                if (!commId) {
                    showAlert('No active context found.');
                    return;
                }
                const currentModal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                if (currentModal) currentModal.hide();

                const payCommId = document.getElementById('payCommissionId');
                if (payCommId) {
                    payCommId.value = commId;
                    payCommId.dataset.amount = '0';
                }
                const targetPayModal = document.getElementById('paymentModal');
                if (targetPayModal) {
                    bootstrap.Modal.getOrCreateInstance(targetPayModal).show();
                }
            });
        }

        // ── Submission Handler ────────────────────────────────────────
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            hideAlert();

            const commissionId = reviewCommId.value;
            const rating = parseInt(selectedRatingEl.value, 10);
            const comment = document.getElementById('reviewComment').value.trim();

            if (!commissionId) {
                showAlert('Invalid identification trace context.');
                return;
            }
            if (!rating || rating < 1 || rating > 5) {
                showAlert('Please choose a star rating score before submitting.');
                return;
            }

            setLoading(true);

            try {
                const base = window.BASE_URL ?? '/';
                const res = await fetch(`${base}api/commissions/submit_review.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        commission_id: commissionId,
                        rating,
                        comment
                    }),
                });
                const data = await res.json();

                if (data.success) {
                    showAlert(data.message || 'Review documented successfully!', 'success');
                    reviewForm.reset();
                    selectedRatingEl.value = '0';
                    highlightStars(0);
                } else {
                    showAlert(data.message || 'Error executing entry updates.');
                }
            } catch (err) {
                console.error(err);
                showAlert('Network request failure state detected.');
            } finally {
                setLoading(false);
            }
        });

        // Clean viewport footprint on component closure lifecycle
        const modalEl = document.getElementById('reviewModal');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                hideAlert();
                setLoading(false);
                reviewForm.reset();
                selectedRatingEl.value = '0';
                highlightStars(0);
                downloadProofBtn.setAttribute('href', '#');
                proofImagePreview.src = '';
                proofImagePreview.classList.add('d-none');
                proofFileIconFallback.classList.add('d-none');
                proofDownloadSection.classList.add('d-none');
            });
        }
    });

    /**
     * Global Initializer Routine
     * Exposes full setup context parameters out to global client clicks.
     */
    window.initReviewModal = function(commissionId, proofUrl) {
    const reviewCommId       = document.getElementById('reviewCommissionId');
    const proofSection       = document.getElementById('proofDownloadSection');
    const downloadBtn        = document.getElementById('downloadProofBtn');
    const proofImagePreview  = document.getElementById('proofImagePreview');
    const proofIconFallback  = document.getElementById('proofFileIconFallback');
    const proofFileNameSpan  = document.getElementById('proofFileNameSpan');
    const targetModal        = document.getElementById('reviewModal');

    if (!reviewCommId || !targetModal) return;

    reviewCommId.value = commissionId;

    if (proofUrl && proofUrl.trim() !== '') {
        // Clear out duplicates or accidental absolute/relative collisions
        let cleanUrl = proofUrl;
        
        if (!cleanUrl.startsWith('http')) {
            const base = window.BASE_URL ?? './';
            // Ensure we don't end up with doubled base URLs or forward slashes
            cleanUrl = (base + '/' + cleanUrl).replace(/([^:]\/)\/+/g, "$1");
        }
        
        downloadBtn.setAttribute('href', cleanUrl);
        
        // Extract extension rules
        const ext = cleanUrl.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            proofImagePreview.src = cleanUrl;
            proofImagePreview.classList.remove('d-none');
            proofIconFallback.classList.add('d-none');
        } else {
            const fragments = cleanUrl.split('/');
            proofFileNameSpan.textContent = fragments[fragments.length - 1];
            proofIconFallback.classList.remove('d-none');
            proofImagePreview.classList.add('d-none');
        }
        proofSection.classList.remove('d-none');
    } else {
        downloadBtn.setAttribute('href', '#');
        proofSection.classList.add('d-none');
    }

    bootstrap.Modal.getOrCreateInstance(targetModal).show();
};
</script>