<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-9">

                <h2 class="mt-3 mb-1">Transaction Records</h2>
                <p class="mb-3">Payments received for your completed commissions.</p>

                <!-- ── Earnings summary pill ── -->
                <div class="d-inline-flex flex-wrap align-items-center gap-2 px-3 px-sm-4 py-2 mb-4 theme-border"
                    style="border-radius: var(--radius-lg); border-width: 2px !important; background: var(--clr-bg-card); max-width: 100%;">
                    <i class="bi bi-wallet2" style="color: var(--clr-gold);"></i>
                    <span class="text-muted" style="font-size: 0.875rem;">Total Earned:</span>
                    <span id="totalEarnings" class="fw-bold" style="color: var(--clr-gold); font-size: 1rem;">—</span>
                </div>

                <!-- ── Filters ── -->
                <div class="row align-items-center mb-4 g-3">
                    <div class="col-12 col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text theme-border border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchInput"
                                class="form-control theme-border border-start-0"
                                placeholder="Search transaction ID..."
                                style="border-width: 2px !important;">
                        </div>
                    </div>

                    <div class="col-12 col-md-4 col-lg-3 ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-outline d-flex align-items-center justify-content-between w-100 theme-border"
                                type="button" id="dateRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                style="border-width: 2px !important; padding: 0.4rem 1rem;">
                                <span><i class="bi bi-calendar"></i> Date Range</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu p-3 shadow-sm theme-border dropdown-menu-end" style="width: min(300px, 90vw);">
                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date" id="dateStart"
                                        class="form-control theme-border"
                                        style="border-width: 1px !important;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date" id="dateEnd"
                                        class="form-control theme-border"
                                        style="border-width: 1px !important;">
                                </div>
                                <div class="d-flex gap-2">
                                    <button id="applyDateRange" class="btn btn-fill w-100 btn-sm">Apply</button>
                                    <button id="resetDateRange" class="btn btn-outline w-100 btn-sm">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Status Tabs ── -->
                <div class="text-center">
                    <div class="d-inline-flex flex-wrap justify-content-center p-1 mb-4 theme-border"
                        style="border-radius: var(--radius-lg) !important; max-width: 100%;">
                        <?php
                        $tabs = [
                            ''          => 'All',
                            'Paid'      => 'Paid',
                            'Pending'   => 'Pending',
                            'Cancelled' => 'Cancelled',
                        ];
                        foreach ($tabs as $val => $label): ?>
                            <button class="btn btn-outline btn-sm status-tab my-1"
                                data-status="<?= $val ?>"
                                style="border-radius: var(--radius-lg); border: none;">
                                <?= $label ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- ── Table ── -->
                <div class="theme-border p-0 hide-scrollbar"
                    style="border-width: 2px !important; border-radius: var(--radius-lg);
                           height: 400px; overflow-y: auto; overflow-x: hidden; position: relative;">

                    <table class="table mb-0 align-middle" style="width: 100%;">
                        <thead class="sticky-top"
                            style="background-color: var(--clr-gold-light); z-index: 10; top: 0;">
                            <tr>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Transaction ID</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Client</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Commission</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Amount</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Method</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Status</th>
                                <th class="p-2 p-sm-3 fs-fluid-3xs" style="color: var(--clr-text-primary);">Date</th>
                                <th class="p-2 p-sm-3 text-center fs-fluid-3xs" style="color: var(--clr-text-primary);">Details</th>
                            </tr>
                        </thead>
                        <tbody id="txnTableBody">
                            <tr>
                                <td colspan="8" class="text-center p-4">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading transactions...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- ── Pagination ── -->
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 px-1 gap-2">
                    <small id="paginationInfo" class="text-muted"></small>
                    <div id="paginationControls" class="d-flex gap-2 flex-wrap"></div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- ── Transaction Detail Modal ── -->
<div class="modal fade" id="txnDetailModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content theme-border"
            style="background-color: var(--clr-bg-alt); border-radius: var(--radius-md);">

            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <i class="bi bi-receipt fs-4" style="color: var(--clr-text-primary);"></i>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center pt-0">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: var(--clr-open);"></i>
                </div>
                <h2 class="mb-2">TRANSACTION RECEIPT</h2>
                <p class="mb-4" style="color: var(--clr-text-secondary);">
                    Payment received for your commission.
                </p>

                <div class="text-start px-2">
                    <h5 class="mb-3">Transaction Details</h5>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Transaction ID :</span>
                        <span id="modalTxnId" class="text-end"></span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Client :</span>
                        <span id="modalClient" class="text-end"></span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Commission :</span>
                        <span id="modalCategory" class="text-end"></span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Payment Method :</span>
                        <span id="modalPaymentMethod" class="text-end"></span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Amount Received :</span>
                        <span id="modalAmount" class="fw-bold text-end" style="color: var(--clr-open);"></span>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between gap-1 mb-2">
                        <span class="fw-bold">Date :</span>
                        <span id="modalDate" class="text-end"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-fill-static w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const state = {
        page:      1,
        perPage:   10,
        search:    '',
        status:    '',
        dateStart: '',
        dateEnd:   '',
    };

    const STATUS_COLORS = {
        'Paid':      'var(--clr-open)',
        'Pending':   'var(--clr-star)',
        'Cancelled': 'var(--clr-closed)',
    };

    const tbody    = document.getElementById('txnTableBody');
    const pagInfo  = document.getElementById('paginationInfo');
    const pagCtrls = document.getElementById('paginationControls');
    const searchInput = document.getElementById('searchInput');

    // ── Fetch ─────────────────────────────────────────────────────────────────
    async function loadTransactions() {
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center p-4">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading transactions...
            </td></tr>`;

        const params = new URLSearchParams({
            page:       state.page,
            per_page:   state.perPage,
            search:     state.search,
            status:     state.status,
            date_start: state.dateStart,
            date_end:   state.dateEnd,
        });

        try {
            const res  = await fetch(`<?= BASE_URL ?>api/payments/fetch_artist_transactions.php?${params}`);
            const json = await res.json();

            if (!json.success) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-danger">${json.message}</td></tr>`;
                return;
            }

            // Update earnings pill (only on first unfiltered load, or always)
            document.getElementById('totalEarnings').textContent = '₱' + json.total_earnings;

            renderRows(json.data);
            renderPagination(json.pagination);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-danger">Failed to load transactions. Please try again.</td></tr>`;
            console.error(err);
        }
    }

    // ── Render rows ───────────────────────────────────────────────────────────
    function renderRows(transactions) {
        if (!transactions.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-muted">No transaction records found.</td></tr>`;
            return;
        }

        tbody.innerHTML = transactions.map(t => {
            const color    = STATUS_COLORS[t.payment_status] ?? 'var(--clr-text-muted)';
            const isPaid   = t.payment_status === 'Paid';
            const detailBtn = isPaid
                ? `<button class="btn btn-sm btn-outline js-view-txn fs-fluid-3xs"
                        style="padding: 2px 10px;"
                        data-txn-id="${t.transaction_id}"
                        data-client="${escHtml(t.client_name)}"
                        data-category="${escHtml(t.category)}"
                        data-method="${escHtml(t.payment_method)}"
                        data-amount="₱${t.amount}"
                        data-date="${escHtml(t.payment_date_full)}">
                        <i class="bi bi-file-earmark-text"></i> View
                   </button>`
                : `<span class="text-muted fs-fluid-3xs">—</span>`;

            return `
                <tr style="border-bottom: 1px solid var(--clr-border);">
                    <td class="p-2 p-sm-3 fs-fluid-3xs">#TXN-${t.transaction_id}</td>
                    <td class="p-2 p-sm-3 fs-fluid-3xs">${escHtml(t.client_name)}</td>
                    <td class="p-2 p-sm-3 fs-fluid-3xs">${escHtml(t.category)}</td>
                    <td class="p-2 p-sm-3 fw-bold fs-fluid-3xs">₱${t.amount}</td>
                    <td class="p-2 p-sm-3">
                        <span class="badge bg-light text-dark fs-fluid-3xs">
                            ${escHtml(t.payment_method)}
                        </span>
                    </td>
                    <td class="p-2 p-sm-3">
                        <span class="badge fs-fluid-3xs" style="background-color: ${color}; color: white; padding: 4px 8px;">
                            ${escHtml(t.payment_status)}
                        </span>
                    </td>
                    <td class="p-2 p-sm-3 fs-fluid-3xs">${escHtml(t.payment_date)}</td>
                    <td class="p-2 p-sm-3 text-center">${detailBtn}</td>
                </tr>`;
        }).join('');

        document.querySelectorAll('.js-view-txn').forEach(btn => {
            btn.addEventListener('click', () => openModal(btn.dataset));
        });
    }

    // ── Pagination ────────────────────────────────────────────────────────────
    function renderPagination({ total_rows, total_pages, current_page, per_page }) {
        const start = total_rows === 0 ? 0 : (current_page - 1) * per_page + 1;
        const end   = Math.min(current_page * per_page, total_rows);
        pagInfo.textContent = total_rows
            ? `Showing ${start}–${end} of ${total_rows} record${total_rows !== 1 ? 's' : ''}`
            : '';

        if (total_pages <= 1) { pagCtrls.innerHTML = ''; return; }

        let html = `<button class="btn btn-sm btn-outline theme-border"
            ${current_page === 1 ? 'disabled' : ''}
            onclick="window.__artistTxn.goPage(${current_page - 1})">
            <i class="bi bi-chevron-left"></i></button>`;

        for (let i = 1; i <= total_pages; i++) {
            if (i === 1 || i === total_pages || (i >= current_page - 1 && i <= current_page + 1)) {
                html += `<button class="btn btn-sm ${i === current_page ? 'btn-fill' : 'btn-outline theme-border'}"
                             onclick="window.__artistTxn.goPage(${i})">${i}</button>`;
            } else if (i === current_page - 2 || i === current_page + 2) {
                html += `<span class="btn btn-sm disabled">…</span>`;
            }
        }

        html += `<button class="btn btn-sm btn-outline theme-border"
            ${current_page === total_pages ? 'disabled' : ''}
            onclick="window.__artistTxn.goPage(${current_page + 1})">
            <i class="bi bi-chevron-right"></i></button>`;

        pagCtrls.innerHTML = html;
    }

    // ── Modal ─────────────────────────────────────────────────────────────────
    function openModal(d) {
        document.getElementById('modalTxnId').textContent        = '#TXN-' + d.txnId;
        document.getElementById('modalClient').textContent        = d.client;
        document.getElementById('modalCategory').textContent      = d.category;
        document.getElementById('modalPaymentMethod').textContent  = d.method;
        document.getElementById('modalAmount').textContent         = d.amount;
        document.getElementById('modalDate').textContent           = d.date;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('txnDetailModal')).show();
    }

    // ── Helper ────────────────────────────────────────────────────────────────
    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    }

    // ── Status tabs ───────────────────────────────────────────────────────────
    document.querySelectorAll('.status-tab').forEach(btn => {
        if (btn.dataset.status === '') btn.classList.add('active-tab');
        btn.addEventListener('click', () => {
            document.querySelectorAll('.status-tab').forEach(b => b.classList.remove('active-tab'));
            btn.classList.add('active-tab');
            state.status = btn.dataset.status;
            state.page   = 1;
            loadTransactions();
        });
    });

    // ── Search ────────────────────────────────────────────────────────────────
    let searchTimer;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            state.search = searchInput.value.trim();
            state.page   = 1;
            loadTransactions();
        }, 400);
    });

    // ── Date range ────────────────────────────────────────────────────────────
    document.getElementById('applyDateRange').addEventListener('click', () => {
        state.dateStart = document.getElementById('dateStart').value;
        state.dateEnd   = document.getElementById('dateEnd').value;
        state.page      = 1;
        bootstrap.Dropdown.getOrCreateInstance(
            document.getElementById('dateRangeDropdown')
        ).hide();
        loadTransactions();
    });

    document.getElementById('resetDateRange').addEventListener('click', () => {
        document.getElementById('dateStart').value = '';
        document.getElementById('dateEnd').value   = '';
        state.dateStart = '';
        state.dateEnd   = '';
        state.page      = 1;
        loadTransactions();
    });

    // ── Page nav ──────────────────────────────────────────────────────────────
    window.__artistTxn = {
        goPage(p) { state.page = p; loadTransactions(); }
    };

    // ── Active tab style ──────────────────────────────────────────────────────
    const style = document.createElement('style');
    style.textContent = `.status-tab.active-tab { background-color: var(--clr-gold-light) !important; font-weight: 600; }`;
    document.head.appendChild(style);

    // ── Init ──────────────────────────────────────────────────────────────────
    loadTransactions();
})();
</script>