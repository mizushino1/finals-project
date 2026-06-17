<?php
require_once __DIR__ . '/../../src/middleware/admin_middleware.php';
require_once __DIR__ . '/../../config/database.php';

$db = getDB();

// Total processed = sum of all paid payments (status_id 10 = Paid)
$stmtTotal = $db->query('SELECT COALESCE(SUM(amount), 0) FROM payment_tbl WHERE status_id = 10');
$totalProcessed = (float) $stmtTotal->fetchColumn();

// Pending payouts = transactions not yet matched by a "Paid" payment
// (transaction total minus what's been paid for that transaction)
$stmtPending = $db->query('
    SELECT COALESCE(SUM(t.total_amount), 0)
    FROM transaction_tbl t
    LEFT JOIN payment_tbl p ON p.transaction_id = t.transaction_id AND p.status_id = 10
    WHERE p.payment_id IS NULL
');
$pendingPayouts = (float) $stmtPending->fetchColumn();

// Success rate = paid payments / total payments
$stmtCounts = $db->query('
    SELECT
        SUM(CASE WHEN status_id = 10 THEN 1 ELSE 0 END) AS paid_count,
        COUNT(*) AS total_count
    FROM payment_tbl
');
$counts = $stmtCounts->fetch(PDO::FETCH_ASSOC);
$successRate = ($counts && $counts['total_count'] > 0)
    ? ($counts['paid_count'] / $counts['total_count']) * 100
    : 0;

// Recent payment records joined with commission + client + payment method
$stmtPayments = $db->query('
    SELECT
        p.payment_id,
        p.amount,
        p.payment_date,
        pm.payment_method_name,
        st.status_name AS payment_status,
        t.transaction_id,
        c.commission_id,
        oa.first_name AS client_first_name,
        oa.last_name  AS client_last_name
    FROM payment_tbl p
    JOIN transaction_tbl t   ON p.transaction_id = t.transaction_id
    JOIN commission_tbl c    ON t.commission_id = c.commission_id
    JOIN user_tbl u          ON c.user_id = u.user_id
    JOIN account_tbl oa      ON u.account_id = oa.account_id
    JOIN payment_method_tbl pm ON p.payment_method_id = pm.payment_method_id
    JOIN status_tbl st       ON p.status_id = st.status_id
    ORDER BY p.payment_date DESC
    LIMIT 100
');
$payments = $stmtPayments->fetchAll(PDO::FETCH_ASSOC);

$statusColors = [
    'Paid'      => 'var(--clr-open)',
    'Completed' => 'var(--clr-open)',
    'Pending'   => 'var(--clr-star)',
    'Cancelled' => 'var(--clr-closed)',
];
?>

<main class="py-5">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="joan mb-0">Payments Overview</h2>
                <p class="text-muted mb-0">Track all financial transactions and payment gateway activities</p>
            </div>
            <button class="btn btn-artovia-primary">
                <i class="bi bi-download"></i> Export Reports
            </button>
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

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Total Processed</h6>
                    <h3 class="fw-bold">₱<?= number_format($totalProcessed, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Pending Payouts</h6>
                    <h3 class="fw-bold">₱<?= number_format($pendingPayouts, 2) ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card theme-border p-4" style="background: var(--clr-bg-card);">
                    <h6 class="text-muted">Success Rate</h6>
                    <h3 class="fw-bold"><?= number_format($successRate, 1) ?>%</h3>
                </div>
            </div>
        </div>

        <div class="card theme-border border-0 shadow-sm p-0 overflow-hidden" style="background: var(--clr-bg-card);">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead style="background-color: var(--clr-bg-alt);">
                        <tr>
                            <th class="p-3">Transaction ID</th>
                            <th class="p-3">Client</th>
                            <th class="p-3">Payment Method</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td class="p-3 text-muted text-center" colspan="6">No payment records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $p): ?>
                                <?php $isPaid = strcasecmp($p['payment_status'], 'Paid') === 0; ?>
                                <tr style="border-bottom: 1px solid var(--clr-border);">
                                    <td class="p-3">#TXN-<?= (int) $p['transaction_id'] ?></td>
                                    <td class="p-3"><?= htmlspecialchars($p['client_first_name'] . ' ' . $p['client_last_name']) ?></td>
                                    <td class="p-3">
                                        <span class="badge bg-light text-dark"><?= htmlspecialchars($p['payment_method_name']) ?></span>
                                    </td>
                                    <td class="p-3 fw-bold">₱<?= number_format((float) $p['amount'], 2) ?></td>
                                    <td class="p-3">
                                        <?php $color = $statusColors[$p['payment_status']] ?? 'var(--clr-text-muted)'; ?>
                                        <span class="badge" style="background-color: <?= $color ?>; color: white;"><?= htmlspecialchars($p['payment_status']) ?></span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <?php if ($isPaid): ?>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary js-view-receipt"
                                                data-transaction-id="<?= (int) $p['transaction_id'] ?>"
                                                data-payment-method="<?= htmlspecialchars($p['payment_method_name']) ?>"
                                                data-amount="<?= number_format((float) $p['amount'], 2) ?>"
                                                data-date="<?= date('M d, Y h:i A', strtotime($p['payment_date'])) ?>">View</button>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
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

<!-- ── Shared Payment Receipt Modal (styled after payments/success.php) ── -->
<div class="modal fade" id="paymentReceiptModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content theme-border"
            style="background-color: var(--clr-bg-alt); border-radius: var(--radius-md);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <i class="bi bi-receipt fs-4" style="color: var(--clr-text-primary);"></i>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center pt-0">
                <div class="mb-3">
                    <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: var(--clr-open);"></i>
                </div>
                <h2 class="mb-2">PAYMENT RECEIPT</h2>
                <p class="mb-4" style="color: var(--clr-text-secondary);">
                    This payment has been confirmed and processed.
                </p>

                <!-- Payment Details -->
                <div class="text-start px-2">
                    <h5 class="mb-3">Payment Details</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Transaction ID :</span>
                        <span id="receiptTransactionId"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Payment Method :</span>
                        <span id="receiptPaymentMethod"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Amount Paid :</span>
                        <span id="receiptAmount"></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Date :</span>
                        <span id="receiptDate"></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-artovia-primary w-100" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.js-view-receipt').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('receiptTransactionId').textContent = '#' + btn.dataset.transactionId;
            document.getElementById('receiptPaymentMethod').textContent = btn.dataset.paymentMethod;
            document.getElementById('receiptAmount').textContent = '₱' + btn.dataset.amount;
            document.getElementById('receiptDate').textContent = btn.dataset.date;

            const modalEl = document.getElementById('paymentReceiptModal');
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });
    });
</script>