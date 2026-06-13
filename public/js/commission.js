/* ══════════════════════════════════════════════════════════════
    commissions.js — Artovia Commission Job Board (With Live Actions)
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    let allCommissions = [];

    // Safely pull global configuration parameters with robust fallbacks
    const APP_BASE_URL = window.BASE_URL ?? './';
    const CURRENT_ROLE = window.USER_ROLE ?? 'guest';

    // Main grid elements
    const grid = document.getElementById('commissionGrid');
    const gridLoading = document.getElementById('commissionGridLoading');
    const gridError = document.getElementById('commissionGridError');
    const gridEmpty = document.getElementById('commissionGridEmpty');

    // Pending strip elements (user/client — received bids)
    const pendingStrip = document.getElementById('pendingGrid');
    const pendingStripLoading = document.getElementById('pendingGridLoading');
    const pendingSection = document.getElementById('pendingSection');

    // Artist strip elements
    const artistPendingSection = document.getElementById('artistPendingSection');
    const artistPendingGrid = document.getElementById('artistPendingGrid');
    const artistPendingLoading = document.getElementById('artistPendingLoading');
    const artistPendingEmpty = document.getElementById('artistPendingEmpty');
    const artistPendingBadge = document.getElementById('artistPendingBadge');
    const artistAcceptedSection = document.getElementById('artistAcceptedSection');
    const artistAcceptedGrid = document.getElementById('artistAcceptedGrid');
    const artistAcceptedLoading = document.getElementById('artistAcceptedLoading');
    const artistAcceptedEmpty = document.getElementById('artistAcceptedEmpty');
    const artistAcceptedBadge = document.getElementById('artistAcceptedBadge');

    // Controls
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const sortSelect = document.getElementById('sortSelect');
    const resultsNum = document.getElementById('resultsNumber');
    const clearBtn = document.getElementById('clearFilters');

    // Modal elements
    const submitBtn = document.getElementById('submitCommissionBtn');
    const titleInput = document.getElementById('commissionTitle');
    const descInput = document.getElementById('commissionDescription');
    const budgetInput = document.getElementById('commissionBudget');
    const categoryInput = document.getElementById('commissionCategory');
    const imageFile = document.getElementById('commissionImageFile');
    const imageName = document.getElementById('commissionImageName');
    const formAlert = document.getElementById('commissionFormAlert');

    // Avatar palette
    const PALETTE = [
        ['#e8d5b0', '#a8834a'], ['#d4e8d0', '#3a7a4a'],
        ['#d0dce8', '#3a5a7a'], ['#e8d0e0', '#7a3a5a']
    ];

    // ── Avatar helper ─────────────────────────────────────────
    // Mirrors browse.js: shows a real photo when avatar_url is present,
    // otherwise falls back to a coloured initials circle.
    function makeAvatar(name, index, avatarUrl = null) {
        const [bg, fg] = PALETTE[index % PALETTE.length];
        if (avatarUrl) {
            return `
            <img src="${APP_BASE_URL}${avatarUrl}"
                 class="rounded-circle flex-shrink-0 object-fit-cover"
                 style="width:38px; height:38px; border:1px solid ${fg}33;"
                 alt="${name}"
                 onerror="this.replaceWith(this.nextElementSibling)"
            >
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:38px; height:38px; background:${bg}; border:1px solid ${fg}33; display:none!important;">
                <span class="fs-fluid-sm fw-bold" style="font-family:var(--font-ui); color:${fg};">${(name.trim()[0] ?? '?').toUpperCase()}</span>
            </div>`;
        }
        const letter = name.trim() ? name.trim()[0].toUpperCase() : '?';
        return `
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:38px; height:38px; background:${bg}; border:1px solid ${fg}33;">
                <span class="fs-fluid-sm fw-bold" style="font-family:var(--font-ui); color:${fg};">${letter}</span>
            </div>`;
    }

    // ── Null-safe class helpers ───────────────────────────────

    function show(el) { el?.classList.remove('d-none'); }
    function hide(el) { el?.classList.add('d-none'); }

    // ── Helpers ───────────────────────────────────────────────

    function getStatusConfig(statusId) {
        switch (parseInt(statusId)) {
            case 1: return { text: 'Active', class: 'artist-card__badge--open' };
            case 2: return { text: 'Pending', class: 'bg-warning text-dark border border-warning' };
            case 3: return { text: 'Accepted', class: 'theme-fill text-dark border border-info' };
            case 4: return { text: 'Rejected', class: 'artist-card__badge--closed' };
            case 5: return { text: 'In Progress', class: 'theme-fill text-dark border border-primary' };
            case 6: return { text: 'Completed', class: 'theme-fill text-dark border border-success' };
            case 7: return { text: 'Cancelled', class: 'bg-danger text-white border border-secondary' };
            default: return { text: 'Unknown', class: 'bg-dark text-white' };
        }
    }

    function parseDescription(raw) {
        const parts = (raw ?? '').split('\n\n');
        if (parts.length >= 2) {
            return { title: parts[0].trim(), body: parts.slice(1).join('\n\n').trim() };
        }
        return { title: '', body: (raw ?? '').trim() };
    }

    function buildCard(c, index, compact = false) {
        const clientName = c.posted_by ?? 'Anonymous Client';
        const budget = parseFloat(c.price ?? 0);
        const [bg, fg] = PALETTE[index % PALETTE.length];
        const status = getStatusConfig(c.status_id);
        const { title, body } = parseDescription(c.description);
        const category = c.category_name ?? null;

        const budgetDisplay = budget > 0
            ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
            : 'Open Budget';

        const dateStr = c.commission_date
            ? new Date(c.commission_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Recent';

        const avatarHtml = makeAvatar(clientName, index, c.client_avatar_url ?? c.avatar_url ?? null);

        const categoryBadge = category
            ? `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                    style="background:${bg}; color:${fg}; border:1px solid ${fg}33; letter-spacing:0.04em;">
                    ${category}
               </span>`
            : '';

        // Contextual action button router
        let actionBtn = '';

        if (CURRENT_ROLE === 'artist') {
            // Artists should only see "Take" if the commission is actively Open (status 1)
            if (parseInt(c.status_id) === 1) {
                actionBtn = `<button type="button" 
                                class="btn-artovia-primary take-commission-btn py-1 px-3 fs-fluid-xs rounded-2"
                                data-commission-id="${c.commission_id}">
                                Take
                             </button>`;
            } else {
                // If the commission is already accepted/in progress, show non-interactive view
                actionBtn = `<a href="${APP_BASE_URL}commissions/view?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">View</a>`;
            }

        } else if (CURRENT_ROLE === 'user' || CURRENT_ROLE === 'client') {
            // Client/User view routing
            if (c.request_id && parseInt(c.status_id) === 1) {
                // Display the active inbound artist pitch and its decision paths
                actionBtn = `
                    <div class="w-100 mt-2 bg-light p-2 rounded border fs-fluid-xxs text-dark mb-2">
                        <strong class="text-muted d-block mb-1 text-start" style="font-size: 0.75rem;">Artist Pitch:</strong>
                        <p class="text-secondary m-0 text-start italic" style="font-size: 0.85rem; font-style: italic;">
                            "${c.message || 'No custom pitch message attached.'}"
                        </p>
                    </div>
                    <div class="d-flex gap-2 w-100">
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger decline-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                data-request-id="${c.request_id}">
                                Decline
                        </button>
                        <button type="button" 
                                class="btn btn-sm btn-success assign-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                data-commission-id="${c.commission_id}" 
                                data-request-id="${c.request_id}">
                                Accept
                        </button>
                    </div>`;
            } else {
                // Default fallback option for a user's posted management index if no bids exist yet
                actionBtn = `<a href="${APP_BASE_URL}commissions/manage?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">Manage</a>`;
            }

        } else if (CURRENT_ROLE === 'admin') {
            actionBtn = `<a href="${APP_BASE_URL}commissions/manage?id=${c.commission_id}" class="btn-danger text-white py-1 px-3 fs-fluid-xs rounded-2">Moderate</a>`;

        } else {
            // True Guest/Logged-out user fallback route options
            actionBtn = `<a href="${APP_BASE_URL}commissions/view?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">View Details</a>`;
        }

        if (compact) {
            return `
                <div style="width:280px; flex-shrink:0;">
                    <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                ${avatarHtml}
                                <div class="text-truncate">
                                    <p class="m-0 fw-bold  fs-fluid-sm text-truncate lh-1">${clientName}</p>
                                    <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                                </div>
                            </div>
                            <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                                ${status.text}
                            </span>
                        </div>
                        <div class="flex-grow-1 mb-3">
                            ${categoryBadge}
                            ${title ? `<p class="m-0 fw-semibold  fs-fluid-xs mb-1 text-truncate">${title}</p>` : ''}
                            <p class="text-muted small m-0" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">
                                ${body}
                            </p>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                            <div>
                                <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                                <p class="m-0 fw-bold  fs-fluid-sm">${budgetDisplay}</p>
                            </div>
                            ${actionBtn}
                        </div>
                    </div>
                </div>`;
        }

        return `
            <div class="col">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            ${avatarHtml}
                            <div class="text-truncate">
                                <p class="m-0 fw-bold  fs-fluid-sm text-truncate lh-1">${clientName}</p>
                                <small class="text-muted fs-fluid-xxs">${dateStr}</small>
                            </div>
                        </div>
                        <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                            ${status.text}
                        </span>
                    </div>
                    <div class="flex-grow-1 mb-4">
                        ${categoryBadge}
                        ${title ? `<p class="m-0 fw-semibold  fs-fluid-xs mb-1 text-truncate">${title}</p>` : ''}
                        <p class="text-muted small m-0" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">
                            ${body}
                        </p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                        <div>
                            <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                            <p class="m-0 fw-bold  fs-fluid-sm">${budgetDisplay}</p>
                        </div>
                        ${actionBtn}
                    </div>
                </div>
            </div>`;
    }

    // ── Pending Requests Strip ────────────────────────────────
    // Fetches from commission_request_tbl via fetch_pending_requests.php.
    // Only shown to users/clients (commission owners) and admins.

    async function loadPendingRequests() {
        const showPending = CURRENT_ROLE === 'user' || CURRENT_ROLE === 'client' || CURRENT_ROLE === 'admin';

        if (!showPending) {
            hide(pendingSection);
            return;
        }

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/fetch_pending_requests.php`);
            const data = await res.json();

            hide(pendingStripLoading);

            if (!data || !data.success || !Array.isArray(data.data) || data.data.length === 0) {
                hide(pendingSection);
                return;
            }

            if (pendingStrip) {
                pendingStrip.innerHTML = data.data.map((r, i) => buildRequestCard(r, i)).join('');
                show(pendingStrip);
            }
            show(pendingSection);

        } catch (err) {
            console.error('Pending requests load error:', err);
            hide(pendingStripLoading);
            hide(pendingSection);
        }
    }

    // Builds a compact card for an inbound artist request
    function buildRequestCard(r, index) {
        const artistName = r.artist_username ?? 'Unknown Artist';
        const [bg, fg] = PALETTE[index % PALETTE.length];
        const category = r.category_name ?? null;
        const pitch = r.pitch_message ? r.pitch_message.trim() : null;
        const budget = parseFloat(r.price ?? 0);
        const { title } = parseDescription(r.commission_description ?? '');

        const budgetDisplay = budget > 0
            ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
            : 'Open';

        const dateStr = r.requested_at
            ? new Date(r.requested_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Recently';

        const avatarHtml = makeAvatar(artistName, index, r.artist_avatar_url ?? r.avatar_url ?? null);

        const categoryBadge = category
            ? `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                    style="background:${bg}; color:${fg}; border:1px solid ${fg}33; letter-spacing:0.04em;">
                    ${category}
               </span>`
            : '';

        const pitchBlock = pitch
            ? `<div class="bg-light rounded border p-2 mb-2" style="font-size:0.8rem; font-style:italic; color: var(--color-text-muted, #6c757d);">
                   "${pitch}"
               </div>`
            : `<p class="text-muted mb-2" style="font-size:0.78rem; font-style:italic;">No pitch message attached.</p>`;

        return `
            <div style="width:300px; flex-shrink:0;">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">

                    <!-- Artist identity row -->
                    <div class="d-flex align-items-center gap-2 mb-2 overflow-hidden">
                        ${avatarHtml}
                        <div class="text-truncate">
                            <p class="m-0 fw-bold fs-fluid-sm text-truncate lh-1">${artistName}</p>
                            <small class="text-muted fs-fluid-xxs">Applied ${dateStr}</small>
                        </div>
                    </div>

                    <!-- Commission context -->
                    <div class="flex-grow-1 mb-2">
                        ${categoryBadge}
                        ${title ? `<p class="m-0 fw-semibold fs-fluid-xs mb-1 text-truncate" title="${title}">${title}</p>` : ''}
                        ${pitchBlock}
                    </div>

                    <!-- Budget + actions -->
                    <div class="pt-2 border-top mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <p class="m-0 text-muted fs-fluid-xxs text-uppercase" style="letter-spacing:0.05em;">Budget</p>
                                <p class="m-0 fw-bold fs-fluid-sm">${budgetDisplay}</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger decline-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                    data-request-id="${r.request_id}">
                                Decline
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-success assign-artist-btn flex-grow-1 py-1 fs-fluid-xs rounded-2"
                                    data-commission-id="${r.commission_id}"
                                    data-request-id="${r.request_id}">
                                Accept
                            </button>
                        </div>
                    </div>

                </div>
            </div>`;
    }

    // ── Artist: Pending Request Card ─────────────────────────
    // Shows a commission the artist applied for while status is still pending (status_id 2)

    function buildArtistPendingCard(r, index) {
        const ownerName = r.owner_username ?? 'Client';
        const [bg, fg] = PALETTE[index % PALETTE.length];
        const budget = parseFloat(r.price ?? 0);
        const { title } = parseDescription(r.commission_description ?? '');
        const category = r.category_name ?? null;
        const pitch = r.pitch_message ? r.pitch_message.trim() : null;

        const budgetDisplay = budget > 0
            ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
            : 'Open';

        const dateStr = r.requested_at
            ? new Date(r.requested_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Recently';

        const avatarHtml = makeAvatar(ownerName, index, r.owner_avatar_url ?? r.avatar_url ?? null);

        const categoryBadge = category
            ? `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                    style="background:${bg}; color:${fg}; border:1px solid ${fg}33; letter-spacing:0.04em;">
                    ${category}
               </span>`
            : '';

        const pitchSnippet = pitch
            ? `<div class="bg-light rounded border p-2 mb-2" style="font-size:0.78rem; font-style:italic; color:var(--color-text-muted,#6c757d);">
                   "${pitch.length > 80 ? pitch.slice(0, 80) + '…' : pitch}"
               </div>`
            : '';

        return `
            <div style="width:300px; flex-shrink:0;">
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
                                data-request-id="${r.request_id}">
                            Withdraw
                        </button>
                    </div>

                </div>
            </div>`;
    }

    // ── Artist: Accepted Commission Card ─────────────────────
    // Shows a commission assigned to this artist with action buttons (In Progress / Complete)

    function buildArtistAcceptedCard(c, index) {
        const ownerName = c.owner_username ?? 'Client';
        const [bg, fg] = PALETTE[index % PALETTE.length];
        const budget = parseFloat(c.price ?? 0);
        const { title } = parseDescription(c.commission_description ?? '');
        const category = c.category_name ?? null;
        const statusId = parseInt(c.commission_status_id);
        const status = getStatusConfig(statusId);

        const budgetDisplay = budget > 0
            ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}`
            : 'Open';

        const dateStr = c.commission_date
            ? new Date(c.commission_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
            : 'Recent';

        const avatarHtml = makeAvatar(ownerName, index, c.owner_avatar_url ?? c.avatar_url ?? null);

        const categoryBadge = category
            ? `<span class="badge rounded-pill fs-fluid-xxs fw-semibold text-uppercase mb-2"
                    style="background:${bg}; color:${fg}; border:1px solid ${fg}33; letter-spacing:0.04em;">
                    ${category}
               </span>`
            : '';

        // Determine which action button to show based on current status
        let actionBtn = '';
        if (statusId === 3) {
            // Accepted but not started — artist can mark as In Progress
            actionBtn = `<button type="button"
                            class="btn btn-sm btn-outline artist-progress-btn py-1 px-3 fs-fluid-xs rounded-2"
                            data-commission-id="${c.commission_id}">
                            Start Work
                         </button>`;
        } else if (statusId === 5) {
            // In Progress — artist can mark as Completed
            actionBtn = `<button type="button"
                            class="btn btn-sm btn-outline artist-complete-btn py-1 px-3 fs-fluid-xs rounded-2"
                            data-commission-id="${c.commission_id}">
                            Mark Complete
                         </button>`;
        } else if (statusId === 6) {
            // Already completed — read-only
            actionBtn = `<span class="text-success fw-semibold fs-fluid-xxs">✓ Completed</span>`;
        }

        return `
            <div style="width:300px; flex-shrink:0;">
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
    }

    // ── Load Artist Commission Strips ─────────────────────────

    async function loadArtistCommissions() {
        if (CURRENT_ROLE !== 'artist') {
            hide(artistPendingSection);
            hide(artistAcceptedSection);
            return;
        }

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/fetch_artist_commissions.php`);
            const data = await res.json();

            // ── Pending strip ──
            hide(artistPendingLoading);

            if (data.success && Array.isArray(data.pending) && data.pending.length > 0) {
                if (artistPendingGrid) {
                    artistPendingGrid.innerHTML = data.pending.map((r, i) => buildArtistPendingCard(r, i)).join('');
                    show(artistPendingGrid);
                }
                hide(artistPendingEmpty);
                if (artistPendingBadge) {
                    artistPendingBadge.textContent = data.pending.length;
                    artistPendingBadge.style.display = '';
                }
            } else {
                hide(artistPendingGrid);
                show(artistPendingEmpty);
                if (artistPendingBadge) artistPendingBadge.style.display = 'none';
            }

            // ── Accepted strip ──
            hide(artistAcceptedLoading);

            if (data.success && Array.isArray(data.accepted) && data.accepted.length > 0) {
                if (artistAcceptedGrid) {
                    artistAcceptedGrid.innerHTML = data.accepted.map((c, i) => buildArtistAcceptedCard(c, i)).join('');
                    show(artistAcceptedGrid);
                }
                hide(artistAcceptedEmpty);
                if (artistAcceptedBadge) {
                    artistAcceptedBadge.textContent = data.accepted.length;
                    artistAcceptedBadge.style.display = '';
                }
            } else {
                hide(artistAcceptedGrid);
                show(artistAcceptedEmpty);
                if (artistAcceptedBadge) artistAcceptedBadge.style.display = 'none';
            }

        } catch (err) {
            console.error('Artist commissions load error:', err);
            hide(artistPendingLoading);
            hide(artistAcceptedLoading);
            show(artistPendingEmpty);
            show(artistAcceptedEmpty);
        }
    }

    async function handleCancelRequest(button) {
        const requestId = parseInt(button.getAttribute('data-request-id'));
        if (!requestId) return;

        if (!confirm('Withdraw this request? The client will no longer see your application.')) return;

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Withdrawing…';

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/update_status.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ request_id: requestId, status: 'cancelled' })
            });
            const data = await res.json();

            if (data && data.success) {
                showSuccessModal('Request Withdrawn', data.message || 'Your application has been cancelled.');
                loadArtistCommissions();
            } else {
                alert(data?.message || 'Failed to withdraw request.');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (err) {
            console.error('Cancel request error:', err);
            alert('A network error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    // ── Artist Status Update Handlers ────────────────────────

    async function handleArtistStatusUpdate(button, newStatus) {
        const commissionId = parseInt(button.getAttribute('data-commission-id'));
        if (!commissionId) return;

        const labelMap = { in_progress: 'In Progress', completed: 'Completed' };
        const confirmMsg = newStatus === 'in_progress'
            ? 'Mark this commission as In Progress?'
            : 'Mark this commission as Completed? This cannot be undone.';

        if (!confirm(confirmMsg)) return;

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Updating…';

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/update_status.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ commission_id: commissionId, status: newStatus })
            });
            const data = await res.json();

            if (data && data.success) {
                showSuccessModal(
                    `Marked as ${labelMap[newStatus]}!`,
                    data.message || `Commission updated to ${labelMap[newStatus]}.`
                );
                loadArtistCommissions();
                loadCommissions(); // Refresh main grid so user's view also updates
            } else {
                alert(data?.message || 'Failed to update status.');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (err) {
            console.error('Artist status update error:', err);
            alert('A network error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    // ── Filtering & Sorting ───────────────────────────────────

    function sortData(data, sortVal) {
        const list = [...data];
        switch (sortVal) {
            case 'newest': return list.sort((a, b) => new Date(b.commission_date || 0) - new Date(a.commission_date || 0));
            case 'oldest': return list.sort((a, b) => new Date(a.commission_date || 0) - new Date(b.commission_date || 0));
            case 'budget_desc': return list.sort((a, b) => parseFloat(b.price ?? 0) - parseFloat(a.price ?? 0));
            case 'budget_asc': return list.sort((a, b) => parseFloat(a.price ?? 0) - parseFloat(b.price ?? 0));
            default: return list;
        }
    }

    function applyFilters() {
        const budgetVal = document.querySelector('input[name="budget"]:checked')?.value ?? '0-999999';
        const statusVal = document.querySelector('input[name="status"]:checked')?.value ?? 'all';
        const searchVal = searchInput?.value.trim().toLowerCase() ?? '';
        const sortVal = sortSelect?.value ?? 'newest';

        const [minPrice, maxPrice] = budgetVal.split('-').map(Number);

        let filtered = allCommissions.filter(c => {
            const price = parseFloat(c.price ?? 0);
            const matchesBudget = price >= minPrice && price <= maxPrice;
            const matchesStatus = statusVal === 'all' || parseInt(c.status_id) === parseInt(statusVal);
            const clientName = (c.posted_by || '').toLowerCase();
            const desc = (c.description || '').toLowerCase();
            const category = (c.category_name || '').toLowerCase();
            const matchesSearch = !searchVal
                || clientName.includes(searchVal)
                || desc.includes(searchVal)
                || category.includes(searchVal);
            return matchesBudget && matchesStatus && matchesSearch;
        });

        filtered = sortData(filtered, sortVal);

        if (resultsNum) resultsNum.textContent = filtered.length;

        if (filtered.length === 0) {
            hide(grid);
            show(gridEmpty);
        } else {
            hide(gridEmpty);
            show(grid);
            if (grid) grid.innerHTML = filtered.map((c, i) => buildCard(c, i)).join('');
        }
    }

    // ── Data Fetching ─────────────────────────────────────────

    async function loadCommissions() {
        show(gridLoading);
        hide(grid);
        hide(gridError);
        hide(gridEmpty);

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/fetch.php`);
            const data = await res.json();

            hide(gridLoading);

            if (!data || !data.success) {
                hide(pendingStripLoading);
                hide(pendingSection);
                show(gridError);
                if (resultsNum) resultsNum.textContent = '0';
                return;
            }

            allCommissions = Array.isArray(data.data) ? data.data : [];
            loadPendingRequests();
            loadArtistCommissions();
            applyFilters();

        } catch (err) {
            console.error('Commission load error:', err);
            hide(gridLoading);
            hide(pendingStripLoading);
            hide(pendingSection);
            show(gridError);
        }
    }

    // ── Success Modal Generation Handler ──────────────────────

    function showSuccessModal(title, message) {
        const modalId = 'successNotificationModal';
        let modalEl = document.getElementById(modalId);

        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id = modalId;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');

            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content text-center border-0 shadow-lg p-4 bg-card" style="border-radius: 1rem;">
                        <div class="modal-body">
                            <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle mx-auto mb-3" 
                                 style="width: 60px; height: 60px; font-size: 1.75rem;">
                                 ✓
                            </div>
                            <h4 class="fw-bold mb-2" id="successModalTitle" style="font-family: var(--font-ui);">Action Complete!</h4>
                            <p class="text-muted small mb-4 px-2" id="successModalMessage"></p>
                            <button type="button" class="btn-artovia-primary w-100 py-2 rounded-3" data-bs-dismiss="modal">
                                Perfect, thanks!
                            </button>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }

        document.getElementById('successModalTitle').textContent = title;
        document.getElementById('successModalMessage').textContent = message;

        if (typeof bootstrap !== 'undefined') {
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        } else {
            alert(message);
        }
    }

    // ── Assign Artist / Accept Bid Handler ────────────────────

    async function handleAssignArtist(button) {
        const commissionId = parseInt(button.getAttribute('data-commission-id'));
        const requestId = parseInt(button.getAttribute('data-request-id'));

        if (!commissionId || !requestId) return;

        if (!confirm('Accept this artist? All other pending requests for this commission will be automatically declined.')) {
            return;
        }

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Accepting...';

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/update_status.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    commission_id: commissionId,
                    request_id: requestId,
                    status: 'accepted'
                })
            });
            const data = await res.json();

            if (data && data.success) {
                showSuccessModal('Artist Accepted!', data.message || 'The commission is now in progress.');
                loadCommissions();
                loadPendingRequests();
            } else {
                alert(data?.message || 'Failed to accept artist.');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (err) {
            console.error('Accept handler error:', err);
            alert('A network error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    async function handleDeclineArtist(button) {
        const requestId = parseInt(button.getAttribute('data-request-id'));
        if (!requestId) return;

        if (!confirm('Are you sure you want to decline this artist\'s request application?')) {
            return;
        }

        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Dropping...';

        try {
            const res = await fetch(`${APP_BASE_URL}api/commissions/update_status.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    request_id: requestId,
                    status: 'rejected'
                })
            });
            const data = await res.json();

            if (data && data.success) {
                showSuccessModal('Request Declined', data.message || 'The request has been updated.');
                loadCommissions();
                loadPendingRequests();
            } else {
                alert(data?.message || 'Failed to decline request.');
                button.disabled = false;
                button.textContent = originalText;
            }
        } catch (err) {
            console.error('Decline handler error:', err);
            alert('A network error occurred. Please try again.');
            button.disabled = false;
            button.textContent = originalText;
        }
    }

    // ── Artist Take Commission Request Handler ────────────────

    // ── Artist Take Commission Request Handler (Form Modal) ──
    function handleTakeCommission(button) {
        const commissionId = parseInt(button.getAttribute('data-commission-id'));
        if (!commissionId) return;

        const modalId = 'takeCommissionFormModal';
        let modalEl = document.getElementById(modalId);

        // Dynamic generation of the application text form modal
        if (!modalEl) {
            modalEl = document.createElement('div');
            modalEl.id = modalId;
            modalEl.className = 'modal fade';
            modalEl.setAttribute('tabindex', '-1');
            modalEl.setAttribute('aria-hidden', 'true');

            modalEl.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg p-4 bg-card" style="border-radius: 1rem;">
                        <div class="modal-header border-0 p-0 mb-3">
                            <h5 class="modal-title fw-bold" style="font-family: var(--font-ui);">Apply for Commission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="takeFormAlert" class="alert d-none fs-fluid-xs"></div>
                            <div class="mb-3">
                                <label for="takeCommissionMessage" class="form-label text-muted small fw-semibold">
                                    Introduce yourself or leave a brief pitch for the client:
                                </label>
                                <textarea id="takeCommissionMessage" class="form-control" rows="4" 
                                    placeholder="e.g., I love the concept! I specialize in this specific art style and can finish it within your timeframe..."
                                    style="border-radius: 0.5rem; resize: none; font-size: 0.9rem;"></textarea>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <button type="button" class="btn btn-secondary w-50 py-2 rounded-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" id="submitTakeRequestBtn" class="btn-artovia-primary w-50 py-2 rounded-3">
                                    Send Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modalEl);
        }

        // CRITICAL: Bind the current active commission_id to the modal element directly
        modalEl.setAttribute('data-active-commission-id', commissionId);

        // Reset variables and view states every time the entry form opens
        const textarea = document.getElementById('takeCommissionMessage');
        const alertBox = document.getElementById('takeFormAlert');
        const submitBtn = document.getElementById('submitTakeRequestBtn');

        textarea.value = '';
        alertBox.classList.add('d-none');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Request';

        // Instantiate and open the modal box safely
        const bsFormModal = new bootstrap.Modal(modalEl);
        bsFormModal.show();

        // Remove old click listeners if modal was closed and reopened to avoid event stacking
        const newSubmitBtn = document.getElementById('submitTakeRequestBtn');
        const clonedBtn = newSubmitBtn.cloneNode(true);
        newSubmitBtn.parentNode.replaceChild(clonedBtn, newSubmitBtn);

        // Bind the form execution processing event 
        clonedBtn.addEventListener('click', async () => {
            const messageText = textarea.value.trim();

            // CRITICAL FETCH: Pull the precise ID fresh from the DOM attribute
            const activeCommissionId = parseInt(modalEl.getAttribute('data-active-commission-id'));

            if (!activeCommissionId) {
                alertBox.textContent = 'Error: Commission identifier lost. Please close and re-open the window.';
                alertBox.className = 'alert alert-danger fs-fluid-xs';
                alertBox.classList.remove('d-none');
                return;
            }

            if (!messageText) {
                alertBox.textContent = 'Please write a short message before sending your request.';
                alertBox.className = 'alert alert-danger fs-fluid-xs';
                alertBox.classList.remove('d-none');
                return;
            }

            alertBox.classList.add('d-none');
            clonedBtn.disabled = true;
            clonedBtn.textContent = 'Sending...';

            try {
                const res = await fetch(`${APP_BASE_URL}api/commissions/fetch_request.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        commission_id: activeCommissionId, // Sent reliably every time
                        message: messageText
                    })
                });
                const data = await res.json();

                if (data && data.success) {
                    bsFormModal.hide();
                    showSuccessModal('Request Sent!', data.message || 'Your application has been sent to the client.');
                    loadCommissions();
                } else {
                    alertBox.textContent = data?.message || 'Failed to submit request.';
                    alertBox.className = 'alert alert-danger fs-fluid-xs';
                    alertBox.classList.remove('d-none');
                    clonedBtn.disabled = false;
                    clonedBtn.textContent = 'Send Request';
                }
            } catch (err) {
                console.error('Take commission error:', err);
                alertBox.textContent = 'A network error occurred. Please try again.';
                alertBox.className = 'alert alert-danger fs-fluid-xs';
                alertBox.classList.remove('d-none');
                clonedBtn.disabled = false;
                clonedBtn.textContent = 'Send Request';
            }
        });
    }

    // ── Modal Form Handling ───────────────────────────────────

    if (imageFile) {
        imageFile.addEventListener('change', () => {
            if (imageName) imageName.textContent = imageFile.files[0]?.name ?? '';
        });
    }

    function showModalAlert(message, isSuccess) {
        if (!formAlert) return;
        formAlert.textContent = message;
        formAlert.className = `alert fs-fluid-xs ${isSuccess ? 'alert-success' : 'alert-danger'}`;
        show(formAlert);
    }

    function resetModal() {
        if (titleInput) titleInput.value = '';
        if (descInput) descInput.value = '';
        if (budgetInput) budgetInput.value = '';
        if (categoryInput) categoryInput.value = '';
        if (imageFile) imageFile.value = '';
        if (imageName) imageName.textContent = '';
        hide(formAlert);
    }

    if (submitBtn) {
        submitBtn.addEventListener('click', async () => {
            hide(formAlert);

            const title = titleInput?.value.trim() ?? '';
            const description = descInput?.value.trim() ?? '';
            const budget = parseFloat(budgetInput?.value ?? 0);
            const category_id = parseInt(categoryInput?.value ?? 0);

            if (!title) { showModalAlert('Please provide a commission name.', false); return; }
            if (!description) { showModalAlert('Please provide a project description.', false); return; }
            if (isNaN(budget) || budget <= 0) { showModalAlert('Please enter a valid budget higher than ₱0.', false); return; }
            if (!category_id) { showModalAlert('Please select a category.', false); return; }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting…';

            try {
                const res = await fetch(`${APP_BASE_URL}api/commissions/create.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title, description, budget, category_id })
                });
                const data = await res.json();

                if (data && data.success) {
                    showModalAlert(data.message, true);
                    setTimeout(() => {
                        if (typeof bootstrap !== 'undefined') {
                            bootstrap.Modal.getInstance(document.getElementById('postCommissionModal'))?.hide();
                        }
                        loadCommissions();
                    }, 1500);
                } else {
                    showModalAlert(data?.message || 'Something went wrong.', false);
                }
            } catch {
                showModalAlert('Network error — please try again.', false);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Post Commission';
            }
        });
    }

    document.getElementById('postCommissionModal')?.addEventListener('hidden.bs.modal', resetModal);

    // ── Event Listeners ───────────────────────────────────────

    document.querySelectorAll('input[type="radio"]').forEach(input =>
        input.addEventListener('change', applyFilters)
    );
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);
    if (searchBtn) searchBtn.addEventListener('click', applyFilters);
    if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') applyFilters(); });

    // Dynamic central action routing via delegated listeners
    document.addEventListener('click', e => {
        if (e.target) {
            if (e.target.classList.contains('assign-artist-btn')) {
                handleAssignArtist(e.target);
            }
            if (e.target.classList.contains('decline-artist-btn')) {
                handleDeclineArtist(e.target);
            }
            if (e.target.classList.contains('take-commission-btn')) {
                handleTakeCommission(e.target);
            }
            if (e.target.classList.contains('artist-progress-btn')) {
                handleArtistStatusUpdate(e.target, 'in_progress');
            }
            if (e.target.classList.contains('artist-complete-btn')) {
                handleArtistStatusUpdate(e.target, 'completed');
            }
            if (e.target.classList.contains('artist-cancel-request-btn')) {
                handleCancelRequest(e.target);
            }
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            const budgetAll = document.querySelector('input[name="budget"][value="0-999999"]'); if (budgetAll) budgetAll.checked = true;
            const statusAll = document.querySelector('input[name="status"][value="all"]'); if (statusAll) statusAll.checked = true;
            if (searchInput) searchInput.value = '';
            if (sortSelect) sortSelect.value = 'newest';
            applyFilters();
        });
    }

    loadCommissions();
});