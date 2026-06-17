document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════════════════
       FEATURED ARTISTS GRID
    ══════════════════════════════════════════ */
    const grid = document.getElementById('featuredArtistsGrid');

    if (grid) {
        // Quick color array fallback to generate placeholder styles consistently
        const PALETTE = [
            ['#e8d5b0', '#a8834a'],
            ['#d4e8d0', '#3a7a4a'],
            ['#d0dce8', '#3a5a7a'],
            ['#e8d0e0', '#7a3a5a']
        ];
        
        const GRADIENTS = [
            'linear-gradient(135deg,#f0e6cc 0%,#ede8de 100%)',
            'linear-gradient(135deg,#dce8d8 0%,#e8f0e4 100%)',
            'linear-gradient(135deg,#d8dce8 0%,#e4e8f0 100%)',
            'linear-gradient(135deg,#e8d8e4 0%,#f0e4ec 100%)'
        ];

        fetch(`${BASE_URL}api/artists/fetch_all.php`)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.data.length) return;

                grid.innerHTML = data.data.slice(0, 4).map((a, index) => {
                    const isOpen = a.is_available == 1;
                    const name = a.username ?? 'Unknown';
                    const rateNum = parseFloat(a.starting_rate ?? 0);
                    
                    const badgeClass = isOpen ? 'artist-card__badge--open' : 'artist-card__badge--closed';
                    const badgeText  = isOpen ? 'Open' : 'Closed';
                    
                    const priceDisplay = rateNum > 0
                        ? `₱${rateNum.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                        : 'Free';

                    // Generate a nice text placeholder if avatar string is missing
                    const [bg, fg] = PALETTE[index % PALETTE.length];
                    const letter = (name.trim()[0] ?? '?').toUpperCase();
                    const coverStyle = GRADIENTS[index % GRADIENTS.length];

                    const avatarHtml = a.avatar_url
                        ? `<img src="${BASE_URL}${a.avatar_url}" class="rounded-circle w-100 h-100 object-fit-cover" alt="${name}">`
                        : `<div class="artist-card__avatar-placeholder w-100 h-100 rounded-circle d-flex align-items-center justify-content-center" style="background:${bg};"><span style="font-family:var(--font-ui);font-weight:700;font-size:1rem;color:${fg};">${letter}</span></div>`;

                    return `
                        <div class="col-6 col-lg-3">
                            <div class="artist-card h-100 border rounded-3 position-relative d-flex flex-column shadow-sm overflow-visible bg-card">
                                
                                <div class="artist-card__cover position-relative w-100 d-flex align-items-end px-3" style="background:${coverStyle};">
                                    <div class="artist-card__avatar rounded-circle border p-0 position-absolute start-0 end-0 mx-auto shadow-sm bg-card d-flex align-items-center justify-content-center">
                                        ${avatarHtml}
                                    </div>
                                </div>

                                <div class="artist-card__body p-2 p-sm-3 d-flex flex-column gap-1 flex-grow-1">
                                    <div class="artist-card__header d-flex align-items-start justify-content-between gap-1">
                                        <span class="artist-card__name text-truncate fw-bold">${name}</span>
                                        <span class="artist-card__rating d-inline-flex align-items-center gap-1 fw-bold text-nowrap">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" style="width:11px; height:11px; fill:var(--clr-star);"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            4.8
                                        </span>
                                    </div>
                                    <p class="artist-card__tag m-0 text-muted small">Visual Artist</p>

                                    <div class="artist-card__meta d-flex align-items-center justify-content-between mt-auto pt-2 gap-1 flex-wrap">
                                        <span class="artist-card__badge d-inline-flex align-items-center fw-bold text-uppercase ${badgeClass}">${badgeText}</span>
                                        <span class="artist-card__starting fw-bold text-nowrap">from ${priceDisplay}</span>
                                    </div>
                                </div>

                                <div class="artist-card__actions d-flex p-2 p-sm-3 pt-0 gap-2 mt-auto">
                                    <a href="${BASE_URL}profile?id=${a.account_id}&role=artist" class="btn-artovia-outline text-center flex-grow-1 p-2 py-1.5 rounded-2 fs-fluid-xs">Profile</a>
                                </div>

                            </div>
                        </div>
                    `;
                }).join('');
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