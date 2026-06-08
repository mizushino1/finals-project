document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════
       FEATURED ARTISTS GRID
    ══════════════════════════════════════════ */
    const grid = document.getElementById('featuredArtistsGrid');

    if (grid) {
        fetch(`${BASE_URL}api/artists/fetch_all.php`)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.data.length) return;

                grid.innerHTML = data.data.slice(0, 4).map(a => `
                    <div class="col-6 col-lg-3">
                        <div class="artist-card">
                            <div class="artist-card__cover">
                                <div class="artist-card__avatar">
                                    ${a.avatar_url
                                        ? `<img src="${BASE_URL}${a.avatar_url}" alt="${a.username}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`
                                        : '<i class="bi bi-person"></i>'
                                    }
                                </div>
                            </div>
                            <div class="artist-card__body">
                                <p class="artist-card__name">${a.username}</p>
                                <p class="artist-card__tag">Visual Artist</p>
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                    <span class="artist-card__rate">From ₱${parseFloat(a.starting_rate).toLocaleString()}</span>
                                    <span class="artist-card__badge">Open</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            })
            .catch(() => {});
    }

    /* ══════════════════════════════════════════
       THEME TOGGLE
    ══════════════════════════════════════════ */
    const toggle = document.getElementById('themeToggle');

    if (toggle) {
        const html = document.documentElement;
        const icon = toggle.querySelector('i');

        // Sync icon to whatever theme is already set on load
        icon.className = html.getAttribute('data-bs-theme') === 'dark'
            ? 'bi bi-sun'
            : 'bi bi-moon-stars';

        toggle.addEventListener('click', () => {
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            icon.className = isDark ? 'bi bi-moon-stars' : 'bi bi-sun';
        });
    }

    /* ══════════════════════════════════════════
       SCROLL FADE-IN (IntersectionObserver)
    ══════════════════════════════════════════ */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity    = '1';
                entry.target.style.transform  = 'translateY(0)';
                observer.unobserve(entry.target); // fire once, then stop watching
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.section, .stats-band, .features-strip, .cta-section').forEach(el => {
        el.style.opacity    = '0';
        el.style.transform  = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

});