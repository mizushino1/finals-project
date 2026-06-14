<!-- =====================================================
     payment_modal.php
     Standalone 2-step payment modal.
     Step 1 → Choose method   (paymentMethodStep)
     Step 2 → Fill details    (paymentDetailsStep)

     Usage: include this file once per page.
     To open programmatically:
       document.getElementById('payCommissionId').value = '<id>';
       document.getElementById('payCommissionId').dataset.amount = '<amount>';
       new bootstrap.Modal(document.getElementById('paymentModal')).show();
     ===================================================== -->

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Hidden state carrier — set .value and .dataset.amount before showing modal -->
            <input type="hidden" id="payCommissionId" value="" data-amount="0">

            <!-- ===================== STEP 1: CHOOSE METHOD ===================== -->
            <div id="paymentMethodStep">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">
                        <i class="bi bi-credit-card-fill me-2"></i>Choose Payment Method
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted mb-4">Select how you'd like to pay for this commission.</p>

                    <div class="d-flex flex-column gap-2" id="paymentMethodOptions">

                        <button type="button"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="gcash" style="padding:.75rem 1rem;border-radius:.5rem;">
                            <span><i class="bi bi-phone-fill me-2" style="color:#007dfe;"></i>GCash</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="maya" style="padding:.75rem 1rem;border-radius:.5rem;">
                            <span><i class="bi bi-phone-fill me-2" style="color:#00d3ab;"></i>Maya</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="paypal" style="padding:.75rem 1rem;border-radius:.5rem;">
                            <span><i class="bi bi-paypal me-2" style="color:#00457c;"></i>PayPal</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="credit_card" style="padding:.75rem 1rem;border-radius:.5rem;">
                            <span><i class="bi bi-credit-card-fill me-2"></i>Credit / Debit Card</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button type="button"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-between payment-method-option"
                            data-method="bank" style="padding:.75rem 1rem;border-radius:.5rem;">
                            <span><i class="bi bi-bank2 me-2"></i>Bank Transfer</span>
                            <i class="bi bi-chevron-right"></i>
                        </button>

                    </div>

                    <div class="text-center mt-4">
                        <small class="text-muted">
                            <i class="bi bi-lock-fill"></i> All transactions are encrypted and secure.
                        </small>
                    </div>
                </div>
            </div>

            <!-- ===================== STEP 2: PAYMENT DETAILS ===================== -->
            <div id="paymentDetailsStep" class="d-none">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <button type="button" id="backToMethodsBtn"
                            class="btn btn-sm btn-link p-0 me-2 text-decoration-none">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <span id="paymentDetailsTitle">Payment Details</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">

                    <div id="checkoutAlert" class="alert d-none" role="alert"></div>

                    <!-- Order summary -->
                    <div class="card p-3 mb-4">
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

                    <!-- Dynamic fields injected by JS -->
                    <div id="paymentFields" class="mb-4"></div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="payTerms">
                        <label class="form-check-label" for="payTerms">
                            I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>.
                        </label>
                    </div>

                    <button class="btn btn-success w-100" id="payNowBtn">
                        <span id="payNowLabel">Pay now</span>
                        <span id="payNowSpinner"
                            class="spinner-border spinner-border-sm ms-2 d-none"
                            role="status" aria-hidden="true"></span>
                    </button>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        // ── Step elements ────────────────────────────────────
        const methodStep = document.getElementById('paymentMethodStep');
        const detailsStep = document.getElementById('paymentDetailsStep');

        const payCommId = document.getElementById('payCommissionId');
        const methodBtns = document.querySelectorAll('.payment-method-option');
        const backMethodsBtn = document.getElementById('backToMethodsBtn');

        const detailsTitle = document.getElementById('paymentDetailsTitle');
        const paymentFields = document.getElementById('paymentFields');
        const amountDisplay = document.getElementById('paymentAmountDisplay');
        const totalDisplay = document.getElementById('paymentTotalDisplay');

        const termsCheckbox = document.getElementById('payTerms');
        const checkoutAlert = document.getElementById('checkoutAlert');
        const payNowBtn = document.getElementById('payNowBtn');
        const payNowLabel = document.getElementById('payNowLabel');
        const payNowSpinner = document.getElementById('payNowSpinner');

        let selectedMethod = null;

        // ── Step navigation ──────────────────────────────────
        function showStep(step) {
            [methodStep, detailsStep].forEach(s => s.classList.add('d-none'));
            step.classList.remove('d-none');
        }

        // ── Method labels & field templates ─────────────────
        const methodLabels = {
            gcash: 'GCash',
            maya: 'Maya',
            paypal: 'PayPal',
            credit_card: 'Credit / Debit Card',
            bank: 'Bank Transfer',
        };

        // Maps frontend method keys → payment_method_id in DB
        const methodIds = {
            gcash: 1,
            maya: 2,
            paypal: 3,
            credit_card: 4,
            bank: 5,
        };

        const fieldTemplates = {
            gcash: () => `
            <label class="form-label">GCash Mobile Number</label>
            <input type="tel" class="form-control payment-input"
                placeholder="09XX XXX XXXX" pattern="09[0-9]{9}" maxlength="11" required>
            <small class="text-muted">You'll receive an OTP prompt to confirm.</small>
        `,
            maya: () => `
            <label class="form-label">Maya Mobile Number</label>
            <input type="tel" class="form-control payment-input"
                placeholder="09XX XXX XXXX" pattern="09[0-9]{9}" maxlength="11" required>
            <small class="text-muted">You'll receive an OTP prompt to confirm.</small>
        `,
            paypal: () => `
            <label class="form-label">PayPal Email</label>
            <input type="email" class="form-control payment-input"
                placeholder="you@example.com" required>
            <small class="text-muted">You'll be redirected to PayPal to confirm payment.</small>
        `,
            credit_card: () => `
            <label class="form-label">Card Number</label>
            <input type="text" class="form-control payment-input mb-2"
                placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric" required>
            <div class="row g-2">
                <div class="col-7">
                    <label class="form-label">Expiry</label>
                    <input type="text" class="form-control payment-input"
                        placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="col-5">
                    <label class="form-label">CVC</label>
                    <input type="text" class="form-control payment-input"
                        placeholder="123" maxlength="4" inputmode="numeric" required>
                </div>
            </div>
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
            <input type="text" class="form-control payment-input"
                placeholder="Enter account number" required>
        `,
        };

        // ── Card formatting ──────────────────────────────────
        function attachCardFormatting() {
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

        // ── Fetch commission price ───────────────────────────
        async function fetchCommissionAmount(commissionId) {
            try {
                const res = await fetch(`/finals-project/api/commissions/get_price.php?id=${commissionId}`);
                const data = await res.json();
                return data.success ? parseFloat(data.price) : 0;
            } catch (e) {
                console.error('Failed to fetch commission amount:', e);
                return 0;
            }
        }

        // ── Step 1 → Step 2 ─────────────────────────────────
        methodBtns.forEach(btn => {
            btn.addEventListener('click', async () => { // ← async added
                selectedMethod = btn.dataset.method;

                detailsTitle.textContent = methodLabels[selectedMethod] + ' Payment';
                paymentFields.innerHTML = (fieldTemplates[selectedMethod] || (() => ''))();

                if (selectedMethod === 'credit_card') attachCardFormatting();

                // Show loading while fetching real price
                amountDisplay.textContent = 'Loading...';
                totalDisplay.textContent = 'Loading...';
                showStep(detailsStep);

                const amount = await fetchCommissionAmount(payCommId.value);
                payCommId.dataset.amount = amount; // keep in sync for anything else that reads it

                const formatted = '₱' + amount.toLocaleString('en-PH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                amountDisplay.textContent = formatted;
                totalDisplay.textContent = formatted;
            });
        });

        if (backMethodsBtn) {
            backMethodsBtn.addEventListener('click', () => showStep(methodStep));
        }

        // ── Alert helpers ────────────────────────────────────
        function showAlert(msg, type = 'danger') {
            checkoutAlert.className = `alert alert-${type}`;
            checkoutAlert.textContent = msg;
            checkoutAlert.classList.remove('d-none');
        }

        function hideAlert() {
            checkoutAlert.classList.add('d-none');
        }

        function setLoading(on) {
            payNowBtn.disabled = on;
            payNowSpinner.classList.toggle('d-none', !on);
            payNowLabel.textContent = on ? 'Processing...' : 'Pay now';
        }

        // ── Validation ───────────────────────────────────────
        function validateForm() {
            if (!termsCheckbox.checked) {
                showAlert('Please agree to the Terms and Conditions before continuing.');
                return false;
            }

            const requiredFields = paymentFields.querySelectorAll('[required]');
            for (const f of requiredFields) {
                if (!f.value || !f.value.trim()) {
                    showAlert('Please fill in all required payment fields.');
                    f.focus();
                    return false;
                }
            }

            if (['gcash', 'maya'].includes(selectedMethod)) {
                const num = paymentFields.querySelector('.payment-input').value.replace(/\s/g, '');
                if (!/^09\d{9}$/.test(num)) {
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

        // ── Pay Now submission ───────────────────────────────
        payNowBtn.addEventListener('click', async () => {
            hideAlert();
            if (!validateForm()) return;

            const commissionId = payCommId.value;
            const paymentMethodId = methodIds[selectedMethod];

            setLoading(true);

            try {
                const res = await fetch('/finals-project/api/payments/initiate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        commission_id: commissionId,
                        payment_method_id: paymentMethodId,
                    }),
                });
                const data = await res.json();

                if (data.success) {
                    showAlert('Payment successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect ||
                            (window.BASE_URL ?? '/') + 'payments/success?txn=' + data.transaction_id;
                    }, 1200);
                } else {
                    showAlert(data.message || 'Payment failed. Please try again.');
                    setLoading(false);
                }
            } catch (err) {
                console.error('Payment error:', err);
                showAlert('Something went wrong while processing your payment. Please try again.');
                setLoading(false);
            }
        });

        // ── Reset on close ────────────────────────────────────
        const payModalEl = document.getElementById('paymentModal');
        if (payModalEl) {
            payModalEl.addEventListener('hidden.bs.modal', () => {
                showStep(methodStep);
                selectedMethod = null;
                hideAlert();
                setLoading(false);
                if (termsCheckbox) termsCheckbox.checked = false;
            });
        }
    });
</script>