/* ══════════════════════════════════════════════════════════════
   artovia.api.js — Commission action handlers (fetch wrappers)
   Requires: artovia.core.js
══════════════════════════════════════════════════════════════ */

(function (global) {
    'use strict';

    const A = global.Artovia;
    if (!A) { console.error('artovia.api.js: Artovia core not loaded.'); return; }

    const BASE = () => A.config.baseUrl;

    // ── Generic action helper ──────────────────────────────────
    // Disables a button, POSTs JSON, re-enables on failure.

    async function runAction(btn, loadingText, payload, onSuccess, onFail) {
        const original = btn.textContent;
        A.btnLoading(btn, loadingText);
        try {
            const data = await A.postJSON(`${BASE()}api/commissions/update_status.php`, payload);
            if (data?.success) {
                onSuccess(data);
            } else {
                alert(data?.message || 'Something went wrong.');
                A.btnReset(btn, original);
                onFail?.();
            }
        } catch (err) {
            console.error(err);
            alert('A network error occurred. Please try again.');
            A.btnReset(btn, original);
            onFail?.();
        }
    }

    // ── Accept artist (client) ─────────────────────────────────

    A.handleAssignArtist = async function (btn, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        const requestId    = parseInt(btn.getAttribute('data-request-id'));
        if (!commissionId || !requestId) return;

        if (!confirm('Accept this artist? All other pending requests for this commission will be automatically declined.')) return;

        await runAction(
            btn,
            'Accepting…',
            { commission_id: commissionId, request_id: requestId, status: 'accepted' },
            (data) => {
                A.showSuccessModal('Artist Accepted!', data.message || 'The commission is now in progress.');
                onComplete?.();
            }
        );
    };

    // ── Decline artist (client) ────────────────────────────────

    A.handleDeclineArtist = async function (btn, onComplete) {
        const requestId = parseInt(btn.getAttribute('data-request-id'));
        if (!requestId) return;

        if (!confirm("Are you sure you want to decline this artist's application?")) return;

        await runAction(
            btn,
            'Dropping…',
            { request_id: requestId, status: 'rejected' },
            (data) => {
                A.showSuccessModal('Request Declined', data.message || 'The request has been updated.');
                onComplete?.();
            }
        );
    };

    // ── Withdraw own request (artist) ──────────────────────────

    A.handleCancelRequest = async function (btn, onComplete) {
        const requestId = parseInt(btn.getAttribute('data-request-id'));
        if (!requestId) return;

        if (!confirm('Withdraw this request? The client will no longer see your application.')) return;

        await runAction(
            btn,
            'Withdrawing…',
            { request_id: requestId, status: 'cancelled' },
            (data) => {
                A.showSuccessModal('Request Withdrawn', data.message || 'Your application has been cancelled.');
                onComplete?.();
            }
        );
    };

    // ── Update commission status (artist) ──────────────────────

    A.handleArtistStatusUpdate = async function (btn, newStatus, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        if (!commissionId) return;

        const labelMap  = { in_progress: 'In Progress', completed: 'Completed' };
        const confirmMsg = newStatus === 'in_progress'
            ? 'Mark this commission as In Progress?'
            : 'Mark this commission as Completed? This cannot be undone.';

        if (!confirm(confirmMsg)) return;

        await runAction(
            btn,
            'Updating…',
            { commission_id: commissionId, status: newStatus },
            (data) => {
                A.showSuccessModal(`Marked as ${labelMap[newStatus]}!`, data.message || `Commission updated to ${labelMap[newStatus]}.`);
                onComplete?.();
            }
        );
    };

    // ── Take commission (artist — opens pitch form modal) ──────

    A.handleTakeCommission = function (btn, onComplete) {
        const commissionId = parseInt(btn.getAttribute('data-commission-id'));
        if (!commissionId) return;

        const MODAL_ID = 'artvTakeCommissionModal';
        let modalEl = document.getElementById(MODAL_ID);

        // Lazily create modal on first use
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

        // Store current commission ID on the modal element
        modalEl.dataset.activeCommissionId = commissionId;

        // Reset state
        const textarea  = document.getElementById(`${MODAL_ID}Message`);
        const alertBox  = document.getElementById(`${MODAL_ID}Alert`);
        const submitBtn = document.getElementById(`${MODAL_ID}Submit`);

        textarea.value = '';
        alertBox.classList.add('d-none');
        A.btnReset(submitBtn, 'Send Request');

        // Clone submit button to clear any stale event listeners
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