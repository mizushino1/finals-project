<main class="min-vh-100 d-flex flex-column justify-content-center align-items-center position-relative fp-page">

    <div id="forgotPasswordCard" class="fp-card theme-border">
        <div class="text-center">
            <h2 class="theme-font">Forgot Password?</h2>
            <p class="fp-card__subtitle">Enter your email to receive a verification code</p>

            <form id="forgotPasswordForm">
                <div class="mb-4 text-start">
                    <label for="emailInput" class="auth-label mb-2">Email</label>
                    <div class="input-group auth-input-group-dark">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control"
                            id="emailInput"
                            placeholder="Enter your email"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient-fill">
                    Send OTP
                </button>
            </form>
        </div>
    </div>

    <div id="otpVerificationCard" class="fp-card d-none">
        <div class="text-center">
            <h2 class="fp-card__title">OTP Verification</h2>
            <p class="fp-card__subtitle">We sent a 6-digit code to your email</p>

            <form id="otpVerificationForm">
                <div class="mb-4">

                    <!-- OTP inputs -->
                    <div class="fp-otp-strip">
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]" autocomplete="one-time-code">
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]">
                        <span class="fp-otp-divider">—</span>
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]">
                        <input type="text" class="form-control otp-input" maxlength="1" required inputmode="numeric" pattern="[0-9]">
                    </div>

                    <!-- Resend row -->
                    <div class="fp-resend-row">
                        <span>Resend code in <strong class="text-white" id="otpCountdown">60s</strong></span>
                        <a href="#" class="gold-text text-decoration-none small fw-bold" id="resendOtpBtn">Resend OTP</a>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold">
                    Verify Code
                </button>
            </form>
        </div>
    </div>

    <div id="resetPasswordCard" class="fp-card d-none">
        <div class="text-center">
            <h2 class="fp-card__title">Reset Password</h2>
            <p class="fp-card__subtitle">Choose a strong new password</p>

            <form id="resetPasswordForm">
                <div class="mb-3 text-start">
                    <label for="newPassword" class="auth-label mb-2">New Password</label>
                    <div class="input-group auth-input-group-dark position-relative">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control pe-5"
                            id="newPassword"
                            placeholder="Enter new password"
                            required
                        >
                        <span class="eye-toggle-icon" aria-label="Toggle password visibility">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label for="confirmPassword" class="auth-label mb-2">Confirm Password</label>
                    <div class="input-group auth-input-group-dark position-relative">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input
                            type="password"
                            class="form-control pe-5"
                            id="confirmPassword"
                            placeholder="Confirm new password"
                            required
                        >
                        <span class="eye-toggle-icon" aria-label="Toggle password visibility">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

</main>

<script src="<?php echo BASE_URL; ?>public/js/auth.js"></script>