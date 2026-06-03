/* browse.js — Artovia Browse Page */

document.addEventListener('DOMContentLoaded', () => {

    // ── State ──
    let allArtists = [];

    // ── Elements ──
    const artistGrid        = document.getElementById('artistGrid');
    const artistGridLoading = document.getElementById('artistGridLoading');
    const artistGridError   = document.getElementById('artistGridError');
    const artistGridEmpty   = document.getElementById('artistGridEmpty');
    const topArtistsGrid    = document.getElementById('topArtistsGrid');
    const topArtistsLoading = document.getElementById('topArtistsLoading');
    const searchInput       = document.getElementById('searchInput');
    const searchBtn         = document.getElementById('searchBtn');

    // ── Build a single artist card HTML ──
    function buildCard(artist) {
        const isOpen   = artist.account_status === 'active';
        const name     = artist.username      ?? 'Unknown';
        const price    = artist.start_at      ?? '0';
        const statusClass = isOpen ? 'open' : 'closed';
        const statusText  = isOpen ? '● Open commission' : '● Closed commission';

        return `
        <div class="artist-card" data-id="${artist.artist_id}" data-price="${price}" data-open="${isOpen ? 1 : 0}">
            <button class="artist-card__wishlist" aria-label="Add to wishlist" data-active="0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            <div class="artist-card__avatar">
                <div class="artist-card__avatar-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
            </div>
            <div class="artist-card__body">
                <div class="artist-card__header">
                    <span class="artist-card__name">${name}</span>
                </div>
                <p class="artist-card__starting">Start at ₱${parseFloat(price).toLocaleString()}</p>
                <span class="artist-card__status artist-card__status--${statusClass}">${statusText}</span>
            </div>
            <div class="artist-card__actions">
                <a href="${BASE_URL}profile?id=${artist.artist_id}" class="btn btn--ghost btn--sm">View Profile</a>
                <a href="${BASE_URL}commissions/create?artist=${artist.artist_id}" class="btn btn--accent btn--sm">Hire Artist</a>
            </div>
        </div>`;
    }

    // ── Render cards into a container ──
    function renderCards(container, artists) {
        container.innerHTML = artists.map(buildCard).join('');
        attachWishlistListeners(container);
    }

    // ── Wishlist toggle ──
    function attachWishlistListeners(container) {
        container.querySelectorAll('.artist-card__wishlist').forEach(btn => {
            btn.addEventListener('click', () => {
                const svg    = btn.querySelector('svg');
                const active = btn.dataset.active === '1';
                if (active) {
                    svg.setAttribute('fill', 'none');
                    svg.style.color  = '';
                    btn.dataset.active = '0';
                } else {
                    svg.setAttribute('fill', 'currentColor');
                    svg.style.color  = '#c9873a';
                    btn.dataset.active = '1';
                }
            });
        });
    }

    // ── Apply filters and search to allArtists ──
    function applyFilters() {
        const priceVal        = document.querySelector('input[name="price"]:checked')?.value ?? '0-99999';
        const availabilityVal = document.querySelector('input[name="availability"]:checked')?.value ?? 'all';
        const searchVal       = searchInput?.value.trim().toLowerCase() ?? '';

        const [minPrice, maxPrice] = priceVal.split('-').map(Number);

        const filtered = allArtists.filter(a => {
            const price    = parseFloat(a.start_at ?? 0);
            const matchesPrice = price >= minPrice && price <= maxPrice;
            const matchesAvail = availabilityVal === 'all' || (availabilityVal === 'open' && a.account_status === 'active');
            const matchesSearch = !searchVal || a.username.toLowerCase().includes(searchVal);
            return matchesPrice && matchesAvail && matchesSearch;
        });

        if (filtered.length === 0) {
            artistGrid.classList.add('d-none');
            artistGridEmpty.classList.remove('d-none');
        } else {
            artistGridEmpty.classList.add('d-none');
            artistGrid.classList.remove('d-none');
            renderCards(artistGrid, filtered);
        }
    }

    // ── Fetch all artists from profile API ──
    async function loadArtists() {
        try {
            const res  = await fetch(`${BASE_URL}api/artists/fetch_all.php`);
            const data = await res.json();

            artistGridLoading.classList.add('d-none');

            if (!data.success || !data.data.length) {
                artistGridEmpty.classList.remove('d-none');
                return;
            }

            allArtists = data.data;
            artistGrid.classList.remove('d-none');
            renderCards(artistGrid, allArtists);

            // Top artists = highest start_at (top 4)
            const top4 = [...allArtists]
                .sort((a, b) => parseFloat(b.start_at) - parseFloat(a.start_at))
                .slice(0, 4);

            topArtistsLoading.classList.add('d-none');
            topArtistsGrid.classList.remove('d-none');
            renderCards(topArtistsGrid, top4);

        } catch (err) {
            artistGridLoading.classList.add('d-none');
            artistGridError.classList.remove('d-none');
            console.error('Failed to load artists:', err);
        }
    }

    // ── Filter listeners ──
    document.querySelectorAll('input[name="price"], input[name="availability"]').forEach(input => {
        input.addEventListener('change', applyFilters);
    });

    // ── Search listeners ──
    if (searchBtn)   searchBtn.addEventListener('click', applyFilters);
    if (searchInput) searchInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') applyFilters();
    });

    // ── Init ──
    loadArtists();

});