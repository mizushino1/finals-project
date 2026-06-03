<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/browse.css">

<main>

    <!-- HERO SECTION -->
    <section class="hero">
        <p class="hero__eyebrow">Discover Remarkable Commission Artists</p>
        <h1 class="hero__title">Find the perfect art style<br>for your next commission</h1>
        <div class="search-bar">
            <svg class="search-bar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="search-bar__input" id="searchInput" placeholder="Search Artist...">
            <button class="search-bar__btn" id="searchBtn">Search</button>
        </div>
    </section>

    <!-- BROWSE SECTION -->
    <section class="browse">
        <div class="browse__sidebar">

            <div class="filter-group">
                <h3 class="filter-group__title">Price Range</h3>
                <ul class="filter-group__list">
                    <li><label><input type="radio" name="price" value="0-300" checked> ₱50 – ₱300</label></li>
                    <li><label><input type="radio" name="price" value="300-700"> ₱300 – ₱700</label></li>
                    <li><label><input type="radio" name="price" value="700-1500"> ₱700 – ₱1,500</label></li>
                    <li><label><input type="radio" name="price" value="1500-99999"> ₱1,500+</label></li>
                </ul>
            </div>

            <div class="filter-group">
                <h3 class="filter-group__title">Availability</h3>
                <ul class="filter-group__list">
                    <li><label><input type="radio" name="availability" value="all" checked> All</label></li>
                    <li><label><input type="radio" name="availability" value="open"> Open only</label></li>
                </ul>
            </div>

        </div>

        <!-- ARTIST GRID — filled by browse.js -->
        <div class="browse__content">

            <!-- Loading state -->
            <div id="artistGridLoading" class="grid-loading">
                <p>Loading artists...</p>
            </div>

            <!-- Error state -->
            <div id="artistGridError" class="grid-error d-none">
                <p>Failed to load artists. Please try again.</p>
            </div>

            <!-- Empty state -->
            <div id="artistGridEmpty" class="grid-empty d-none">
                <p>No artists found.</p>
            </div>

            <!-- Grid -->
            <div class="artist-grid d-none" id="artistGrid"></div>

        </div>
    </section>

    <!-- TOP ARTISTS SECTION -->
    <section class="top-artists">
        <h2 class="top-artists__title">TOP ARTIST</h2>

        <div id="topArtistsLoading" class="grid-loading">
            <p>Loading top artists...</p>
        </div>

        <div class="top-artists__grid d-none" id="topArtistsGrid"></div>
    </section>

</main>

<script src="<?= BASE_URL ?>public/js/browse.js"></script>