<?php
require_once __DIR__ . '/../../src/middleware/admin_middleware.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// Reuse the same stat cards as the dashboard for consistency
$stmtPending = $db->query('SELECT COUNT(*) FROM commission_tbl WHERE status_id = 2');
$totalPending = (int) $stmtPending->fetchColumn();

$stmtActive = $db->query('SELECT COUNT(*) FROM commission_tbl WHERE status_id = 5');
$activeCommissions = (int) $stmtActive->fetchColumn();

$stmtCompleted = $db->query('SELECT COUNT(*) FROM commission_tbl WHERE status_id = 6');
$completedCommissions = (int) $stmtCompleted->fetchColumn();

$stmtRevenue = $db->query('SELECT COALESCE(SUM(amount), 0) FROM payment_tbl WHERE status_id = 10');
$totalRevenue = (float) $stmtRevenue->fetchColumn();

// All commissions, newest first, with client + status + category info
$stmtAll = $db->query('
    SELECT
        c.commission_id,
        c.description,
        c.price,
        c.commission_date,
        cat.category_name,
        st.status_name AS commission_status,
        oa.first_name AS client_first_name,
        oa.last_name  AS client_last_name,
        oa.username   AS client_username,
        aa.first_name AS artist_first_name,
        aa.last_name  AS artist_last_name
    FROM commission_tbl c
    JOIN user_tbl u           ON c.user_id = u.user_id
    JOIN account_tbl oa       ON u.account_id = oa.account_id
    LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
    JOIN status_tbl st        ON c.status_id = st.status_id
    LEFT JOIN artist_tbl art   ON c.artist_id = art.artist_id
    LEFT JOIN account_tbl aa   ON art.account_id = aa.account_id
    ORDER BY c.commission_date DESC
    LIMIT 100
');
$commissions = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

// Map status_name -> badge color variable for consistent UI
$statusColors = [
    'Pending'     => 'var(--clr-star)',
    'Accepted'    => 'var(--clr-open)',
    'In Progress' => 'var(--clr-gold)',
    'Completed'   => 'var(--clr-open)',
    'Rejected'    => 'var(--clr-closed)',
    'Cancelled'   => 'var(--clr-closed)',
];
?>

<style>
    /* Modal Overlay - Slightly tinted dark overlay to match the warm palette */
    .admin-modal {
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(25, 23, 20, 0.4);
        /* Warm dark tint */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal Content Box - Styled like your dashboard cards */
    .modal-content {
        background-color: #ffffff;
        padding: 24px;
        border: 2px solid #c8b189;
        border-radius: 12px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 10px 25px rgba(200, 177, 137, 0.15);
        animation: fadeIn 0.2s ease-out;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    /* Heading matching your serif/dark dashboard typography */
    .modal-header h3 {
        margin: 0;
        font-family: inherit;
        color: #1a1a1a;
        font-size: 1.25rem;
        font-weight: 700;
    }

    .close-modal-btn {
        font-size: 24px;
        cursor: pointer;
        color: #c8b189;
        /* Uses the accent tan color */
        transition: color 0.2s ease;
    }

    .close-modal-btn:hover {
        color: #1a1a1a;
    }

    .modal-body {
        color: #555555;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Button UI Basics */
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    /* Cancel Button - Subtle and clean */
    .btn-secondary {
        background-color: #f4f0e6;
        /* Cream shade to match the dashboard vibe */
        color: #555555;
        border: 1px solid #e2dacb;
    }

    .btn-secondary:hover {
        background-color: #eae3d2;
        color: #1a1a1a;
    }

    /* Danger Button - The exact red from your dashboard screenshot */
    .btn-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-danger:hover {
        background-color: #bd2130;
        /* Darker red on hover */
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Admin Dashboard</h2>
                <h3 class="joan mb-0">Commissions</h3>
                <p class="text-muted mb-0">Manage every commission listing on Artovia</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h5 class="mb-3">Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= BASE_URL ?>admin" class="btn btn-outline-secondary">Dashboard</a>
                        <a href="<?= BASE_URL ?>admin/users" class="btn btn-outline-secondary">Manage Users</a>
                        <a href="<?= BASE_URL ?>admin/commissions" class="btn btn-outline-secondary">Review Commissions</a>
                        <a href="<?= BASE_URL ?>admin/payments" class="btn btn-outline-secondary">Payment Records</a>
                        <a href="<?= BASE_URL ?>admin/reports" class="btn btn-outline-secondary">Reports</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4 h-100 d-flex align-items-center justify-content-center"
                    style="background: var(--clr-bg-card);">
                    <h6 class="text-muted mb-0">System Status: <span class="text-success fw-bold">Operational</span>
                    </h6>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Pending</h6>
                    <h3 class="display-6 fw-bold"><?= number_format($totalPending) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Active Commissions</h6>
                    <h3 class="display-6 fw-bold"><?= number_format($activeCommissions) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Completed</h6>
                    <h3 class="display-6 fw-bold"><?= number_format($completedCommissions) ?></h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card theme-border p-4 h-100" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3 class="display-6 fw-bold">₱<?= number_format($totalRevenue, 2) ?></h3>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden mb-4"
            style="background: var(--clr-bg-card);">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Commission Listings</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 fs-fluid-3xs">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Client</th>
                            <th class="p-3">Artist</th>
                            <th class="p-3">Commission Type</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Budget</th>
                            <th class="p-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($commissions)): ?>
                            <tr>
                                <td class="p-3 text-muted text-center" colspan="6">No commissions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($commissions as $c): ?>
                                <tr style="border-bottom: 1px solid var(--clr-border);" data-commission-id="<?= (int) $c['commission_id'] ?>">
                                    <td class="p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle me-3"
                                                style="width: 40px; height: 40px; background: var(--clr-gold-light);"></div>
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($c['client_first_name'] . ' ' . $c['client_last_name']) ?></div>
                                                <small class="text-muted">ID: #C-<?= (int) $c['commission_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <?php if ($c['artist_first_name']): ?>
                                            <?= htmlspecialchars($c['artist_first_name'] . ' ' . $c['artist_last_name']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3"><?= htmlspecialchars($c['category_name'] ?? 'Uncategorized') ?></td>
                                    <td class="p-3">
                                        <?php $color = $statusColors[$c['commission_status']] ?? 'var(--clr-text-muted)'; ?>
                                        <span class="badge"
                                            style="background-color: <?= $color ?>; color: white;"><?= htmlspecialchars($c['commission_status']) ?></span>
                                    </td>
                                    <td class="p-3">₱<?= number_format((float) $c['price'], 2) ?></td>
                                    <td class="p-3 text-end">
                                        <button class="btn btn-sm btn-danger js-remove-listing" data-commission-id="<?= (int) $c['commission_id'] ?>">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<div id="deleteModal" class="admin-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Deletion</h3>
            <span class="close-modal-btn">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove this commission listing and all related records? This action cannot be undone.</p>
            <div id="modalErrorMessage" style="display: none; color: #dc3545; font-size: 0.85rem; margin-top: 12px; font-weight: 600;"></div>
        </div>
        <div class="modal-footer">
            <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
            <button id="confirmDeleteBtn" class="btn btn-danger">
                <span class="btn-text">Remove Commission</span>
                <span class="btn-spinner" style="display: none;">Removing...</span>
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const cancelBtn = document.getElementById('cancelDeleteBtn');
        const closeX = document.querySelector('.close-modal-btn');
        const errorContainer = document.getElementById('modalErrorMessage');

        let activeRowToRemove = null;
        let activeCommissionId = null;

        // Open modal on click
        document.querySelectorAll('.js-remove-listing').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                activeRowToRemove = btn.closest('tr');
                activeCommissionId = btn.dataset.commissionId;

                // Clear previous error states if any
                if (errorContainer) {
                    errorContainer.style.display = 'none';
                    errorContainer.textContent = '';
                }

                modal.style.display = 'flex';
            });
        });

        // Cleanly close modal and reset actions
        const closeModal = () => {
            modal.style.display = 'none';
            activeRowToRemove = null;
            activeCommissionId = null;

            confirmBtn.disabled = false;
            confirmBtn.querySelector('.btn-text').style.display = 'inline';
            confirmBtn.querySelector('.btn-spinner').style.display = 'none';
        };

        cancelBtn.addEventListener('click', closeModal);
        closeX.addEventListener('click', closeModal);

        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Handle AJAX Deletion inside the custom modal workflow
        confirmBtn.addEventListener('click', async () => {
            if (!activeCommissionId || !activeRowToRemove) return;

            // Visual loading state
            confirmBtn.disabled = true;
            confirmBtn.querySelector('.btn-text').style.display = 'none';
            confirmBtn.querySelector('.btn-spinner').style.display = 'inline';
            if (errorContainer) errorContainer.style.display = 'none';

            try {
                const res = await fetch('<?= BASE_URL ?>api/admin/remove_listing.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        commission_id: activeCommissionId
                    })
                });

                const data = await res.json();

                if (data.success) {
                    activeRowToRemove.remove();
                    closeModal();
                } else {
                    throw new Error(data.message || 'Failed to remove listing.');
                }
            } catch (err) {
                // Display errors inside the modal UI itself instead of browser alerts
                if (errorContainer) {
                    errorContainer.textContent = err.message || 'Network error while removing listing.';
                    errorContainer.style.display = 'block';
                }

                // Re-enable button so they can try again
                confirmBtn.disabled = false;
                confirmBtn.querySelector('.btn-text').style.display = 'inline';
                confirmBtn.querySelector('.btn-spinner').style.display = 'none';
            }
        });
    });
</script>