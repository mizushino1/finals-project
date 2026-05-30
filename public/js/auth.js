(() => {
    const card = document.getElementById('authCard');
    const infoLogin = document.getElementById('infoLogin');
    const infoRegister = document.getElementById('infoRegister');
    const formLogin = document.getElementById('formLogin');
    const formRegister = document.getElementById('formRegister');
    const goToRegister = document.getElementById('goToRegister');
    const goToLogin = document.getElementById('goToLogin');

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

            const visible = toRegister
                ? [infoRegister, formRegister]
                : [infoLogin, formLogin];

            setTimeout(() => {
                visible.forEach(el => {
                    el.style.transition = 'opacity 0.3s ease-in-out';
                    el.style.opacity = '1';
                });
            }, 1000);
        });
    }

    // ── Initial page load fade-in ─────────────────────────────────────────────
    window.addEventListener('DOMContentLoaded', () => {
        const visible = card.classList.contains('auth-card--register')
            ? [infoRegister, formRegister]
            : [infoLogin, formLogin];

        visible.forEach(el => {
            el.style.transition = 'none';
            el.style.opacity = '0';
        });

        setTimeout(() => {
            visible.forEach(el => {
                el.style.transition = 'opacity 0.4s ease-in-out';
                el.style.opacity = '1';
            });
        }, 50);
    });

    // ── Eye toggle click (show/hide password) ─────────────────────────────────
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
    goToLogin.addEventListener('click', () => switchTo('login'));
})();

document.addEventListener('DOMContentLoaded', function () {

    // ── Show eye toggle only when password field has content ──────────────────
    document.querySelectorAll('.auth-input-group').forEach(group => {
        const input  = group.querySelector('input[type="password"]');
        const toggle = group.querySelector('.eye-toggle-icon');
        if (!input || !toggle) return;

        input.addEventListener('input', () => {
            toggle.classList.toggle('visible', input.value.length > 0);
        });
    });

    // ── Forgot password multi-step ────────────────────────────────────────────
    const card1 = document.getElementById('forgotPasswordCard');
    const card2 = document.getElementById('otpVerificationCard');
    const card3 = document.getElementById('resetPasswordCard');

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

    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Password successfully reset!');
            window.location.href = '../login';
        });
    }

    // ── OTP numeric auto-advance ──────────────────────────────────────────────
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

});