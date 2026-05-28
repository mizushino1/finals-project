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

            // 4. After the slide finishes, fade the new content in
            //    transitionend fires per-property; we only want to act once.
            const onSlideEnd = (e) => {
                // Only react to the transform transition on one of the panels
                if (e.target !== card.querySelector('.form-side') || e.propertyName !== 'transform') return;
                card.querySelector('.form-side').removeEventListener('transitionend', onSlideEnd);

                // Fade in only the visible content blocks
                const visible = toRegister
                    ? [infoRegister, formRegister]
                    : [infoLogin,    formLogin];

                visible.forEach(el => {
                    el.style.transition = 'opacity 0.3s ease-in-out';
                    el.style.opacity    = '1';
                });
            };

            card.querySelector('.form-side').addEventListener('transitionend', onSlideEnd);
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