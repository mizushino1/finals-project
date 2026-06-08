<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/browse.css">

<main class="container-xl px-3 px-sm-4 py-4">

    <section class="browse-hero text-center py-5 my-2 position-relative">
        <p class="browse-hero__eyebrow text-uppercase">Discover Remarkable Commission Artists</p>
        <h1 class="browse-hero__title my-3">
            Find the perfect <em>art style</em><br>for your next commission
        </h1>
        <p class="browse-hero__sub mx-auto text-muted mb-4 col-md-8 col-lg-6">
            Browse hundreds of talented artists — filter by style, price, and availability.
        </p>

        <div class="search-bar mx-auto mb-4">
            <svg class="search-bar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="search-bar__input" id="searchInput" placeholder="Search by name, style, or keyword…">
            <button class="search-bar__btn" id="searchBtn">Search</button>
        </div>

        <div class="browse-hero__chips d-flex flex-wrap justify-content-center gap-2">
            <button class="category-chip active" data-style="all"><span class="category-chip__dot"></span> All Styles</button>
            <button class="category-chip" data-style="digital"><span class="category-chip__dot"></span> Digital Art</button>
            <button class="category-chip" data-style="traditional"><span class="category-chip__dot"></span> Traditional</button>
            <button class="category-chip" data-style="chibi"><span class="category-chip__dot"></span> Chibi</button>
            <button class="category-chip" data-style="anime"><span class="category-chip__dot"></span> Anime</button>
            <button class="category-chip" data-style="portrait"><span class="category-chip__dot"></span> Portrait</button>
            <button class="category-chip" data-style="fanart"><span class="category-chip__dot"></span> Fan Art</button>
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
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2">Price Range</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="price" value="0-999999" checked> <span class="text-nowrap">All Prices</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="price" value="0-300"> <span class="text-nowrap">₱50 – ₱300</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="price" value="300-700"> <span class="text-nowrap">₱300 – ₱700</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="price" value="700-1500"> <span class="text-nowrap">₱700 – ₱1,500</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="price" value="1500-999999"> <span class="text-nowrap">₱1,500+</span></label></li>
                            </ul>
                        </div>

                        <div class="filter-group w-100 flex-shrink-0 flex-lg-shrink-1">
                            <h3 class="filter-group__title text-uppercase fw-bold text-muted mb-2">Availability</h3>
                            <ul class="filter-group__list list-unstyled m-0 p-0 d-flex flex-row flex-lg-column gap-1">
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="availability" value="all" checked> <span class="text-nowrap">All Artists</span></label></li>
                                <li><label class="rounded p-2 w-100 d-flex align-items-center gap-2"><input type="radio" name="availability" value="open"> <span class="text-nowrap">Open Only</span></label></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="col-lg-9 col-xl-9.5">
                
                <div class="browse-results-bar d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                    <p class="browse-results-bar__count m-0 text-muted">
                        Showing <strong id="resultsNumber" class="text-primary fw-semibold">—</strong> artists
                    </p>
                    <div class="browse-sort d-flex align-items-center gap-2">
                        <span class="text-muted text-nowrap">Sort by</span>
                        <select id="sortSelect" class="form-select form-select-sm border" aria-label="Sort artists">
                            <option value="relevance">Relevance</option>
                            <option value="rating">Highest Rated</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="newest">Newest</option>
                        </select>
                    </div>
                </div>

                <div id="artistGridLoading" class="row g-2 g-sm-3 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                    <div class="col">
                        <div class="artist-card artist-card--skeleton h-100 border rounded-3 overflow-hidden d-flex flex-column">
                            <div class="artist-card__cover position-relative bg-surface" style="height: 125px;">
                                <div class="skeleton sk-avatar rounded-circle position-absolute start-0 end-0 mx-auto" style="bottom:-24px; width:48px; height:48px;"></div>
                            </div>
                            <div class="artist-card__body p-3 pt-4 flex-grow-1 d-flex flex-direction-column gap-2">
                                <div class="skeleton sk-line sk-line--med w-75" style="height:12px;"></div>
                                <div class="skeleton sk-line sk-line--short w-50" style="height:10px;"></div>
                                <div class="skeleton sk-line sk-line--full w-100 my-1" style="height:10px;"></div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div id="artistGridError" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <div class="grid-state__icon d-inline-flex align-items-center justify-content-center bg-surface rounded-circle mb-3" style="width:56px; height:56px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:28px; height:28px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    </div>
                    <p class="grid-state__title fw-bold text-primary m-0 mb-1">Something went wrong</p>
                    <p class="grid-state__sub text-muted mx-auto mb-3" style="max-width:320px;">We couldn't load the artist list. Please refresh and try again.</p>
                    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">Retry</button>
                </div>

                <div id="artistGridEmpty" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                    <div class="grid-state__icon d-inline-flex align-items-center justify-content-center bg-surface rounded-circle mb-3" style="width:56px; height:56px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="width:28px; height:28px;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                    </div>
                    <p class="grid-state__title fw-bold text-primary m-0 mb-1">No artists found</p>
                    <p class="grid-state__sub text-muted mx-auto m-0" style="max-width:320px;">Try adjusting your filters or search with different keywords.</p>
                </div>

                <div class="row g-2 g-sm-3 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 d-none" id="artistGrid"></div>

            </div>
        </div>
    </section>

    <div class="w-100 my-5"><hr class="text-muted opacity-25"></div>

    <section class="top-artists py-2">
        <div class="top-artists__header d-flex align-items-end justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <p class="top-artists__label text-uppercase fw-bold m-0 mb-1">Community Picks</p>
                <h2 class="top-artists__title fw-normal position-relative m-0">Top Artists</h2>
            </div>
            <a href="#" class="top-artists__view-all d-inline-flex align-items-center gap-1 text-decoration-none fw-semibold">
                View all
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px; height:14px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div id="topArtistsLoading" class="row g-2 g-sm-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5">
            <?php for ($i = 0; $i < 5; $i++): ?>
            <div class="col">
                <div class="artist-card artist-card--skeleton border rounded-3 overflow-hidden d-flex flex-column h-100">
                    <div class="artist-card__cover position-relative bg-surface" style="height: 125px;">
                        <div class="skeleton sk-avatar rounded-circle position-absolute start-0 end-0 mx-auto" style="bottom:-24px; width:48px; height:48px;"></div>
                    </div>
                    <div class="artist-card__body p-3 pt-4 flex-grow-1">
                        <div class="skeleton sk-line sk-line--med w-75 mb-2" style="height:12px;"></div>
                        <div class="skeleton sk-line sk-line--short w-50" style="height:10px;"></div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <div class="row g-2 g-sm-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 d-none" id="topArtistsGrid"></div>
    </section>

</main>

<script src="<?= BASE_URL ?>public/js/browse.js"></script>