/* ══════════════════════════════════════════════════════════════
   commissions.js — Artovia Commission Job Board
══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    let allCommissions = [];

    const grid        = document.getElementById('commissionGrid');
    const gridLoading = document.getElementById('commissionGridLoading');
    const gridError   = document.getElementById('commissionGridError');
    const gridEmpty   = document.getElementById('commissionGridEmpty');
    const searchInput = document.getElementById('searchInput');
    const searchBtn   = document.getElementById('searchBtn');
    const sortSelect  = document.getElementById('sortSelect');
    const resultsNum  = document.getElementById('resultsNumber');
    const clearBtn    = document.getElementById('clearFilters');

    // Avatar palette for missing client images
    const PALETTE = [
        ['#e8d5b0','#a8834a'], ['#d4e8d0','#3a7a4a'],
        ['#d0dce8','#3a5a7a'], ['#e8d0e0','#7a3a5a']
    ];

    function getStatusConfig(statusId) {
        switch (parseInt(statusId)) {
            case 1: return { text: 'Open',        class: 'artist-card__badge--open' };
            case 2: return { text: 'Pending',     class: 'bg-warning text-dark border border-warning' };
            case 3: return { text: 'Accepted',    class: 'bg-info text-dark border border-info' };
            case 4: return { text: 'Rejected',    class: 'artist-card__badge--closed' };
            case 5: return { text: 'In Progress', class: 'bg-primary text-white border border-primary' };
            case 6: return { text: 'Completed',   class: 'bg-success text-white border border-success' };
            case 7: return { text: 'Cancelled',   class: 'bg-secondary text-white border border-secondary' };
            default: return { text: 'Unknown',    class: 'bg-dark text-white' };
        }
    }

    function buildCard(c, index) {
        const clientName = c.posted_by ?? 'Anonymous Client';
        const budget     = parseFloat(c.price ?? 0);
        const letter     = clientName.trim()[0].toUpperCase();
        const [bg, fg]   = PALETTE[index % PALETTE.length];
        
        const avatarHtml = `
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px; height:38px; background:${bg}; border: 1px solid ${fg}33;">
                <span style="font-family:var(--font-ui); font-weight:700; font-size:1rem; color:${fg};">${letter}</span>
            </div>`;

        const budgetDisplay = budget > 0 
            ? `₱${budget.toLocaleString('en-PH', { minimumFractionDigits: 2 })}` 
            : 'Open Budget';

        const dateObj = new Date(c.commission_date);
        const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        const status = getStatusConfig(c.status_id);

        // Dynamic action buttons based on global USER_ROLE injected in PHP
        let actionBtn = '';
        if (USER_ROLE === 'artist' && parseInt(c.status_id) === 1) {
            actionBtn = `<a href="${BASE_URL}commissions/proposal?id=${c.commission_id}" class="btn-artovia-primary py-1 px-3 fs-fluid-xs rounded-2">Apply</a>`;
        } else if (USER_ROLE === 'user' || USER_ROLE === 'client') {
            actionBtn = `<a href="${BASE_URL}commissions/manage?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2">Manage</a>`;
        } else {
            actionBtn = `<a href="${BASE_URL}commissions/view?id=${c.commission_id}" class="btn-artovia-outline py-1 px-3 fs-fluid-xs rounded-2 border-color-subtle">View</a>`;
        }

        return `
            <div class="col">
                <div class="artist-card h-100 border rounded-3 d-flex flex-column shadow-sm bg-card p-3">
                    
                    <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            ${avatarHtml}
                            <div class="text-truncate">
                                <p class="m-0 fw-bold text-primary fs-fluid-sm text-truncate lh-1">${clientName}</p>
                                <small class="text-muted" style="font-size: 0.72rem;">${dateStr}</small>
                            </div>
                        </div>
                        <span class="artist-card__badge d-inline-flex flex-shrink-0 align-items-center fw-bold text-uppercase ${status.class}">
                            ${status.text}
                        </span>
                    </div>

                    <div class="flex-grow-1 mb-4">
                        <p class="text-muted small m-0" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.6;">
                            ${c.description}
                        </p>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                        <div>
                            <p class="m-0 text-muted" style="font-size: 0.65rem; letter-spacing: 0.05em; text-transform: uppercase;">Budget</p>
                            <p class="m-0 fw-bold text-primary fs-fluid-sm">${budgetDisplay}</p>
                        </div>
                        ${actionBtn}
                    </div>

                </div>
            </div>`;
    }

    function renderCards(container, data) {
        container.innerHTML = data.map((c, i) => buildCard(c, i)).join('');
    }

    function sortData(data, sortVal) {
        const list = [...data];
        switch (sortVal) {
            case 'newest':      return list.sort((a, b) => new Date(b.commission_date) - new Date(a.commission_date));
            case 'oldest':      return list.sort((a, b) => new Date(a.commission_date) - new Date(b.commission_date));
            case 'budget_desc': return list.sort((a, b) => parseFloat(b.price ?? 0) - parseFloat(a.price ?? 0));
            case 'budget_asc':  return list.sort((a, b) => parseFloat(a.price ?? 0) - parseFloat(b.price ?? 0));
            default:            return list;
        }
    }

    function applyFilters() {
        const budgetVal = document.querySelector('input[name="budget"]:checked')?.value ?? '0-999999';
        const statusVal = document.querySelector('input[name="status"]:checked')?.value ?? 'all';
        const searchVal = searchInput?.value.trim().toLowerCase() ?? '';
        const sortVal   = sortSelect?.value ?? 'newest';

        const [minPrice, maxPrice] = budgetVal.split('-').map(Number);

        let filtered = allCommissions.filter(c => {
            const price = parseFloat(c.price ?? 0);
            const matchesBudget = price >= minPrice && price <= maxPrice;
            const matchesStatus = statusVal === 'all' || parseInt(c.status_id) === parseInt(statusVal);
            
            const clientName = (c.posted_by || '').toLowerCase();
            const desc = (c.description || '').toLowerCase();
            const matchesSearch = !searchVal || clientName.includes(searchVal) || desc.includes(searchVal);
            
            return matchesBudget && matchesStatus && matchesSearch;
        });

        filtered = sortData(filtered, sortVal);

        if (resultsNum) resultsNum.textContent = filtered.length;

        if (filtered.length === 0) {
            grid.classList.add('d-none');
            gridEmpty.classList.remove('d-none');
        } else {
            gridEmpty.classList.add('d-none');
            grid.classList.remove('d-none');
            renderCards(grid, filtered);
        }
    }

    async function loadCommissions() {
        try {
            const res  = await fetch(`${BASE_URL}api/commissions/fetch.php`);
            const data = await res.json();

            if (gridLoading) gridLoading.classList.add('d-none');

            if (!data.success || !data.data?.length) {
                gridEmpty.classList.remove('d-none');
                if (resultsNum) resultsNum.textContent = '0';
                return;
            }

            allCommissions = data.data;
            applyFilters(); // Initial render + counts via filters
            
        } catch (err) {
            if (gridLoading) gridLoading.classList.add('d-none');
            gridError.classList.remove('d-none');
        }
    }

    // Attach Listeners
    document.querySelectorAll('input[type="radio"]').forEach(input => input.addEventListener('change', applyFilters));
    if (sortSelect)  sortSelect.addEventListener('change', applyFilters);
    if (searchBtn)   searchBtn.addEventListener('click', applyFilters);
    if (searchInput) searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') applyFilters(); });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            document.querySelector('input[name="budget"][value="0-999999"]').checked = true;
            document.querySelector('input[name="status"][value="all"]').checked = true;
            if (searchInput) searchInput.value = '';
            if (sortSelect) sortSelect.value = 'newest';
            applyFilters();
        });
    }

    loadCommissions();
});