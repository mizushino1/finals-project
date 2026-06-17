/* ══════════════════════════════════════════════════════════════
   artovia.core.js — Global namespace, UI helpers & utilities
   Load this script first on any page that uses Artovia JS.
══════════════════════════════════════════════════════════════ */

(function (global) {
    'use strict';

    // Guard against double-loading
    if (global.Artovia) return;

    const Artovia = {};

    // ── Config ─────────────────────────────────────────────────

    Artovia.config = {
        baseUrl:     window.BASE_URL  ?? './',
        currentRole: window.USER_ROLE ?? 'guest',
    };

    Artovia.PALETTE = [
        ['#e8d5b0', '#a8834a'],
        ['#d4e8d0', '#3a7a4a'],
        ['#d0dce8', '#3a5a7a'],
        ['#e8d0e0', '#7a3a5a'],
    ];

    // ── Visibility helpers ─────────────────────────────────────

    Artovia.show = function (el) { el?.classList.remove('d-none'); };
    Artovia.hide = function (el) { el?.classList.add('d-none');    };

    // ── Avatar ─────────────────────────────────────────────────
    // Shows a real photo when avatarUrl is present;
    // falls back to a coloured initials circle.

    Artovia.makeAvatar = function (name, index, avatarUrl = null) {
        const [bg, fg] = Artovia.PALETTE[index % Artovia.PALETTE.length];
        const letter   = (name ?? '').trim()[0]?.toUpperCase() ?? '?';

        if (avatarUrl) {
            return `
                <img src="${Artovia.config.baseUrl}${avatarUrl}"
                     class="rounded-circle flex-shrink-0 object-fit-cover"
                     style="width:38px;height:38px;border:1px solid ${fg}33;"
                     alt="${name}"
                     onerror="this.replaceWith(this.nextElementSibling)">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:38px;height:38px;background:${bg};border:1px solid ${fg}33;display:none!important;">
                    <span class="fs-fluid-sm fw-bold" style="font-family:var(--font-ui);color:${fg};">${letter}</span>
                </div>`;
        }

        return `
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:38px;height:38px;background:${bg};border:1px solid ${fg}33;">
                <span class="fs-fluid-sm fw-bold" style="font-family:var(--font-ui);color:${fg};">${letter}</span>
            </div>`;
    };

    // ── Status config ──────────────────────────────────────────

    Artovia.getStatusConfig = function (statusId) {
        const map = {
            1: { text: 'Active',      class: 'artist-card__badge--open' },
            2: { text: 'Pending',     class: 'bg-warning text-dark border border-warning' },
            3: { text: 'Accepted',    class: 'theme-fill text-dark border border-info' },
            4: { text: 'Rejected',    class: 'artist-card__badge--closed' },
            5: { text: 'In Progress', class: 'theme-fill text-dark border border-primary' },
            6: { text: 'Completed',   class: 'theme-fill text-dark border border-success' },
            7: { text: 'Cancelled',   class: 'bg-danger text-white border border-secondary' },
        };
        return map[parseInt(statusId)] ?? { text: 'Unknown', class: 'bg-dark text-white' };
    };

    // ── Formatters ─────────────────────────────────────────────

    Artovia.formatBudget = function (price, fallback = 'Open Budget') {
        const n = parseFloat(price ?? 0);
        return n > 0 ? `₱${n.toLocaleString('en-PH', { minimumFractionDigits: 2 })}` : fallback;
    };

    Artovia.formatDate = function (dateStr, fallback = 'Recent') {
        return dateStr
            ? new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : fallback;
    };

    // ── Description parser ─────────────────────────────────────
    // Splits raw text where the first paragraph is treated as the title.

    Artovia.parseDescription = function (raw) {
        const parts = (raw ?? '').split('\n\n');
        if (parts.length >= 2) {
            return { title: parts[0].trim(), body: parts.slice(1).join('\n\n').trim() };
        }
        return { title: '', body: (raw ?? '').trim() };
    };

    // ── Category badge HTML ────────────────────────────────────

    Artovia.makeCategoryBadge = function (category, bg, fg) {
        if (!category) return '';
        return `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                      style="background:${bg};color:${fg};border:1px solid ${fg}33;letter-spacing:0.04em;">
                    ${category}
                </span>`;
    };

    // ── Inline alert helper ────────────────────────────────────

    Artovia.showAlert = function (alertEl, message, isSuccess = false) {
        if (!alertEl) return;
        alertEl.textContent = message;
        alertEl.className   = `alert fs-fluid-xs ${isSuccess ? 'alert-success' : 'alert-danger'}`;
        Artovia.show(alertEl);
    };

    // ── Success modal ──────────────────────────────────────────
    // Lazily creates a single shared modal element and reuses it.

    Artovia.showSuccessModal = function (title, message) {
        const MODAL_ID = 'artvSuccessModal';
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
                            <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle mx-auto mb-3"
                                 style="width:60px;height:60px;font-size:1.75rem;">✓</div>
                            <h4 class="fw-bold mb-2" id="${MODAL_ID}Title" style="font-family:var(--font-ui);">Action Complete!</h4>
                            <p class="text-muted small mb-4 px-2" id="${MODAL_ID}Message"></p>
                            <button type="button" class="btn-artovia-primary w-100 py-2 rounded-3" data-bs-dismiss="modal">
                                Perfect, thanks!
                            </button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }

        document.getElementById(`${MODAL_ID}Title`).textContent   = title;
        document.getElementById(`${MODAL_ID}Message`).textContent = message;

        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(modalEl).show();
        } else {
            alert(message);
        }
    };

    // ── Button loading state helpers ───────────────────────────

    Artovia.btnLoading = function (btn, loadingText) {
        btn.disabled    = true;
        btn.textContent = loadingText;
    };

    Artovia.btnReset = function (btn, originalText) {
        btn.disabled    = false;
        btn.textContent = originalText;
    };

    // ── JSON POST helper ───────────────────────────────────────

    Artovia.postJSON = async function (url, payload) {
        const res = await fetch(url, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(payload),
        });
        return res.json();
    };

    // ── HTML escaping ──────────────────────────────────────────

    Artovia.escapeHtml = function (str) {
        if (str == null) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    function renderRatingStars(rating) {
        const numericRating = parseFloat(rating) || 0;
        let starsHtml = '';
        
        // Loop through 5 stars
        for (let i = 1; i <= 5; i++) {
            if (numericRating >= i) {
                // Full Star
                starsHtml += `<i class="bi bi-star-fill text-warning me-1"></i>`;
            } else if (numericRating > i - 1 && numericRating < i) {
                // Half Star (e.g., 4.5)
                starsHtml += `<i class="bi bi-star-half text-warning me-1"></i>`;
            } else {
                // Empty Star
                starsHtml += `<i class="bi bi-star text-muted me-1"></i>`;
            }
        }
        
        return starsHtml;
    }

    // ── Expose globally ────────────────────────────────────────

    global.Artovia = Artovia;

}(window));