function setRole(role) {
    document.getElementById('registerRole').value = role;
}
// ── LOGIN FETCH ──
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();
 
        const username = document.getElementById('loginUsername').value.trim();
        const password = document.getElementById('loginPassword').value.trim();
        const alert = document.getElementById('auth-alert');
 
        try {
            const res = await fetch(BASE_URL + 'api/auth/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const data = await res.json();
 
            if (data.success) {
                // Redirect based on role
                if (data.role === 'admin') {
                    window.location.href = BASE_URL + 'admin';
                } else {
                    window.location.href = BASE_URL;
                }
            } else {
                alert.className = 'alert alert-danger w-100 px-lg-4';
                alert.textContent = data.message;
                alert.classList.remove('d-none');
            }
        } catch (err) {
            alert.className = 'alert alert-danger w-100 px-lg-4';
            alert.textContent = 'Something went wrong. Please try again.';
            alert.classList.remove('d-none');
        }
    });
}
 
// ── REGISTER FETCH ──
const registerForm = document.getElementById('registerForm');
if (registerForm) {
 
    // Logic to handle tab switching for the role
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const role = this.innerText.toLowerCase(); // 'user' or 'artist'
            const wrapper = document.getElementById('artistStartAtWrapper');
 
            if (wrapper) {
                wrapper.classList.toggle('d-none', role !== 'artist');
            }
        });
    });
 
    registerForm.addEventListener('submit', async function (e) {
        e.preventDefault();
 
        const role = document.getElementById('registerRole').value;
        const firstName = document.getElementById('registerFirstName').value.trim();
        const lastName = document.getElementById('registerLastName').value.trim();
        const username = document.getElementById('registerUsername').value.trim();
        const password = document.getElementById('registerPassword').value.trim();
        const startAt = document.getElementById('registerStartAt')?.value || 0;
        const authModalEl = new bootstrap.Modal(document.getElementById('authModal'));
        const modalMessage = document.getElementById('modalMessage');
        const actionBtn = document.getElementById('modalActionBtn');
 
        try {
            const res = await fetch(BASE_URL + 'api/auth/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ role, first_name: firstName, last_name: lastName, username, password, start_at: startAt })
            });
            const data = await res.json();
 
            modalMessage.textContent = data.success ? data.message + ' You can now log in.' : data.message;
 
            if (data.success) {
                actionBtn.textContent = 'Go to Login';
                actionBtn.onclick = () => {
                    authModalEl.hide();
                    switchTo('login');
                };
            } else {
                actionBtn.textContent = 'Close';
                actionBtn.onclick = () => authModalEl.hide();
            }
 
            authModalEl.show();
        } catch (err) {
            modalMessage.textContent = 'Something went wrong. Please try again.';
            actionBtn.textContent = 'Close';
            actionBtn.onclick = () => authModalEl.hide();
            authModalEl.show();
        }
    });
}
 
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
    // ─────────────────────────────────────────────────────────────────────────
 
    // Dynamic eye icon visibility based on input length (handles autofills)
    document.querySelectorAll('.auth-input-group, .input-group').forEach(group => {
        const input = group.querySelector('input[type="password"]');
        const toggle = group.querySelector('.eye-toggle-icon');
        if (!input || !toggle) return;
 
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
    // ── LOGIN + REGISTER SWITCH (Login <-> Register) ─────────────────────────
    // ── Hoisted here so register fetch handler above can also call switchTo ──
    // ─────────────────────────────────────────────────────────────────────────
 
    const card = document.getElementById('authCard');
    const infoLogin = document.getElementById('infoLogin');
    const infoRegister = document.getElementById('infoRegister');
    const formLogin = document.getElementById('formLogin');
    const formRegister = document.getElementById('formRegister');
    const allContent = [infoLogin, infoRegister, formLogin, formRegister];
 
    // Exposed to window so the register fetch handler (outside DOMContentLoaded) can reach it
    window.switchTo = function switchTo(mode) {
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
 
 
    // ─────────────────────────────────────────────────────────────────────────
    // ── EXCLUSIVE LOGIN FUNCTIONALITY (Only runs on login.php) ───────────────
    // ─────────────────────────────────────────────────────────────────────────
    if (isLogin) {
        const goToRegister = document.getElementById('goToRegister');
 
        // Resize guard
        let resizeTimer = null;
        window.addEventListener('resize', () => {
            if (!card) return;
            card.classList.add('no-transition');
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => card.classList.remove('no-transition'), 150);
        });
 
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
 
        if (goToRegister) {
            goToRegister.addEventListener('click', (e) => {
                e.preventDefault();
                switchTo('register');
            });
        }
        document.querySelectorAll('.login-trigger').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                switchTo('login');
            });
        });
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