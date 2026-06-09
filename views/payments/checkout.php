<?php require_once __DIR__ . '/../../src/middleware/user_middleware.php'; ?>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-9">
                <h2 class="mb-4">CHECKOUT</h2>
                <p class="mb-4">Review your order and proceed to payment.</p>

                <div class="row">
                    <!-- Left Column: Commission Details -->
                    <div class="col-lg-7">
                        <div class="card theme-border p-4 mb-4">
                            <h3 class="mb-4">Commission Details</h3>
                            <div class="d-flex align-items-center mb-4">
                                <div class="rounded-circle theme-border me-3" style="width: 50px; height: 50px;"></div>
                                <div class="flex-grow-1">
                                    <label class="form-label">Artist Name</label>
                                    <input type="text" class="form-control theme-border" style="border-width: 1px !important;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commission Type</label>
                                <input type="text" class="form-control theme-border" style="border-width: 1px !important;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Budget</label>
                                <input type="text" class="form-control theme-border" style="border-width: 1px !important;">
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Description</label>
                                <textarea class="form-control theme-border" rows="4" style="resize: none; border-width: 1px !important;"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Payment Method -->
                    <div class="col-lg-5">
                        <div class="card theme-border p-4">
                            <h3 class="mb-4">Payment Method</h3>
                            <div class="mb-4">
                                <select class="form-select theme-border" style="border-width: 1px !important;" aria-label="Select payment method">
                                    <option value="gcash">GCASH</option>
                                    <option value="maya">Maya</option>
                                    <option value="bank">Bank Transfer</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>

                            <!-- Order Summary Box -->
                            <div class="card p-3 mb-4" style="background-color: var(--clr-bg-alt); border: 1px solid var(--clr-border);">
                                <h5 class="text-center mb-3">Order Summary</h5>
                                <div class="d-flex justify-content-between mb-2"><span>Item</span><span>₱0.00</span></div>
                                <div class="d-flex justify-content-between mb-2"><span>Fee</span><span>₱0.00</span></div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>₱0.00</span></div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terms">
                                <label class="form-check-label" for="terms">I agree to the <a href="#" class="theme-font-color">Terms and Conditions</a>.</label>
                            </div>

                            <button class="btn-artovia-primary w-100">Pay now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Payment Success Modal -->
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