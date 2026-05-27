(() => {
    const card        = document.getElementById('authCard');
    const infoPanel   = document.getElementById('infoPanel');
    const formPanel   = document.getElementById('formPanel');
    const infoLogin   = document.getElementById('infoLogin');
    const infoRegister= document.getElementById('infoRegister');
    const formLogin   = document.getElementById('formLogin');
    const formRegister= document.getElementById('formRegister');
    const goToRegister= document.getElementById('goToRegister');
    const goToLogin   = document.getElementById('goToLogin');

    // Swap visible content, then slide panels after a brief delay
    // so Bootstrap's d-none removal renders before the transition fires
    function switchTo(mode) {
        const toRegister = mode === 'register';

        // Fade out current content
        [infoLogin, infoRegister, formLogin, formRegister].forEach(el => {
            el.style.opacity = '0';
            el.style.transition = 'opacity 0.2s ease';
        });

        setTimeout(() => {
            // Swap info content
            infoLogin.classList.toggle('d-none',  toRegister);
            infoRegister.classList.toggle('d-none', !toRegister);

            // Swap form content
            formLogin.classList.toggle('d-none',    toRegister);
            formRegister.classList.toggle('d-none', !toRegister);

            // Slide the card panels
            // On register: form moves left (col-md-7 first), info moves right
            card.classList.toggle('auth-card--register', toRegister);

            // Fade content back in
            [infoLogin, infoRegister, formLogin, formRegister].forEach(el => {
                el.style.opacity = '1';
            });
        }, 200);
    }

    goToRegister.addEventListener('click', () => switchTo('register'));
    goToLogin.addEventListener('click',    () => switchTo('login'));
})();