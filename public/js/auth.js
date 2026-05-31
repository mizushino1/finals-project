document.addEventListener('DOMContentLoaded', () => {
    const path = window.location.pathname.toLowerCase();
    
    // Determine the current page context
    const isLogin = path.includes('login.php') || path.includes('/login');
    const isSettings = path.includes('settings.php') || path.includes('/settings');
    const isForgotPassword = path.includes('forgot_password.php') || path.includes('/forgot_password');

    // If it's none of these pages, exit immediately
    if (!isLogin && !isSettings && !isForgotPassword) return;

    // ─────────────────────────────────────────────────────────────────────────
    // ── SHARED FUNCTIONALITY: Password Eye Toggle ────────────────────────────
    // ── (Runs on login.php, settings.php, and forgot_password.php) ───────────
    // ─────────────────────────────────────────────────────────────────────────
    
    // Dynamic eye icon visibility based on input length (handles autofills)
    document.querySelectorAll('.auth-input-group, .input-group').forEach(group => {
        const input  = group.querySelector('input[type="password"]');
        const toggle = group.querySelector('.eye-toggle-icon');
        if (!input || !toggle) return;

        // Run immediately on load
        toggle.classList.toggle('visible', input.value.length > 0);

        input.addEventListener('input', () => {
            toggle.classList.toggle('visible', input.value.length > 0);
        });
    });

    // Click event to toggle input masking
    document.querySelectorAll('.eye-toggle-icon').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const wrapper = toggle.closest('.auth-input-group') || toggle.closest('.input-group');
            if (!wrapper) return;

            const input = wrapper.querySelector('input[type="password"], input[type="text"]');
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const icon = toggle.querySelector('i');
            if (icon) {
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });


    // ─────────────────────────────────────────────────────────────────────────
    // ── EXCLUSIVE LOGIN FUNCTIONALITY (Only runs on login.php) ───────────────
    // ─────────────────────────────────────────────────────────────────────────
    if (isLogin) {
        const card = document.getElementById('authCard');
        const infoLogin = document.getElementById('infoLogin');
        const infoRegister = document.getElementById('infoRegister');
        const formLogin = document.getElementById('formLogin');
        const formRegister = document.getElementById('formRegister');
        const goToRegister = document.getElementById('goToRegister');
        const goToLogin = document.getElementById('goToLogin');

        const allContent = [infoLogin, infoRegister, formLogin, formRegister];

        // Resize guard
        let resizeTimer = null;
        window.addEventListener('resize', () => {
            if (!card) return;
            card.classList.add('no-transition');
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => card.classList.remove('no-transition'), 150);
        });

        // Mode switch (Login <-> Register)
        function switchTo(mode) {
            if (!card || allContent.some(el => !el)) return;
            const toRegister = mode === 'register';

            allContent.forEach(el => {
                el.style.transition = 'none';
                el.style.opacity = '0';
            });

            requestAnimationFrame(() => {
                infoLogin.classList.toggle('d-none', toRegister);
                infoRegister.classList.toggle('d-none', !toRegister);
                formLogin.classList.toggle('d-none', toRegister);
                formRegister.classList.toggle('d-none', !toRegister);

                card.classList.toggle('auth-card--register', toRegister);

                const visible = toRegister ? [infoRegister, formRegister] : [infoLogin, formLogin];

                setTimeout(() => {
                    visible.forEach(el => {
                        el.style.transition = 'opacity 0.3s ease-in-out';
                        el.style.opacity = '1';
                    });
                }, 1000);
            });
        }

        // Initial page load fade-in
        if (card) {
            const visible = card.classList.contains('auth-card--register')
                ? [infoRegister, formRegister]
                : [infoLogin, formLogin];

            visible.forEach(el => {
                if (el) {
                    el.style.transition = 'none';
                    el.style.opacity = '0';
                }
            });

            setTimeout(() => {
                visible.forEach(el => {
                    if (el) {
                        el.style.transition = 'opacity 0.4s ease-in-out';
                        el.style.opacity = '1';
                    }
                });
            }, 50);
        }

        if (goToRegister) goToRegister.addEventListener('click', () => switchTo('register'));
        if (goToLogin) goToLogin.addEventListener('click', () => switchTo('login'));
    }


    // ─────────────────────────────────────────────────────────────────────────
    // ── EXCLUSIVE FORGOT PASSWORD & OTP (Only runs on forgot_password.php) ───
    // ─────────────────────────────────────────────────────────────────────────
    if (isForgotPassword) {
        const card1 = document.getElementById('forgotPasswordCard');
        const card2 = document.getElementById('otpVerificationCard');
        const card3 = document.getElementById('resetPasswordCard');

        // Multi-step form transition: Step 1 -> Step 2
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

        // Multi-step form transition: Step 2 -> Step 3
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

        // Final step: Reset form submit
        const resetForm = document.getElementById('resetPasswordForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function (e) {
                e.preventDefault();
                alert('Password successfully reset!');
                window.location.href = '../login';
            });
        }

        // OTP numeric auto-advance focus logic
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
    }
});