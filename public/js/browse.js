/* browse.js — Artovia Browse Page */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Hamburger menu toggle ── */
    const hamburger = document.querySelector('.navbar__hamburger');
    const navLinks  = document.querySelector('.navbar__links');
    const navAuth   = document.querySelector('.navbar__auth');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            const open = navLinks.style.display === 'flex';
            navLinks.style.display = open ? '' : 'flex';
            navLinks.style.flexDirection = 'column';
            navLinks.style.position = 'absolute';
            navLinks.style.top = '58px';
            navLinks.style.left = '0';
            navLinks.style.right = '0';
            navLinks.style.background = '#111';
            navLinks.style.padding = '1rem 1.5rem';
            navAuth.style.display = open ? '' : 'flex';
            navAuth.style.position = 'absolute';
            navAuth.style.top = open ? '' : 'calc(58px + 9rem)';
            navAuth.style.left = '0';
            navAuth.style.right = '0';
            navAuth.style.justifyContent = 'center';
            navAuth.style.padding = '0.75rem 1.5rem';
            navAuth.style.background = '#111';
        });
    }

    /* ── Wishlist heart toggle ── */
    document.querySelectorAll('.artist-card__wishlist').forEach(btn => {
        btn.addEventListener('click', () => {
            const svg = btn.querySelector('svg');
            const active = btn.dataset.active === '1';
            if (active) {
                svg.setAttribute('fill', 'none');
                svg.style.color = '';
                btn.dataset.active = '0';
            } else {
                svg.setAttribute('fill', 'currentColor');
                svg.style.color = '#c9873a';
                btn.dataset.active = '1';
            }
        });
    });

    /* ── Filter sidebar: live label highlight ── */
    document.querySelectorAll('.filter-group__list input').forEach(input => {
        input.addEventListener('change', () => {
            const label = input.closest('label');
            if (input.type === 'checkbox') {
                label.style.fontWeight = input.checked ? '600' : '';
                label.style.color = input.checked ? 'var(--clr-dark)' : '';
            }
        });
    });

});