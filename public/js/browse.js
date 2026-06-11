/* ══════════════════════════════════════════════════════════════
   browse.js — Artovia Browse Page
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    let allArtists = [];
    let activeStyle = 'all';

    const artistGrid = document.getElementById('artistGrid');
    const artistGridLoading = document.getElementById('artistGridLoading');
    const artistGridError = document.getElementById('artistGridError');
    const artistGridEmpty = document.getElementById('artistGridEmpty');
    const topArtistsGrid = document.getElementById('topArtistsGrid');
    const topArtistsLoading = document.getElementById('topArtistsLoading');
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const sortSelect = document.getElementById('sortSelect');
    const resultsNumber = document.getElementById('resultsNumber');
    const clearFiltersBtn = document.getElementById('clearFilters');

    const AVATAR_PALETTE = [
        ['#e8d5b0', '#a8834a'],
        ['#d4e8d0', '#3a7a4a'],
        ['#d0dce8', '#3a5a7a'],
        ['#e8d0e0', '#7a3a5a'],
        ['#e0e8d0', '#5a7a3a'],
    ];

    function initialsAvatar(name, index) {
        const parts = name.trim().split(/[\s_]+/);
        const letter = (parts[0]?.[0] ?? '?').toUpperCase();
        const [bg, fg] = AVATAR_PALETTE[index % AVATAR_PALETTE.length];
        return `
            <div class="artist-card__avatar-placeholder w-100 h-100 rounded-circle d-flex align-items-center justify-content-center" style="background:${bg};">
                <span style="font-family:var(--font-ui);font-weight:700;font-size:1.05rem;color:${fg};">${letter}</span>
            </div>`;
    }

    const COVER_GRADIENTS = [
        'linear-gradient(135deg,#f0e6cc 0%,#ede8de 100%)',
        'linear-gradient(135deg,#dce8d8 0%,#e8f0e4 100%)',
        'linear-gradient(135deg,#d8dce8 0%,#e4e8f0 100%)',
        'linear-gradient(135deg,#e8d8e4 0%,#f0e4ec 100%)',
        'linear-gradient(135deg,#e8e4d8 0%,#f0ece4 100%)',
    ];

    // Build item directly embedded within Bootstrap grid columns
    function buildCard(artist, index, opts = {}) {
        const { isTopArtist = false, rank = null } = opts;

        const isOpen = artist.is_available == 1;
        const name = artist.username ?? 'Unknown';
        const price = artist.starting_rate ?? '0';
        const priceNum = parseFloat(price);

        const avatarHtml = artist.avatar_url
            ? `<img src="${BASE_URL}${artist.avatar_url}" class="rounded-circle w-100 h-100 object-fit-cover" alt="${name}">`
            : initialsAvatar(name, index);

        const coverStyle = COVER_GRADIENTS[index % COVER_GRADIENTS.length];

        const rankBadge = rank !== null
            ? `<span class="artist-card__rank position-absolute d-flex align-items-center justify-content-center rounded-circle fw-bold shadow-sm">#${rank}</span>`
            : '';

        const badgeClass = isOpen ? 'artist-card__badge--open' : 'artist-card__badge--closed';
        const badgeText = isOpen ? 'Open' : 'Closed';

        const priceDisplay = priceNum > 0
            ? `₱${priceNum.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
            : 'Free';

        // Direct Routing URL Query Configuration Parameter String targeting the Inbox module
        const chatInboxUrl = `${BASE_URL}messages?target_id=${artist.account_id}&name=${encodeURIComponent(name)}`;

        return `
<div class="col" data-id="${artist.artist_id}" data-price="${price}" data-open="${isOpen ? 1 : 0}">
    <div class="artist-card h-100 border rounded-3 position-relative d-flex flex-column shadow-sm overflow-visible bg-card">
        
        <div class="artist-card__cover position-relative w-100 d-flex align-items-end px-3" style="background:${coverStyle};">
            <div class="artist-card__avatar rounded-circle border p-0 position-absolute start-0 end-0 mx-auto shadow-sm bg-card d-flex align-items-center justify-content-center">${avatarHtml}</div>
            ${rankBadge}
            <button class="artist-card__wishlist position-absolute d-flex align-items-center justify-content-center rounded-circle border-0" aria-label="Add to wishlist" data-active="0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
        </div>

        <div class="artist-card__body p-2 p-sm-3 d-flex flex-column gap-1 flex-grow-1">
            <div class="artist-card__header d-flex align-items-start justify-content-between gap-1">
                <span class="artist-card__name text-truncate fw-bold text-capitalize">${name}</span>
                <span class="artist-card__rating d-inline-flex align-items-center gap-1 fw-bold text-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    4.8
                </span>
            </div>
            <p class="artist-card__tag m-0 text-muted small">Commission Artist</p>

            <div class="artist-card__meta d-flex align-items-center justify-content-between mt-auto pt-2 gap-1 flex-wrap">
                <span class="artist-card__badge d-inline-flex align-items-center fw-bold text-uppercase ${badgeClass}">${badgeText}</span>
                <span class="artist-card__starting fw-bold text-nowrap">from ${priceDisplay}</span>
            </div>
        </div>

        <div class="artist-card__actions d-flex p-2 p-sm-3 pt-0 gap-2 mt-auto align-items-center">
            <a href="${BASE_URL}profile?id=${artist.account_id}&role=artist" class="btn-artovia-outline text-center flex-grow-1 p-2 py-1.5 rounded-2">Profile</a>
            
            <a href="${chatInboxUrl}" class="btn-artovia-primary d-inline-flex align-items-center justify-content-center p-2 rounded-2" style="width: 40px; height: 38px;" title="Message ${name}">
                <i class="bi bi-chat-dots-fill"></i>
            </a>
        </div>

    </div>
</div>`;
    }

    function renderCards(container, artists, opts = {}) {
        container.innerHTML = artists
            .map((a, i) => buildCard(a, i, {
                isTopArtist: opts.isTopArtist ?? false,
                rank: opts.showRank ? i + 1 : null,
            }))
            .join('');
        attachWishlistListeners(container);
    }

    function attachWishlistListeners(container) {
        container.querySelectorAll('.artist-card__wishlist').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                const svg = btn.querySelector('svg');
                const active = btn.dataset.active === '1';
                if (active) {
                    svg.setAttribute('fill', 'none');
                    svg.style.color = '';
                    btn.dataset.active = '0';
                    btn.classList.remove('active');
                } else {
                    svg.setAttribute('fill', '#dc2626');
                    svg.style.color = '#dc2626';
                    btn.dataset.active = '1';
                    btn.classList.add('active');
                }
            });
        });
    }

    function sortArtists(artists, sortVal) {
        const list = [...artists];
        switch (sortVal) {
            case 'rating':
                return list.sort((a, b) => parseFloat(b.starting_rate ?? 0) - parseFloat(a.starting_rate ?? 0));
            case 'price_asc':
                return list.sort((a, b) => parseFloat(a.starting_rate ?? 0) - parseFloat(b.starting_rate ?? 0));
            case 'price_desc':
                return list.sort((a, b) => parseFloat(b.starting_rate ?? 0) - parseFloat(a.starting_rate ?? 0));
            case 'newest':
                return list.sort((a, b) => b.artist_id - a.artist_id);
            default:
                return list;
        }
    }

    function applyFilters() {
        const priceVal = document.querySelector('input[name="price"]:checked')?.value ?? '0-999999';
        const availabilityVal = document.querySelector('input[name="availability"]:checked')?.value ?? 'all';
        const searchVal = searchInput?.value.trim().toLowerCase() ?? '';
        const sortVal = sortSelect?.value ?? 'relevance';

        const [minPrice, maxPrice] = priceVal.split('-').map(Number);

        let filtered = allArtists.filter(a => {
            const price = parseFloat(a.starting_rate ?? 0);
            const matchesPrice = price >= minPrice && price <= maxPrice;
            const matchesAvail = availabilityVal === 'all' ||
                (availabilityVal === 'open' && a.is_available == 1);
            const matchesSearch = !searchVal ||
                a.username.toLowerCase().includes(searchVal);
            return matchesPrice && matchesAvail && matchesSearch;
        });

        filtered = sortArtists(filtered, sortVal);

        if (resultsNumber) resultsNumber.textContent = filtered.length;

        if (filtered.length === 0) {
            artistGrid.classList.add('d-none');
            artistGridEmpty.classList.remove('d-none');
        } else {
            artistGridEmpty.classList.add('d-none');
            artistGrid.classList.remove('d-none');
            renderCards(artistGrid, filtered);
        }
    }

    async function loadArtists() {
        try {
            const res = await fetch(`${BASE_URL}api/artists/fetch_all.php`);
            const data = await res.json();

            if (artistGridLoading) artistGridLoading.classList.add('d-none');

            if (!data.success || !data.data?.length) {
                artistGridEmpty.classList.remove('d-none');
                if (resultsNumber) resultsNumber.textContent = '0';
                return;
            }

            allArtists = data.data;
            if (resultsNumber) resultsNumber.textContent = allArtists.length;

            artistGrid.classList.remove('d-none');
            renderCards(artistGrid, allArtists);

            const top5 = [...allArtists]
                .sort((a, b) => parseFloat(b.starting_rate ?? 0) - parseFloat(a.starting_rate ?? 0))
                .slice(0, 5);

            if (topArtistsLoading) topArtistsLoading.classList.add('d-none');
            topArtistsGrid.classList.remove('d-none');
            renderCards(topArtistsGrid, top5, { isTopArtist: true, showRank: true });

            applyFilters();

        } catch (err) {
            if (artistGridLoading) artistGridLoading.classList.add('d-none');
            artistGridError.classList.remove('d-none');
            console.error('Failed to load artists:', err);
        }
    }

    document.querySelectorAll('input[name="price"], input[name="availability"]')
        .forEach(input => input.addEventListener('change', applyFilters));

    if (sortSelect) sortSelect.addEventListener('change', applyFilters);
    if (searchBtn) searchBtn.addEventListener('click', applyFilters);
    if (searchInput) searchInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') applyFilters();
    });

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            const priceAll = document.querySelector('input[name="price"][value="0-999999"]');
            const availAll = document.querySelector('input[name="availability"][value="all"]');
            if (priceAll) priceAll.checked = true;
            if (availAll) availAll.checked = true;
            if (searchInput) searchInput.value = '';
            if (sortSelect) sortSelect.value = 'relevance';
            document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
            document.querySelector('.category-chip[data-style="all"]')?.classList.add('active');
            activeStyle = 'all';
            applyFilters();
        });
    }

    document.querySelectorAll('.category-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeStyle = chip.dataset.style ?? 'all';
            applyFilters();
        });
    });

    loadArtists();
});