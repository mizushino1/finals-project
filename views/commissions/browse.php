<?php require_once __DIR__ . '/../../src/middleware/auth_middleware.php'; ?>
<link rel="stylesheet" href="browse.css">

<main>

    <!-- HERO SECTION -->
    <section class="hero">
        <p class="hero__eyebrow">Discover Remarkable Commission Artists</p>
        <h1 class="hero__title">Find the perfect art style<br>for your next commission</h1>

        <!-- SEARCH BAR -->
        <div class="search-bar">
            <svg class="search-bar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="search-bar__input" placeholder="Search Artist...">
            <button class="search-bar__btn">Search</button>
        </div>
    </section>

    <!-- BROWSE SECTION -->
    <section class="browse">
        <div class="browse__sidebar">

            <!-- CATEGORIES -->
            <div class="filter-group">
                <h3 class="filter-group__title">Categories</h3>
                <ul class="filter-group__list">
                    <li><label><input type="checkbox" checked> Anime</label></li>
                    <li><label><input type="checkbox"> Realism</label></li>
                    <li><label><input type="checkbox"> Chibi</label></li>
                    <li><label><input type="checkbox"> Fantasy</label></li>
                    <li><label><input type="checkbox"> Portrait</label></li>
                    <li><label><input type="checkbox"> Abstract</label></li>
                </ul>
            </div>

            <!-- PRICE RANGE -->
            <div class="filter-group">
                <h3 class="filter-group__title">Price Range</h3>
                <ul class="filter-group__list">
                    <li><label><input type="radio" name="price" checked> ₱50 – ₱300</label></li>
                    <li><label><input type="radio" name="price"> ₱300 – ₱700</label></li>
                    <li><label><input type="radio" name="price"> ₱700 – ₱1,500</label></li>
                    <li><label><input type="radio" name="price"> ₱1,500+</label></li>
                </ul>
            </div>

            <!-- RATINGS -->
            <div class="filter-group">
                <h3 class="filter-group__title">Ratings</h3>
                <ul class="filter-group__list">
                    <li><label><input type="checkbox"> ★ 4.9</label></li>
                    <li><label><input type="checkbox"> ★ 4.8</label></li>
                    <li><label><input type="checkbox"> ★ 4.7</label></li>
                    <li><label><input type="checkbox"> ★ 4.5+</label></li>
                </ul>
            </div>

        </div>

        <!-- ARTIST GRID -->
        <div class="browse__content">
            <div class="artist-grid" id="artistGrid">

                <?php
                $artists = [
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'750','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'closed'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                    ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                ];
                foreach ($artists as $artist): ?>
                <div class="artist-card">
                    <button class="artist-card__wishlist" aria-label="Add to wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>

                    <div class="artist-card__avatar">
                        <div class="artist-card__avatar-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                        </div>
                    </div>

                    <div class="artist-card__body">
                        <div class="artist-card__header">
                            <span class="artist-card__name"><?= htmlspecialchars($artist['name']) ?></span>
                            <span class="artist-card__rating">★ <?= htmlspecialchars($artist['rating']) ?></span>
                        </div>
                        <p class="artist-card__starting">Start at ₱<?= htmlspecialchars($artist['price']) ?></p>
                        <p class="artist-card__desc"><?= htmlspecialchars($artist['desc']) ?></p>
                        <span class="artist-card__status artist-card__status--<?= $artist['status'] === 'open' ? 'open' : 'closed' ?>">
                            <?= $artist['status'] === 'open' ? '● Open commission' : '● Closed commission' ?>
                        </span>
                    </div>

                    <div class="artist-card__actions">
                        <a href="#" class="btn btn--ghost btn--sm">View Profile</a>
                        <a href="#" class="btn btn--accent btn--sm">Hire Artist</a>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <!-- TOP ARTISTS SECTION -->
    <section class="top-artists">
        <h2 class="top-artists__title">TOP ARTIST</h2>
        <div class="top-artists__grid">

            <?php
            $top = [
                ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
                ['name'=>'Benten','rating'=>'4.9','price'=>'800','desc'=>'Best artist bababababbb hahahahahha hahahahahhh','status'=>'open'],
            ];
            foreach ($top as $artist): ?>
            <div class="artist-card">
                <button class="artist-card__wishlist" aria-label="Add to wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>

                <div class="artist-card__avatar">
                    <div class="artist-card__avatar-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                    </div>
                </div>

                <div class="artist-card__body">
                    <div class="artist-card__header">
                        <span class="artist-card__name"><?= htmlspecialchars($artist['name']) ?></span>
                        <span class="artist-card__rating">★ <?= htmlspecialchars($artist['rating']) ?></span>
                    </div>
                    <p class="artist-card__starting">Start at ₱<?= htmlspecialchars($artist['price']) ?></p>
                    <p class="artist-card__desc"><?= htmlspecialchars($artist['desc']) ?></p>
                    <span class="artist-card__status artist-card__status--open">● Open commission</span>
                </div>

                <div class="artist-card__actions">
                    <a href="#" class="btn btn--ghost btn--sm">View Profile</a>
                    <a href="#" class="btn btn--accent btn--sm">Hire Artist</a>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </section>

</main>

<script src="/js/browse.js"></script>