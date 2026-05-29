<main class="min-vh-100 d-flex flex-column justify-content-center align-items-center" style="background: #000; color: #fff; padding: 20px;">
    
    <header class="w-100 position-absolute top-0 start-0 d-flex justify-content-between align-items-center px-4 py-3" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(10px); z-index: 100;">
        <div class="d-flex align-items-center">
            <span class="fw-bold tracking-widest text-uppercase gold-text" style="font-family: serif; font-size: 1.5rem; color: #d4af37;">ARTOVIA</span>
        </div>
        <div>
            <a href="<?php echo BASE_URL; ?>login" class="btn btn-sm px-3 py-1 text-white fw-bold text-decoration-none" style="background: #222; border: 1px solid #444; border-radius: 4px;">Log In / Sign Up</a>
        </div>
    </header>

    <div id="forgotPasswordCard" class="auth-card p-4 p-md-5 w-100" style="max-width: 550px; background: #0a0a0a; border: 2px solid #d4af37; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5);">
        <div class="text-center">
            <h2 class="mb-2 text-uppercase tracking-wide" style="font-family: serif; color: #d4af37; font-size: 2rem;">Forgot Password?</h2>
            <p class="mb-4 text-white-50 small">Enter your email to receive a verification code</p>

            <form id="forgotPasswordForm">
                <div class="mb-4 text-start">
                    <label for="emailInput" class="form-label mb-2 small tracking-wider text-white-50 text-uppercase">Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-secondary text-white-50" style="border-right: none;"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control bg-transparent text-white border-secondary px-3 py-2" id="emailInput" placeholder="Enter your email" required style="box-shadow: none; border-left: none;">
                    </div>
                </div>
                <button type="submit" class="btn w-100 py-2.5 fw-bold text-uppercase tracking-wider" style="background: linear-gradient(135deg, #d4af37, #aa8416); color: #000; border: none; border-radius: 4px;">Send OTP</button>
            </form>
        </div>
    </div>

    <div id="otpVerificationCard" class="auth-card p-4 p-md-5 w-100 d-none" style="max-width: 550px; background: #0a0a0a; border: 2px solid #d4af37; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5);">
        <div class="text-center">
            <h2 class="mb-2 text-uppercase tracking-wide" style="font-family: serif; color: #d4af37; font-size: 2rem;">OTP Verification</h2>
            <p class="mb-4 text-white-50 small">We sent a 6-digit code to your email</p>

            <form id="otpVerificationForm">
                <div class="mb-4">
                    <div class="otp-container d-flex justify-content-center align-items-center mx-auto p-2" style="max-width: 380px; background: rgba(255,255,255,0.03); border: 2px solid #d4af37; border-radius: 50px;">
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                        <span class="text-white-50 mx-1 fw-bold">-</span>
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                        <input type="text" class="form-control otp-input text-center mx-1 text-white bg-transparent fw-bold" maxlength="1" required style="border: none; border-bottom: 2px solid #fff; border-radius: 0; width: 35px; height: 40px; box-shadow: none; font-size: 1.2rem;">
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center mx-auto" style="max-width: 320px;">
                        <span class="text-white-50 small">Resend code in <span class="text-white fw-bold">60s</span></span>
                        <a href="#" class="text-decoration-none small fw-bold" style="color: #d4af37;">Resend OTP</a>
                    </div>
                </div>
                <button type="submit" class="btn w-100 py-2.5 fw-bold text-uppercase tracking-wider" style="background: linear-gradient(135deg, #d4af37, #aa8416); color: #000; border: none; border-radius: 4px;">Verify Code</button>
            </form>
        </div>
    </div>

    <div id="resetPasswordCard" class="auth-card p-4 p-md-5 w-100 d-none" style="max-width: 550px; background: #0a0a0a; border: 2px solid #d4af37; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.5);">
        <div class="text-center">
            <h2 class="mb-4 text-uppercase tracking-wide" style="font-family: serif; color: #d4af37; font-size: 2rem;">Reset Password</h2>

            <form id="resetPasswordForm">
                <div class="mb-3 text-start">
                    <label for="newPassword" class="form-label text-white-50 small mb-2 text-uppercase tracking-wider">New Password</label>
                    <div class="input-group position-relative">
                        <span class="input-group-text bg-transparent border-secondary text-white-50" style="border-right: none;"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control bg-transparent text-white border-secondary py-2 pe-5" id="newPassword" placeholder="Enter password" required style="box-shadow: none; border-left: none;">
                        <span class="position-absolute end-0 top-50 translate-middle-y me-3 eye-toggle-icon" style="cursor: pointer; z-index: 10; color: #aaa;"><i class="bi bi-eye"></i></span>
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label for="confirmPassword" class="form-label text-white-50 small mb-2 text-uppercase tracking-wider">Confirm Password</label>
                    <div class="input-group position-relative">
                        <span class="input-group-text bg-transparent border-secondary text-white-50" style="border-right: none;"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control bg-transparent text-white border-secondary py-2 pe-5" id="confirmPassword" placeholder="Enter password" required style="box-shadow: none; border-left: none;">
                        <span class="position-absolute end-0 top-50 translate-middle-y me-3 eye-toggle-icon" style="cursor: pointer; z-index: 10; color: #aaa;"><i class="bi bi-eye"></i></span>
                    </div>
                </div>

                <button type="submit" class="btn w-100 py-2.5 fw-bold text-uppercase tracking-wider" style="background: linear-gradient(135deg, #d4af37, #aa8416); color: #000; border: none; border-radius: 4px;">Reset Password</button>
            </form>
        </div>
    </div>

</main>

<script src="<?php echo BASE_URL; ?>public/js/auth.js"></script>