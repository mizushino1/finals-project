<?php require_once __DIR__ . '/../../src/middleware/user_middleware.php'; ?>

<!-- Payment Success Modal -->
<main class="min-vh-100 d-flex flex-column justify-content-center align-items-center position-relative">
    <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content theme-border" style="background-color: var(--clr-bg-alt); border-radius: var(--radius-md);">

                <!-- Modal Header -->
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <i class="bi bi-download fs-4" style="color: var(--clr-text-primary);"></i>
                </div>

                <!-- Modal Body -->
                <div class="modal-body text-center pt-0">
                    <!-- Success Icon -->
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: var(--clr-open);"></i>
                    </div>

                    <h2 class="mb-2">PAYMENT SUCCESSFUL!!</h2>
                    <p class="mb-4" style="color: var(--clr-text-secondary);">Thank you! Your payment has been confirmed.</p>

                    <!-- Payment Details -->
                    <div class="text-start px-4">
                        <h5 class="mb-3">Payment Details</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Transaction ID :</span>
                            <span>#01</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Payment Method :</span>
                            <span>GCash</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Amount Paid :</span>
                            <span>₱150,000</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-artovia-primary w-100" data-bs-dismiss="modal">BACK TO HOME</button>
                </div>
            </div>
        </div>
    </div>
</main>