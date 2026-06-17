/* ══════════════════════════════════════════════════════════════
   artovia.api.js — Commission action handlers (fetch wrappers)
   Requires: artovia.core.js
══════════════════════════════════════════════════════════════ */

(function (global) {
    'use strict';

    const A = global.Artovia;
    if (!A) { console.error('artovia.api.js: Artovia core not loaded.'); return; }

    const BASE = () => A.config.baseUrl;

    // ── Generic error modal handler ────────────────────────────
    function showErrorModal(message) {
        const MODAL_ID = 'artvErrorModal';
        let modalEl = document.getElementById(MODAL_ID);

        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id        = MODAL_ID;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
                    <div class="modal-content text-center border-0 shadow-lg p-4 bg-card" style="border-radius:1rem;">
                        <div class="modal-body">
                            <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-circle mx-auto mb-3"
                                 style="width:60px;height:60px;font-size:1.75rem;">✕</div>
                            <h4 class="fw-bold mb-2" style="font-family:var(--font-ui);">Error</h4>
                            <p class="text-muted small mb-4 px-2" id="${MODAL_ID}Message"></p>
                            <button type="button" class="btn btn-secondary w-100 py-2 rounded-3" data-bs-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }
        document.getElementById(`${MODAL_ID}Message`).textContent = message;
        new bootstrap.Modal(modalEl).show();
    }

    // ── Generic confirmation modal handler ──────────────────────
    function showConfirmModal(message, onConfirm) {
        const MODAL_ID = 'artvConfirmModal';
        let modalEl = document.getElementById(MODAL_ID);

        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id        = MODAL_ID;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
                    <div class="modal-content text-center border-0 shadow-lg p-4 bg-card" style="border-radius:1rem;">
                        <div class="modal-body">
                            <h4 class="fw-bold mb-3" style="font-family:var(--font-ui);">Are you sure?</h4>
                            <p class="text-muted small mb-4 px-2" id="${MODAL_ID}Message"></p>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-secondary w-50 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="${MODAL_ID}ConfirmBtn" class="btn-artovia-primary w-50 py-2 rounded-3" data-bs-dismiss="modal">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }

        document.getElementById(`${MODAL_ID}Message`).textContent = message;
        
        const confirmBtn = document.getElementById(`${MODAL_ID}ConfirmBtn`);
        const freshBtn = confirmBtn.cloneNode(true);
        confirmBtn.replaceWith(freshBtn);
        
        freshBtn.addEventListener('click', onConfirm);
        
        new bootstrap.Modal(modalEl).show();
    }

    // ── Generic action helper ──────────────────────────────────
    async function runAction(btn, loadingText, payload, onSuccess, onFail) {
        const original = btn.textContent;
        A.btnLoading(btn, loadingText);
        try {
            const data = await A.postJSON(`${BASE()}api/commissions/update_status.php`, payload);
            if (data?.success) {
                onSuccess(data);
            } else {
                showErrorModal(data?.message || 'Something went wrong.');
                A.btnReset(btn, original);
                onFail?.();
            }
        } catch (err) {
            console.error(err);
            showErrorModal('A network error occurred. Please try again.');
            A.btnReset(btn, original);
            onFail?.();
        }
    }

    // ── Accept artist (client) ─────────────────────────────────
    A.handleAssignArtist = function (btn, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        const requestId    = parseInt(btn.getAttribute('data-request-id'));
        if (!commissionId || !requestId) return;

        showConfirmModal('Accept this artist? All other pending requests for this commission will be automatically declined.', async () => {
            await runAction(
                btn,
                'Accepting…',
                { commission_id: commissionId, request_id: requestId, status: 'accepted' },
                (data) => {
                    A.showSuccessModal('Artist Accepted!', data.message || 'The commission is now in progress.');
                    onComplete?.();
                }
            );
        });
    };

    // ── Decline artist (client) ────────────────────────────────
    A.handleDeclineArtist = function (btn, onComplete) {
        const requestId = parseInt(btn.getAttribute('data-request-id'));
        if (!requestId) return;

        showConfirmModal("Are you sure you want to decline this artist's application?", async () => {
            await runAction(
                btn,
                'Dropping…',
                { request_id: requestId, status: 'rejected' },
                (data) => {
                    A.showSuccessModal('Request Declined', data.message || 'The request has been updated.');
                    onComplete?.();
                }
            );
        });
    };

    // ── Withdraw own request (artist) ──────────────────────────
    A.handleCancelRequest = function (btn, onComplete) {
        const requestId = parseInt(btn.getAttribute('data-request-id'));
        if (!requestId) return;

        showConfirmModal('Withdraw this request? The client will no longer see your application.', async () => {
            await runAction(
                btn,
                'Withdrawing…',
                { request_id: requestId, status: 'cancelled' },
                (data) => {
                    A.showSuccessModal('Request Withdrawn', data.message || 'Your application has been cancelled.');
                    onComplete?.();
                }
            );
        });
    };

    // ── Update commission status (artist) ──────────────────────
    A.handleArtistStatusUpdate = function (btn, newStatus, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        if (!commissionId) return;

        if (newStatus === 'completed') {
            A.openCompletionModal(btn, commissionId, onComplete);
            return;
        }

        showConfirmModal('Mark this commission as In Progress?', async () => {
            await runAction(
                btn,
                'Updating…',
                { commission_id: commissionId, status: newStatus },
                (data) => {
                    A.showSuccessModal('Marked as In Progress!', data.message || 'Commission updated to In Progress.');
                    onComplete?.();
                }
            );
        });
    };

    // ── Lazy-loaded Completion Proof Upload Modal ──────────────
    A.openCompletionModal = function (btn, commissionId, onComplete) {
        const MODAL_ID = 'artvCompletionUploadModal';
        let modalEl = document.getElementById(MODAL_ID);

        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id        = MODAL_ID;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg p-4 bg-card" style="border-radius:1rem;">
                        <div class="modal-header border-0 p-0 mb-3">
                            <h5 class="modal-title fw-bold" style="font-family:var(--font-ui);">Submit Completion Proof</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="${MODAL_ID}Alert" class="alert d-none fs-fluid-xs"></div>
                            <p class="text-muted small mb-3">Please upload a file or photo of your completed commission work to finish the project. This cannot be undone.</p>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-semibold">Select Finished File/Image:</label>
                                <div class="input-group">
                                    <input type="file" id="${MODAL_ID}File" class="form-control d-none" accept="image/*,application/pdf">
                                    <button type="button" class="btn btn-outline-secondary rounded-start-3" onclick="document.getElementById('${MODAL_ID}File').click()">Browse File</button>
                                    <span id="${MODAL_ID}FileName" class="form-control text-truncate rounded-end-3 text-muted small pt-2">No file selected</span>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-secondary w-50 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="${MODAL_ID}Submit" class="btn-artovia-primary w-50 py-2 rounded-3">Complete Project</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);

            document.getElementById(`${MODAL_ID}File`).addEventListener('change', (e) => {
                document.getElementById(`${MODAL_ID}FileName`).textContent = e.target.files[0]?.name ?? 'No file selected';
            });
        }

        const alertBox = document.getElementById(`${MODAL_ID}Alert`);
        const fileInput = document.getElementById(`${MODAL_ID}File`);
        const fileNameSpan = document.getElementById(`${MODAL_ID}FileName`);
        const submitBtn = document.getElementById(`${MODAL_ID}Submit`);

        alertBox.classList.add('d-none');
        fileInput.value = '';
        fileNameSpan.textContent = 'No file selected';
        A.btnReset(submitBtn, 'Complete Project');

        const freshBtn = submitBtn.cloneNode(true);
        submitBtn.replaceWith(freshBtn);

        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        freshBtn.addEventListener('click', async () => {
            if (!fileInput.files[0]) {
                A.showAlert(alertBox, 'Please upload a project image or file to verify completion.', false);
                return;
            }

            alertBox.classList.add('d-none');
            A.btnLoading(freshBtn, 'Uploading…');

            try {
                const formData = new FormData();
                formData.append('commission_id', commissionId);
                formData.append('status', 'completed');
                formData.append('completion_proof', fileInput.files[0]);

                const res = await fetch(`${BASE()}api/commissions/update_status.php`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data?.success) {
                    bsModal.hide();
                    A.showSuccessModal('Marked as Completed!', data.message || 'Commission successfully updated with proof of completion.');
                    onComplete?.();
                } else {
                    A.showAlert(alertBox, data?.message || 'Failed to complete commission.', false);
                    A.btnReset(freshBtn, 'Complete Project');
                }
            } catch (err) {
                console.error(err);
                A.showAlert(alertBox, 'A network error occurred during submission.', false);
                A.btnReset(freshBtn, 'Complete Project');
            }
        });
    };

    // Expose helpers globally for usage in regular dynamic streams
    A.showErrorModal = showErrorModal;
    A.showConfirmModal = showConfirmModal;

    // ── Take commission (artist — opens pitch form modal) ──────
    A.handleTakeCommission = function (btn, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        if (!commissionId) return;

        const MODAL_ID = 'artvTakeCommissionModal';
        let modalEl = document.getElementById(MODAL_ID);

        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id        = MODAL_ID;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');
            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg p-4 bg-card" style="border-radius:1rem;">
                        <div class="modal-header border-0 p-0 mb-3">
                            <h5 class="modal-title fw-bold" style="font-family:var(--font-ui);">Apply for Commission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="${MODAL_ID}Alert" class="alert d-none fs-fluid-xs"></div>
                            <div class="mb-3">
                                <label for="${MODAL_ID}Message" class="form-label text-muted small fw-semibold">
                                    Introduce yourself or leave a brief pitch for the client:
                                </label>
                                <textarea id="${MODAL_ID}Message" class="form-control" rows="4"
                                    placeholder="e.g., I love the concept! I specialize in this style and can finish within your timeframe…"
                                    style="border-radius:0.5rem;resize:none;font-size:0.9rem;"></textarea>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-secondary w-50 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="${MODAL_ID}Submit" class="btn-artovia-primary w-50 py-2 rounded-3">Send Request</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }

        modalEl.dataset.activeCommissionId = commissionId;

        const textarea  = document.getElementById(`${MODAL_ID}Message`);
        const alertBox  = document.getElementById(`${MODAL_ID}Alert`);
        const submitBtn = document.getElementById(`${MODAL_ID}Submit`);

        textarea.value = '';
        alertBox.classList.add('d-none');
        A.btnReset(submitBtn, 'Send Request');

        const freshBtn = submitBtn.cloneNode(true);
        submitBtn.replaceWith(freshBtn);

        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        freshBtn.addEventListener('click', async () => {
            const messageText  = textarea.value.trim();
            const activeId     = parseInt(modalEl.dataset.activeCommissionId);

            if (!activeId) {
                A.showAlert(alertBox, 'Error: Commission ID lost. Please close and re-open.', false);
                return;
            }
            if (!messageText) {
                A.showAlert(alertBox, 'Please write a short message before sending your request.', false);
                return;
            }

            alertBox.classList.add('d-none');
            A.btnLoading(freshBtn, 'Sending…');

            try {
                const data = await A.postJSON(`${BASE()}api/commissions/fetch_request.php`, {
                    commission_id: activeId,
                    message:       messageText,
                });

                if (data?.success) {
                    bsModal.hide();
                    A.showSuccessModal('Request Sent!', data.message || 'Your application has been sent to the client.');
                    onComplete?.();
                } else {
                    A.showAlert(alertBox, data?.message || 'Failed to submit request.', false);
                    A.btnReset(freshBtn, 'Send Request');
                }
            } catch (err) {
                console.error('Take commission error:', err);
                A.showAlert(alertBox, 'A network error occurred. Please try again.', false);
                A.btnReset(freshBtn, 'Send Request');
            }
        });
    };

}(window));