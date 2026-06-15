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

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Admin Dashboard</h2>
                <h3 class="joan mb-0">Commissions</h3>
                <p class="text-muted mb-0">Manage every commission listing on Artovia</p>
            </div>
            <a href="<?= BASE_URL ?>commissions/create-commission" class="btn btn-artovia-primary">
                <i class="bi bi-plus-lg"></i> Create New Listing
            </a>
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
                <table class="table align-middle mb-0">
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

<script>
document.querySelectorAll('.js-remove-listing').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Remove this commission listing and all related records?')) return;

        const commissionId = btn.dataset.commissionId;
        try {
            const res = await fetch('<?= BASE_URL ?>api/admin/remove_listing.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ commission_id: commissionId })
            });
            const data = await res.json();
            if (data.success) {
                btn.closest('tr').remove();
            } else {
                alert(data.message || 'Failed to remove listing.');
            }
        } catch (err) {
            alert('Network error while removing listing.');
        }
    });
});
</script>