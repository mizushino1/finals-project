<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- ===================== STEP 1: REVIEW ===================== -->
            <div id="reviewStep">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Commission Completed!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="payment-section mb-4 text-center">
                        <button type="button" id="proceedToPaymentBtn" class="btn btn-success btn-lg w-100 fw-bold">
                            <i class="bi bi-credit-card-fill me-2"></i>Proceed to Payment
                        </button>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-lock-fill"></i> Secure checkout &mdash; choose your payment method.
                        </small>
                    </div>

                    <hr class="my-4 text-muted">

                    <form id="reviewForm">
                        <input type="hidden" id="modalCommissionId" value="" data-amount="0">

                        <div class="mb-3">
                            <label class="form-label d-block fw-bold">Rate the Artist:</label>
                            <div class="star-rating d-flex gap-2 fs-2 text-secondary" style="cursor: pointer;">
                                <span class="star-node" data-value="1">&#9733;</span>
                                <span class="star-node" data-value="2">&#9733;</span>
                                <span class="star-node" data-value="3">&#9733;</span>
                                <span class="star-node" data-value="4">&#9733;</span>
                                <span class="star-node" data-value="5">&#9733;</span>
                            </div>
                            <input type="hidden" id="selectedRating" value="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="reviewComment" class="form-label fw-bold">Leave a Comment (Optional):</label>
                            <textarea class="form-control" id="reviewComment" rows="4" placeholder="Tell us about your experience with the artist..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                    </form>

                </div>
            </div>

            <!-- ===================== STEP 2: PAYMENT METHOD ===================== -->
            <div id="paymentMethodStep" class="d-none">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <button type="button" id="backToReviewBtn" class="btn btn-sm btn-link p-0 me-2 text-decoration-none">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        Choose Payment Method
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-4">Select how you'd like to pay for this commission.</p>

                    <div class="d-flex flex-column gap-2" id="paymentMethodOptions">

                        <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="gcash" style="padding: 0.75rem 1rem; border-radius: 0.5rem;">
                            <span><i class="bi bi-phone-fill me-2" style="color: #007dfe;"></i>GCash</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="maya" style="padding: 0.75rem 1rem; border-radius: 0.5rem;">
                            <span><i class="bi bi-phone-fill me-2" style="color: #00d3ab;"></i>Maya</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="paypal" style="padding: 0.75rem 1rem; border-radius: 0.5rem;">
                            <span><i class="bi bi-paypal me-2" style="color: #00457c;"></i>PayPal</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="credit_card" style="padding: 0.75rem 1rem; border-radius: 0.5rem;">
                            <span><i class="bi bi-credit-card-fill me-2"></i>Credit / Debit Card</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="bank" style="padding: 0.75rem 1rem; border-radius: 0.5rem;">
                            <span><i class="bi bi-bank2 me-2"></i>Bank Transfer</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted"><i class="bi bi-lock-fill"></i> All transactions are encrypted and secure.</small>
                    </div>
                </div>
            </div>

            <!-- ===================== STEP 3: PAYMENT DETAILS ===================== -->
            <div id="paymentDetailsStep" class="d-none">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <button type="button" id="backToMethodsBtn" class="btn btn-sm btn-link p-0 me-2 text-decoration-none">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <span id="paymentDetailsTitle">Payment Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">

                    <div id="checkoutAlert" class="alert d-none" role="alert"></div>

                    <!-- Order summary -->
                    <div class="card p-3 mb-4 bg-light">
                        <h6 class="text-center mb-3">Order Summary</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Amount Due</span>
                            <span id="paymentAmountDisplay">₱0.00</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span id="paymentTotalDisplay">₱0.00</span>
                        </div>
                    </div>

                    <!-- Dynamic payment fields -->
                    <div id="paymentFields" class="mb-4"></div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms">
                        <label class="form-check-label" for="terms">I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>.</label>
                    </div>

                    <button class="btn btn-success w-100" id="payNowBtn">
                        <span id="payNowLabel">Pay now</span>
                        <span id="payNowSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // -----------------------------------------
    // Step elements
    // -----------------------------------------
    const reviewStep = document.getElementById('reviewStep');
    const paymentMethodStep = document.getElementById('paymentMethodStep');
    const paymentDetailsStep = document.getElementById('paymentDetailsStep');

    const proceedToPaymentBtn = document.getElementById('proceedToPaymentBtn');
    const backToReviewBtn = document.getElementById('backToReviewBtn');
    const backToMethodsBtn = document.getElementById('backToMethodsBtn');

    const modalCommissionId = document.getElementById('modalCommissionId');
    const paymentMethodOptions = document.querySelectorAll('.payment-method-option');

    const paymentFields = document.getElementById('paymentFields');
    const paymentDetailsTitle = document.getElementById('paymentDetailsTitle');
    const paymentAmountDisplay = document.getElementById('paymentAmountDisplay');
    const paymentTotalDisplay = document.getElementById('paymentTotalDisplay');

    const termsCheckbox = document.getElementById('terms');
    const checkoutAlert = document.getElementById('checkoutAlert');
    const payNowBtn = document.getElementById('payNowBtn');
    const payNowLabel = document.getElementById('payNowLabel');
    const payNowSpinner = document.getElementById('payNowSpinner');

    let selectedMethod = null;

    function showStep(step) {
        [reviewStep, paymentMethodStep, paymentDetailsStep].forEach(s => s.classList.add('d-none'));
        step.classList.remove('d-none');
    }

    // -----------------------------------------
    // Step 1 -> Step 2
    // -----------------------------------------
    if (proceedToPaymentBtn) {
        proceedToPaymentBtn.addEventListener('click', () => {
            const commissionId = modalCommissionId.value;
            if (!commissionId) {
                alert('No commission selected. Please try again.');
                return;
            }
            showStep(paymentMethodStep);
        });
    }

    if (backToReviewBtn) {
        backToReviewBtn.addEventListener('click', () => showStep(reviewStep));
    }

    // -----------------------------------------
    // Step 2 -> Step 3
    // -----------------------------------------
    const methodLabels = {
        gcash: 'GCash',
        maya: 'Maya',
        paypal: 'PayPal',
        credit_card: 'Credit / Debit Card',
        bank: 'Bank Transfer',
    };

    const fieldTemplates = {
        gcash: () => `
            <label class="form-label">GCash Mobile Number</label>
            <input type="tel" class="form-control payment-input" placeholder="09XX XXX XXXX" pattern="09[0-9]{9}" maxlength="11" required>
            <small class="text-muted">You'll receive an OTP prompt to confirm.</small>
        `,
        maya: () => `
            <label class="form-label">Maya Mobile Number</label>
            <input type="tel" class="form-control payment-input" placeholder="09XX XXX XXXX" pattern="09[0-9]{9}" maxlength="11" required>
            <small class="text-muted">You'll receive an OTP prompt to confirm.</small>
        `,
        bank: () => `
            <label class="form-label">Bank</label>
            <select class="form-select payment-input mb-2" required>
                <option value="">Select your bank</option>
                <option value="bdo">BDO</option>
                <option value="bpi">BPI</option>
                <option value="metrobank">Metrobank</option>
                <option value="unionbank">UnionBank</option>
                <option value="landbank">Landbank</option>
            </select>
            <label class="form-label">Account Number</label>
            <input type="text" class="form-control payment-input" placeholder="Enter account number" required>
        `,
        paypal: () => `
            <label class="form-label">PayPal Email</label>
            <input type="email" class="form-control payment-input" placeholder="you@example.com" required>
            <small class="text-muted">You'll be redirected to PayPal to confirm payment.</small>
        `,
        credit_card: () => `
            <label class="form-label">Card Number</label>
            <input type="text" class="form-control payment-input mb-2" placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric" required>
            <div class="row g-2">
                <div class="col-7">
                    <label class="form-label">Expiry</label>
                    <input type="text" class="form-control payment-input" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="col-5">
                    <label class="form-label">CVC</label>
                    <input type="text" class="form-control payment-input" placeholder="123" maxlength="4" inputmode="numeric" required>
                </div>
            </div>
        `,
    };

    function attachCardFormatting(method) {
        if (method !== 'credit_card') return;
        const inputs = paymentFields.querySelectorAll('.payment-input');
        const cardInput = inputs[0];
        const expiryInput = inputs[1];

        cardInput.addEventListener('input', () => {
            let v = cardInput.value.replace(/\D/g, '').slice(0, 16);
            cardInput.value = v.replace(/(\d{4})(?=\d)/g, '$1 ');
        });

        expiryInput.addEventListener('input', () => {
            let v = expiryInput.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
            expiryInput.value = v;
        });
    }

    paymentMethodOptions.forEach((btn) => {
        btn.addEventListener('click', () => {
            selectedMethod = btn.dataset.method;
            paymentDetailsTitle.textContent = methodLabels[selectedMethod] + ' Payment';
            paymentFields.innerHTML = (fieldTemplates[selectedMethod] || (() => ''))();
            attachCardFormatting(selectedMethod);

            // Amount is read from data-amount on the hidden commission id field.
            // Set this dynamically when opening the modal, e.g.:
            // document.getElementById('modalCommissionId').dataset.amount = '1500.00';
            const amount = parseFloat(modalCommissionId.dataset.amount || '0');
            paymentAmountDisplay.textContent = '₱' + amount.toFixed(2);
            paymentTotalDisplay.textContent = '₱' + amount.toFixed(2);

            showStep(paymentDetailsStep);
        });
    });

    if (backToMethodsBtn) {
        backToMethodsBtn.addEventListener('click', () => showStep(paymentMethodStep));
    }

    // -----------------------------------------
    // Step 3: Validation + Submit
    // -----------------------------------------
    function showAlert(message, type = 'danger') {
        checkoutAlert.className = `alert alert-${type}`;
        checkoutAlert.textContent = message;
        checkoutAlert.classList.remove('d-none');
    }

    function hideAlert() {
        checkoutAlert.classList.add('d-none');
    }

    function setLoading(isLoading) {
        payNowBtn.disabled = isLoading;
        payNowSpinner.classList.toggle('d-none', !isLoading);
        payNowLabel.textContent = isLoading ? 'Processing...' : 'Pay now';
    }

    function validatePaymentForm() {
        if (!termsCheckbox.checked) {
            showAlert('Please agree to the Terms and Conditions before continuing.');
            return false;
        }

        const requiredFields = paymentFields.querySelectorAll('[required]');
        for (const field of requiredFields) {
            if (!field.value || !field.value.trim()) {
                showAlert('Please fill in all required payment fields.');
                field.focus();
                return false;
            }
        }

        if (['gcash', 'maya'].includes(selectedMethod)) {
            const number = paymentFields.querySelector('.payment-input').value.replace(/\s/g, '');
            if (!/^09\d{9}$/.test(number)) {
                showAlert('Please enter a valid 11-digit mobile number starting with 09.');
                return false;
            }
        }

        if (selectedMethod === 'credit_card') {
            const inputs = paymentFields.querySelectorAll('.payment-input');
            const cardNumber = inputs[0].value.replace(/\s/g, '');
            const expiry = inputs[1].value;
            const cvc = inputs[2].value;

            if (cardNumber.length < 13 || cardNumber.length > 16) {
                showAlert('Please enter a valid card number.');
                return false;
            }
            if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                showAlert('Please enter a valid expiry date (MM/YY).');
                return false;
            }
            if (cvc.length < 3) {
                showAlert('Please enter a valid CVC.');
                return false;
            }
        }

        return true;
    }

    payNowBtn.addEventListener('click', async () => {
        hideAlert();
        if (!validatePaymentForm()) return;

        const commissionId = modalCommissionId.value;
        setLoading(true);

        try {
            const response = await fetch('/views/payments/partials/payment_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    commission_id: commissionId,
                    payment_method: selectedMethod,
                }),a
            });

            const result = await response.json();

            if (result.success) {
                showAlert('Payment successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect || '/views/payments/success.php?txn=' + result.transaction_id;
                }, 1200);
            } else {
                showAlert(result.message || 'Payment failed. Please try again.');
                setLoading(false);
            }
        } catch (err) {
            console.error('Payment error:', err);
            showAlert('Something went wrong while processing your payment. Please try again.');
            setLoading(false);
        }
    });

    // Reset modal to step 1 when closed
    const reviewModalEl = document.getElementById('reviewModal');
    if (reviewModalEl) {
        reviewModalEl.addEventListener('hidden.bs.modal', () => {
            showStep(reviewStep);
            selectedMethod = null;
            hideAlert();
            setLoading(false);
        });
    }
});
</script>