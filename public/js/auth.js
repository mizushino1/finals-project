(() => {
    const card         = document.getElementById('authCard');
    const infoLogin    = document.getElementById('infoLogin');
    const infoRegister = document.getElementById('infoRegister');
    const formLogin    = document.getElementById('formLogin');
    const formRegister = document.getElementById('formRegister');
    const goToRegister = document.getElementById('goToRegister');
    const goToLogin    = document.getElementById('goToLogin');

    // All four content blocks for easy bulk operations
    const allContent = [infoLogin, infoRegister, formLogin, formRegister];

    // ── Resize guard ─────────────────────────────────────────────────────────
    let resizeTimer = null;
    window.addEventListener('resize', () => {
        card.classList.add('no-transition');
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => card.classList.remove('no-transition'), 150);
    });

    // ── Mode switch ───────────────────────────────────────────────────────────
    function switchTo(mode) {
        const toRegister = mode === 'register';
    
        // 1. Instantly hide all content (no fade — instant so it's gone before slide starts)
        allContent.forEach(el => {
            el.style.transition = 'none';
            el.style.opacity    = '0';
        });
    
        // 2. Swap d-none so the correct content is ready but still invisible
        //    (tiny rAF delay ensures the opacity:0 paint lands before d-none toggling)
        requestAnimationFrame(() => {
            infoLogin.classList.toggle('d-none',    toRegister);
            infoRegister.classList.toggle('d-none', !toRegister);
            formLogin.classList.toggle('d-none',    toRegister);
            formRegister.classList.toggle('d-none', !toRegister);
    
            // 3. Trigger the 1s panel slide
            card.classList.toggle('auth-card--register', toRegister);
    
            // 4. After a delay, fade the new content in
            const visible = toRegister
                ? [infoRegister, formRegister]
                : [infoLogin,    formLogin];
    
            setTimeout(() => {
                visible.forEach(el => {
                    el.style.transition = 'opacity 0.3s ease-in-out';
                    el.style.opacity    = '1';
                });
            }, 1000); // Delay should match the CSS transition duration (1s)
        });
    }

    // ── Initial page load: fade in current content without any slide ──────────
    window.addEventListener('DOMContentLoaded', () => {
        const visible = card.classList.contains('auth-card--register')
            ? [infoRegister, formRegister]
            : [infoLogin,    formLogin];

        visible.forEach(el => {
            el.style.transition = 'none';
            el.style.opacity    = '0';
        });

        // Small delay so the browser has painted the layout before fading in
        setTimeout(() => {
            visible.forEach(el => {
                el.style.transition = 'opacity 0.4s ease-in-out';
                el.style.opacity    = '1';
            });
        }, 50);
    });

    // ── Eye toggle ────────────────────────────────────────────────────────────
    document.querySelectorAll('.eye-toggle-icon').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const input = toggle.closest('.input-group')
                               .querySelector('input[type="password"], input[type="text"]');
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            const icon = toggle.querySelector('i');
            if (icon) icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });

    goToRegister.addEventListener('click', () => switchTo('register'));
    goToLogin.addEventListener('click',    () => switchTo('login'));
})();

document.addEventListener("DOMContentLoaded", function () {
    // 1. Get references to the layout component cards
    const card1 = document.getElementById('forgotPasswordCard');
    const card2 = document.getElementById('otpVerificationCard');
    const card3 = document.getElementById('resetPasswordCard');

    // 2. Step 1: Submit Form -> Advance to Step 2 Card
    const fpForm = document.getElementById('forgotPasswordForm');
    if (fpForm) {
        fpForm.addEventListener('submit', function (e) {
            e.preventDefault(); 
            if (card1 && card2) {
                card1.classList.add('d-none');
                card2.classList.remove('d-none');
            }
        });
    }

    // 3. Step 2: Submit OTP -> Advance to Step 3 Card
    const otpForm = document.getElementById('otpVerificationForm');
    if (otpForm) {
        otpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (card2 && card3) {
                card2.classList.add('d-none');
                card3.classList.remove('d-none');
            }
        });
    }

    // 4. Step 3: Complete Password Reset
    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert("Password successfully reset!");
            // Redirect smoothly back to your primary login view
            window.location.href = "../login";
        });
    }

    // 5. Numeric Auto-Advance Mechanics for OTP inputs
    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    // 6. Inline password eye toggles 
    const allPasswordToggles = document.querySelectorAll('.eye-toggle-icon');
    allPasswordToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const container = this.parentElement;
            const inputField = container.querySelector('input');
            const icon = this.querySelector('i');

            if (inputField && icon) {
                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    inputField.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
});