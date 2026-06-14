<?php
require_once __DIR__ . '/../../src/middleware/user_middleware.php';
require_once __DIR__ . '/../../config/database.php';

$txnId = isset($_GET['txn']) ? intval($_GET['txn']) : 0;
$txn   = null;

if ($txnId > 0) {
    $db   = getDB();
    $stmt = $db->prepare('
        SELECT t.transaction_id, t.total_amount, t.transaction_date,
               pm.payment_method_name
        FROM   transaction_tbl t
        JOIN   payment_tbl p         ON p.transaction_id      = t.transaction_id
        JOIN   payment_method_tbl pm ON pm.payment_method_id  = p.payment_method_id
        WHERE  t.transaction_id = ?
        LIMIT 1
    ');
    $stmt->execute([$txnId]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<main class="min-vh-100 d-flex flex-column justify-content-center align-items-center position-relative">

    <?php if (!$txn): ?>
        <!-- Invalid / missing transaction — show error card instead of modal -->
        <div class="card text-center p-5 shadow-sm" style="max-width:420px; background-color:var(--clr-bg-alt); border-radius:var(--radius-md);">
            <i class="bi bi-x-circle-fill mb-3" style="font-size:4rem; color:var(--clr-danger, #dc3545);"></i>
            <h4 class="mb-2">Transaction Not Found</h4>
            <p class="text-muted mb-4">We couldn't find a payment record for this link.</p>
            <a href="<?= BASE_URL ?>commissions" class="btn btn-artovia-primary w-100">Back to Commissions</a>
        </div>

    <?php else: ?>
        <!-- Payment Success Modal — auto-shown via JS below -->
        <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content theme-border"
                    style="background-color:var(--clr-bg-alt); border-radius:var(--radius-md);">

                    <!-- Header -->
                    <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <i class="bi bi-receipt fs-4" style="color:var(--clr-text-primary);"></i>
                    </div>

                    <!-- Body -->
                    <div class="modal-body text-center pt-0">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill" style="font-size:4rem; color:var(--clr-open);"></i>
                        </div>
                        <h2 class="mb-2">PAYMENT SUCCESSFUL!</h2>
                        <p class="mb-4" style="color:var(--clr-text-secondary);">
                            Thank you! Your payment has been confirmed.
                        </p>

                        <!-- Payment Details -->
                        <div class="text-start px-2">
                            <h5 class="mb-3">Payment Details</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Transaction ID :</span>
                                <span>#<?= htmlspecialchars($txn['transaction_id']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Payment Method :</span>
                                <span><?= htmlspecialchars($txn['payment_method_name']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Amount Paid :</span>
                                <span>₱<?= number_format($txn['total_amount'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Date :</span>
                                <span><?= date('M d, Y h:i A', strtotime($txn['transaction_date'])) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 p-4 d-flex gap-2">
                        <a href="<?= BASE_URL ?>payments/history"
                            class="btn btn-outline-secondary flex-grow-1">
                            View History
                        </a>
                        <a href="<?= BASE_URL ?>commissions"
                            class="btn btn-artovia-primary flex-grow-1">
                            Back to Commissions
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const el = document.getElementById('paymentSuccessModal');
                if (el) bootstrap.Modal.getOrCreateInstance(el).show();
            });
        </script>
    <?php endif; ?>

</main>