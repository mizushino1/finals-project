<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>

<main class="container-xl px-3 px-sm-4 py-4">

    <section class="browse-hero text-center py-5 my-2 position-relative">
        <p class="browse-hero__eyebrow text-uppercase">The Commission Board</p>
        <h1 class="browse-hero__title my-3">
            Find your next <em>creative project</em>
        </h1>
        <p class="browse-hero__sub mx-auto text-muted mb-4 col-md-8 col-lg-6">
            Browse open commission requests from clients looking for your unique style, or track your active postings.
        </p>

        <div class="search-bar mx-auto mb-4">
            <svg class="search-bar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="search-bar__input" id="searchInput" placeholder="Search by description, client name, or keywords…">
            <button class="search-bar__btn" id="searchBtn">Search</button>
        </div>
    </section>

    <section class="browse my-4">
        <div class="row g-4">
            
            <aside class="col-lg-3 col-xl-2.5">
                <div class="browse__sidebar sticky-top shadow-sm border rounded-3 p-3 bg-card" style="top: 90px; z-index: 100;">
                    <div class="sidebar__header d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                        <span class="sidebar__header-title text-uppercase fw-bold text-primary">Filters</span>
                        <button class="sidebar__clear-btn border-0 background-none p-0 fw-semibold" id="clearFilters">Clear all</button>
                    </div>

                    <div class="sidebar__content-wrapper d-flex flex-row flex-lg-column overflow-auto gap-3 gap-lg-0">
                        <div class="filter-group pb-lg-3 mb-lg-3 border-bottom-lg w-100 flex-shrink-0 flex-lg-shrink-1">
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2">Budget Range</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="budget" value="0-999999" checked> <span class="text-nowrap">Any Budget</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="budget" value="0-1000"> <span class="text-nowrap">Under ₱1,000</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="budget" value="1000-5000"> <span class="text-nowrap">₱1,000 – ₱5,000</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="budget" value="5000-999999"> <span class="text-nowrap">₱5,000+</span></label></li>
                            </ul>
                        </div>

                        <div class="filter-group w-100 flex-shrink-0 flex-lg-shrink-1">
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2">Project Status</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="status" value="all" checked> <span class="text-nowrap">All Projects</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="status" value="1"> <span class="text-nowrap">Active / Open</span></label></li>
                                <?php if ($_SESSION['role'] === 'user' || $_SESSION['role'] === 'admin'): ?>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="status" value="5"> <span class="text-nowrap">In Progress</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="status" value="6"> <span class="text-nowrap">Completed</span></label></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9 col-xl-9.5">
                
                <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <p class="browse-results-bar__count m-0 text-muted">
                        Showing <strong id="resultsNumber" class="text-primary fw-semibold">—</strong> commissions
                    </p>
                    <div class="browse-sort d-flex align-items-center gap-2">
                        <span class="text-muted text-nowrap">Sort by</span>
                        <select id="sortSelect" class="form-select form-select-sm border bg-card text-primary" aria-label="Sort commissions">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="budget_desc">Highest Budget</option>
                            <option value="budget_asc">Lowest Budget</option>
                        </select>
                    </div>
                </div>

                <div id="commissionGridLoading" class="row g-2 g-sm-3 row-cols-1 row-cols-md-2 row-cols-xl-3">
                    <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="col">
                        <div class="artist-card h-100 border rounded-3 p-3 bg-card d-flex flex-column shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="skeleton rounded-circle" style="width:36px; height:36px;"></div>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="skeleton rounded" style="width:80px; height:12px;"></div>
                                        <div class="skeleton rounded" style="width:50px; height:8px;"></div>
                                    </div>
                                </div>
                                <div class="skeleton rounded-pill" style="width:45px; height:18px;"></div>
                            </div>
                            <div class="flex-grow-1 mb-3 d-flex flex-column gap-2">
                                <div class="skeleton rounded w-100" style="height:10px;"></div>
                                <div class="skeleton rounded w-100" style="height:10px;"></div>
                                <div class="skeleton rounded w-75" style="height:10px;"></div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                <div class="skeleton rounded" style="width:60px; height:16px;"></div>
                                <div class="skeleton rounded-2" style="width:80px; height:32px;"></div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div id="commissionGridError" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <p class="grid-state__title fw-bold text-primary m-0 mb-1">Failed to load commissions</p>
                    <p class="grid-state__sub text-muted mx-auto mb-3">There was an issue communicating with the server.</p>
                    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">Retry</button>
                </div>

                <div id="commissionGridEmpty" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <p class="grid-state__title fw-bold text-primary m-0 mb-1">No commissions found</p>
                    <p class="grid-state__sub text-muted mx-auto m-0">Try adjusting your filters or search keywords.</p>
                </div>

                <div class="row g-2 g-sm-3 row-cols-1 row-cols-md-2 row-cols-xl-3 d-none" id="commissionGrid"></div>

            </div>
        </div>
    </section>
</main>

<script src="<?= BASE_URL ?>public/js/commissions.js"></script>