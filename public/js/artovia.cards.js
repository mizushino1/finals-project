/* ══════════════════════════════════════════════════════════════
   artovia.cards.js — Commission card HTML builders
   Requires: artovia.core.js
══════════════════════════════════════════════════════════════ */

(function (global) {
    'use strict';

    const A = global.Artovia;
    if (!A) { console.error('artovia.cards.js: Artovia core not loaded.'); return; }

    // ── Action button router ───────────────────────────────────
    // Returns the appropriate CTA button HTML based on role & status.

    function buildActionBtn(c) {
        const role     = A.config.currentRole;
        const statusId = parseInt(c.status_id);
        const baseUrl  = A.config.baseUrl;

        if (role === 'artist') {
            if (statusId === 1) {
                return `<button type="button"
                            class="btn-artovia-primary take-commission-btn py-1 px-3 fs-fluid-xs rounded-2"
                            data-commission-id="${c.commission_id}">Take</button>`;
            }
            return `<a href="${baseUrl}commissions/view?id=${c.commission_id}"
                       class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">View</a>`;
        }

        if (role === 'user' || role === 'client') {
            if (c.request_id && statusId === 1) {
                return `
                    <div class="w-100 mt-2 bg-light p-2 rounded border fs-fluid-xxs text-dark mb-2">
                        <strong class="text-muted d-block mb-1 text-start" style="font-size:0.75rem;">Artist Pitch:</strong>
                        <p class="text-secondary m-0 text-start" style="font-size:0.85rem;font-style:italic;">
                            "${c.message || 'No custom pitch message attached.'}"
                        </p>
                    </div>
                    <div class="d-flex gap-2 w-100">
                        <button type="button"
                                class="btn btn-sm btn-outline decline-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                data-request-id="${c.request_id}">Decline</button>
                        <button type="button"
                                class="btn btn-sm btn-fill assign-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                data-commission-id="${c.commission_id}"
                                data-request-id="${c.request_id}">Accept</button>
                    </div>`;
            }
            if (statusId === 6) {
                return `<button type="button"
                            class="btn btn-warning text-dark btn-review-trigger py-1 px-3 fs-fluid-xs rounded-2 fw-semibold shadow-sm"
                            data-commission-id="${c.commission_id}">
                            <i class="bi bi-star-fill me-1"></i>Review
                        </button>`;
            }
            return `<button type="button"
                        class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2"
                        data-bs-toggle="modal" data-bs-target="#editCommissionModal"
                        data-commission-id="${c.commission_id}">Manage</button>`;
        }

        if (role === 'admin') {
            return `<button type="button"
                        class="btn-danger text-white py-1 px-3 fs-fluid-xs rounded-2"
                        data-bs-toggle="modal" data-bs-target="#editCommissionModal"
                        data-commission-id="${c.commission_id}">Moderate</button>`;
        }

        // Guest fallback
        return `<a href="${baseUrl}commissions/view?id=${c.commission_id}"
                   class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">View Details</a>`;
    }

    // ── Shared card body sections ──────────────────────────────

    function buildCardHeader(name, dateStr, statusClass, statusText, avatarHtml) {
        return `
            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    ${avatarHtml}
                    <div class="text-truncate">
                        <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${name}</p>
                        <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                    </div>
                </div>
                <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${statusClass}">
                    ${statusText}
                </span>
            </div>`;
    }

    function buildBudgetFooter(budgetDisplay, actionBtn) {
        return `
            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                <div>
                    <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                    <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                </div>
                ${actionBtn}
            </div>`;
    }

    // ── Main commission card ───────────────────────────────────
    // Used in the main commission grid. compact=true renders a narrower
    // fixed-width card for the horizontal scroll strips.

    A.buildCard = function (c, index, compact = false) {
        const clientName   = c.posted_by ?? 'Anonymous Client';
        const [bg, fg]     = A.PALETTE[index % A.PALETTE.length];
        const status       = A.getStatusConfig(c.status_id);
        const { title, body } = A.parseDescription(c.description);
        const budgetDisplay   = A.formatBudget(c.price);
        const dateStr         = A.formatDate(c.commission_date);
        const avatarHtml      = A.makeAvatar(clientName, index, c.client_avatar_url ?? c.avatar_url ?? null);
        const categoryBadge   = A.makeCategoryBadge(c.category_name, bg, fg);
        const actionBtn       = buildActionBtn(c);

        const refUrl = c.image_url ?? c.reference_image ?? c.reference_url ?? null;
        const refImg = refUrl
            ? `<img src="${A.config.baseUrl}${refUrl}"
                    alt="Reference"
                    class="rounded-2 object-fit-cover flex-shrink-0"
                    style="width:clamp(56px,22%,88px);aspect-ratio:1/1;border:1px solid var(--border-color,#ffffff18);background:var(--bg-subtle,#1a1a1a);"
                    onerror="this.style.display='none'">`
            : '';

        const clamp = compact ? 2 : 3;
        const bodyHtml = `
            <div class="flex-grow-1 ${compact ? 'mb-3' : 'mb-4'}">
                ${categoryBadge}
                <div class="d-flex align-items-start gap-2">
                    <div class="flex-grow-1 overflow-hidden">
                        ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate">${title}</p>` : ''}
                        <p class="text-muted small m-0"
                           style="display:-webkit-box;-webkit-line-clamp:${clamp};-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">
                            ${body}
                        </p>
                    </div>
                    ${refImg}
                </div>
            </div>`;

        const inner = `
            <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                ${buildCardHeader(clientName, dateStr, status.class, status.text, avatarHtml)}
                ${bodyHtml}
                ${buildBudgetFooter(budgetDisplay, actionBtn)}
            </div>`;

        return compact
            ? `<div style="width:280px;flex-shrink:0;">${inner}</div>`
            : `<div class="col">${inner}</div>`;
    };

    // ── Pending request card (client view — inbound artist bids) ──

    A.buildRequestCard = function (r, index) {
        const artistName  = r.artist_username ?? 'Unknown Artist';
        const [bg, fg]    = A.PALETTE[index % A.PALETTE.length];
        const { title }   = A.parseDescription(r.commission_description ?? '');
        const pitch       = r.pitch_message?.trim() ?? null;
        const budgetDisplay = A.formatBudget(r.price, 'Open');
        const dateStr     = A.formatDate(r.requested_at, 'Recently');
        const avatarHtml  = A.makeAvatar(artistName, index, r.artist_avatar_url ?? r.avatar_url ?? null);
        const categoryBadge = A.makeCategoryBadge(r.category_name, bg, fg);

        const pitchBlock = pitch
            ? `<div class="bg-light rounded border p-2 mb-2" style="font-size:0.8rem;font-style:italic;color:var(--color-text-muted,#6c757d);">"${pitch}"</div>`
            : `<p class="text-muted mb-2" style="font-size:0.78rem;font-style:italic;">No pitch message attached.</p>`;

        return `
            <div style="width:300px;flex-shrink:0;">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                    <div class="d-flex align-items-center gap-2 mb-2 overflow-hidden">
                        ${avatarHtml}
                        <div class="text-truncate">
                            <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${artistName}</p>
                            <small class="text-muted fs-fluid-xxs">Applied ${dateStr}</small>
                        </div>
                    </div>
                    <div class="flex-grow-1 mb-2">
                        ${categoryBadge}
                        ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate" title="${title}">${title}</p>` : ''}
                        ${pitchBlock}
                    </div>
                    <div class="pt-2 border-top mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                                <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline decline-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                    data-request-id="${r.request_id}">Decline</button>
                            <button type="button"
                                    class="btn btn-sm btn-fill-static assign-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                    data-commission-id="${r.commission_id}"
                                    data-request-id="${r.request_id}">Accept</button>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    // ── Artist pending card (artist view — outgoing applications) ──

    A.buildArtistPendingCard = function (r, index) {
        const ownerName   = r.owner_username ?? 'Client';
        const [bg, fg]    = A.PALETTE[index % A.PALETTE.length];
        const { title }   = A.parseDescription(r.commission_description ?? '');
        const pitch       = r.pitch_message?.trim() ?? null;
        const budgetDisplay = A.formatBudget(r.price, 'Open');
        const dateStr     = A.formatDate(r.requested_at, 'Recently');
        const avatarHtml  = A.makeAvatar(ownerName, index, r.owner_avatar_url ?? r.avatar_url ?? null);
        const categoryBadge = A.makeCategoryBadge(r.category_name, bg, fg);

        const pitchSnippet = pitch
            ? `<div class="bg-light rounded border p-2 mb-2" style="font-size:0.78rem;font-style:italic;color:var(--color-text-muted,#6c757d);">
                   "${pitch.length > 80 ? pitch.slice(0, 80) + '…' : pitch}"
               </div>`
            : '';

        return `
            <div style="width:300px;flex-shrink:0;">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            ${avatarHtml}
                            <div class="text-truncate">
                                <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${ownerName}</p>
                                <small class="text-muted fs-fluid-xxs">Applied ${dateStr}</small>
                            </div>
                        </div>
                        <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase bg-warning text-dark border border-warning">
                            Pending
                        </span>
                    </div>
                    <div class="flex-grow-1 mb-2">
                        ${categoryBadge}
                        ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate" title="${title}">${title}</p>` : ''}
                        ${pitchSnippet}
                    </div>
                    <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between">
                        <div>
                            <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                            <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger artist-cancel-request-btn py-1 px-3 fs-fluid-xs rounded-2"
                                data-request-id="${r.request_id}">Withdraw</button>
                    </div>
                </div>
            </div>`;
    };

    // ── Artist accepted card (artist view — active/completed work) ──

    A.buildArtistAcceptedCard = function (c, index) {
        const ownerName   = c.owner_username ?? 'Client';
        const [bg, fg]    = A.PALETTE[index % A.PALETTE.length];
        const { title }   = A.parseDescription(c.commission_description ?? '');
        const statusId    = parseInt(c.commission_status_id);
        const status      = A.getStatusConfig(statusId);
        const budgetDisplay = A.formatBudget(c.price, 'Open');
        const dateStr     = A.formatDate(c.commission_date);
        const avatarHtml  = A.makeAvatar(ownerName, index, c.owner_avatar_url ?? c.avatar_url ?? null);
        const categoryBadge = A.makeCategoryBadge(c.category_name, bg, fg);

        let actionBtn = '';
        if (statusId === 3) {
            actionBtn = `<button type="button"
                            class="btn btn-sm btn-outline artist-progress-btn py-1 px-3 fs-fluid-xs rounded-2"
                            data-commission-id="${c.commission_id}">Start Work</button>`;
        } else if (statusId === 5) {
            actionBtn = `<button type="button"
                            class="btn btn-sm btn-outline artist-complete-btn py-1 px-3 fs-fluid-xs rounded-2"
                            data-commission-id="${c.commission_id}">Mark Complete</button>`;
        } else if (statusId === 6) {
            actionBtn = `<span class="text-success fw-semibold fs-fluid-xxs">✓ Completed</span>`;
        }

        return `
            <div style="width:300px;flex-shrink:0;">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            ${avatarHtml}
                            <div class="text-truncate">
                                <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${ownerName}</p>
                                <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                            </div>
                        </div>
                        <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                            ${status.text}
                        </span>
                    </div>
                    <div class="flex-grow-1 mb-2">
                        ${categoryBadge}
                        ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate" title="${title}">${title}</p>` : ''}
                    </div>
                    <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between">
                        <div>
                            <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                            <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                        </div>
                        ${actionBtn}
                    </div>
                </div>
            </div>`;
    };

}(window));