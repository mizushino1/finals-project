<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/constants.php';
?>

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/home.css">

<main>

    <!-- ══════════════════════════════════════
         HERO
    ══════════════════════════════════════ -->
    <section class="hero">
        <div class="hero__bg-img" aria-hidden="true"></div>
            <div class="container hero__content">
                <p class="hero__eyebrow">The creative commission marketplace</p>
                <h1 class="hero__title">
                    Design it.<br>
                    Create it.<br>
                    <em>Get Paid.</em>
                </h1>
                <p class="hero__sub">
                    Artovia connects talented artists with clients who need unique, custom artwork.
                    Post a commission, pick your artist, and bring your vision to life.
                </p>
                <div class="hero__actions">
                    <a href="<?= BASE_URL ?>commissions" class="btn-artovia-primary">
                        Browse Artists
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="<?= BASE_URL ?>login" class="btn-artovia-outline">
                            Get Started
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>commissions/create" class="btn-artovia-outline">
                            Post a Commission
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Feature highlights -->
                <div class="hero__features">
                    <div class="hero__feature">
                        <div class="hero__feature-icon">
                            <img src="<?= BASE_URL ?>public/img/connectIcon.svg" alt="" style="width:38px;height:38px;">
                        </div>
                        <div>
                            <p class="hero__feature-label">Design with Freedom</p>
                            <p class="hero__feature-desc">Create with no limits — make your ideas come to life.</p>
                        </div>
                    </div>
                    <div class="hero__feature">
                        <div class="hero__feature-icon">
                            <img src="<?= BASE_URL ?>public/img/peopleIcon.svg" alt="" style="width:38px;height:38px;">
                        </div>
                        <div>
                            <p class="hero__feature-label">Work and Collaborate</p>
                            <p class="hero__feature-desc">Connect with skilled artists worldwide.</p>
                        </div>
                    </div>
                    <div class="hero__feature">
                        <div class="hero__feature-icon">
                            <img src="<?= BASE_URL ?>public/img/walletIcon.svg" alt="" style="width:38px;height:38px;">
                        </div>
                        <div>
                            <p class="hero__feature-label">Get Paid Securely</p>
                            <p class="hero__feature-desc">Every transaction is safe and tracked.</p>
                        </div>
                    </div>
                </div>

                <!-- Stats pills -->
                <div class="hero__stats">
                    <div class="hero__stat">
                        <div class="hero__stat-icon">
                            <i class="bi bi-brush" style="color:var(--clr-gold-dark);font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="hero__stat-num">500+</div>
                            <div class="hero__stat-label">Active Artists</div>
                        </div>
                    </div>
                    <div class="hero__stat">
                        <div class="hero__stat-icon">
                            <i class="bi bi-check2-circle" style="color:var(--clr-gold-dark);font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="hero__stat-num">2,400+</div>
                            <div class="hero__stat-label">Commissions Done</div>
                        </div>
                    </div>
                    <div class="hero__stat">
                        <div class="hero__stat-icon">
                            <i class="bi bi-star" style="color:var(--clr-gold-dark);font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="hero__stat-num">98%</div>
                            <div class="hero__stat-label">Satisfaction Rate</div>
                        </div>
                    </div>
                    <div class="hero__stat">
                        <div class="hero__stat-icon">
                            <i class="bi bi-wallet2" style="color:var(--clr-gold-dark);font-size:1rem;"></i>
                        </div>
                        <div>
                            <div class="hero__stat-num">₱1.2M+</div>
                            <div class="hero__stat-label">Paid to Artists</div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- ══════════════════════════════════════
         FEATURES STRIP
    ══════════════════════════════════════ -->
    <div class="features-strip">
        <div class="container-fluid p-0">
            <div class="features-strip__grid">
                <div class="features-strip__item">
                    <div class="features-strip__icon">
                        <img src="<?= BASE_URL ?>public/img/connectIcon.svg" alt="" style="width:44px;height:44px;">
                    </div>
                    <div>
                        <p class="features-strip__label">Design with Freedom</p>
                        <p class="features-strip__desc">Create with no limits — make your ideas come to life with artists who match your vision.</p>
                    </div>
                </div>
                <div class="features-strip__item">
                    <div class="features-strip__icon">
                        <img src="<?= BASE_URL ?>public/img/peopleIcon.svg" alt="" style="width:44px;height:44px;">
                    </div>
                    <div>
                        <p class="features-strip__label">Work and Collaborate</p>
                        <p class="features-strip__desc">Connect with skilled and talented artists worldwide, built around your project needs.</p>
                    </div>
                </div>
                <div class="features-strip__item">
                    <div class="features-strip__icon">
                        <img src="<?= BASE_URL ?>public/img/walletIcon.svg" alt="" style="width:44px;height:44px;">
                    </div>
                    <div>
                        <p class="features-strip__label">Get Paid Securely</p>
                        <p class="features-strip__desc">We make sure creators get what they deserve — every transaction is safe and tracked.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         STATS BAND
    ══════════════════════════════════════ -->
    <div class="stats-band">
        <div class="container-fluid p-0">
            <div class="stats-band__grid">
                <div class="stats-band__item">
                    <div class="stats-band__num">500+</div>
                    <div class="stats-band__label">Active Artists</div>
                </div>
                <div class="stats-band__item">
                    <div class="stats-band__num">2,400+</div>
                    <div class="stats-band__label">Commissions Done</div>
                </div>
                <div class="stats-band__item">
                    <div class="stats-band__num">98%</div>
                    <div class="stats-band__label">Satisfaction Rate</div>
                </div>
                <div class="stats-band__item">
                    <div class="stats-band__num">₱1.2M+</div>
                    <div class="stats-band__label">Paid to Artists</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════
         HOW IT WORKS
    ══════════════════════════════════════ -->
    <section class="section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <p class="section__eyebrow">How it works</p>
                    <div class="gold-divider"></div>
                    <h2 class="section__title">From idea to finished artwork in four steps</h2>
                    <p class="section__body">
                        Artovia makes the commission process simple and transparent for both clients and artists.
                    </p>
                </div>
                <div class="col-lg-6 offset-lg-1">
                    <div class="steps">
                        <div class="step">
                            <div class="step__num">1</div>
                            <div>
                                <p class="step__title">Post your commission</p>
                                <p class="step__desc">Describe what you want, set your budget, and publish your request for artists to see.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step__num">2</div>
                            <div>
                                <p class="step__title">Artists send requests</p>
                                <p class="step__desc">Interested artists apply to take your commission with a message and their portfolio.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step__num">3</div>
                            <div>
                                <p class="step__title">Choose your artist</p>
                                <p class="step__desc">Review all applicants and accept the one that fits your vision best.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step__num">4</div>
                            <div>
                                <p class="step__title">Receive your artwork</p>
                                <p class="step__desc">Collaborate, review, and complete payment securely once you're happy with the result.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════
         BROWSE BY CATEGORY
    ══════════════════════════════════════ -->
    <section class="section section--alt">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section__eyebrow">Explore styles</p>
                <div class="gold-divider mx-auto"></div>
                <h2 class="section__title">Find the art style you're looking for</h2>
            </div>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <?php
                $categories = [
                    ['Anime',            'bi-stars'],
                    ['Chibi',            'bi-emoji-smile'],
                    ['Pixel Art',        'bi-grid-3x3'],
                    ['Watercolor',       'bi-droplet-half'],
                    ['Fantasy',          'bi-brilliance'],
                    ['Logo Design',      'bi-pentagon'],
                    ['Portrait',         'bi-person-bounding-box'],
                    ['Character Design', 'bi-brush'],
                ];
                foreach ($categories as [$name, $icon]): ?>
                    <a href="<?= BASE_URL ?>commissions?category=<?= urlencode($name) ?>" class="category-chip">
                        <span class="category-chip__dot"></span>
                        <i class="bi <?= $icon ?>"></i>
                        <?= $name ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════
         FEATURED ARTISTS
    ══════════════════════════════════════ -->
    <section class="section">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between mb-5 flex-wrap gap-3">
                <div>
                    <p class="section__eyebrow m-0">Featured Artists</p>
                    <div class="gold-divider"></div>
                    <h2 class="section__title mb-0">Top talent on Artovia</h2>
                </div>
                <a href="<?= BASE_URL ?>commissions" class="btn-artovia-outline">
                    View All Artists
                </a>
            </div>

            <div class="row g-2 g-sm-3 row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4" id="featuredArtistsGrid">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="col">
                        <div class="artist-card artist-card--skeleton h-100 border rounded-3 overflow-hidden d-flex flex-column bg-card position-relative shadow-sm" style="opacity: 0.65;">
                            <div class="artist-card__cover position-relative bg-surface" style="height: 110px;">
                                <div class="skeleton sk-avatar rounded-circle position-absolute start-0 end-0 mx-auto" style="bottom:-27px; width:54px; height:54px; background: var(--clr-bg-alt);"></div>
                            </div>
                            <div class="artist-card__body p-3 pt-4 flex-grow-1 d-flex flex-column gap-2" style="padding-top: 1.8rem !important;">
                                <div class="skeleton sk-line w-75 rounded" style="height:12px; background: var(--clr-bg-alt);"></div>
                                <div class="skeleton sk-line w-50 rounded" style="height:10px; background: var(--clr-bg-alt);"></div>
                                <div class="skeleton sk-line w-100 rounded my-1" style="height:10px; background: var(--clr-bg-alt);"></div>
                            </div>
                            <div class="artist-card__actions d-flex p-3 pt-0 gap-2 mt-auto">
                                <div class="skeleton flex-grow-1 rounded-2" style="height:32px; background: var(--clr-bg-alt);"></div>
                                <div class="skeleton flex-grow-1 rounded-2" style="height:32px; background: var(--clr-bg-alt);"></div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════
         TESTIMONIALS
    ══════════════════════════════════════ -->
    <section class="section section--alt">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section__eyebrow">What people say</p>
                <div class="gold-divider mx-auto"></div>
                <h2 class="section__title">Loved by artists and clients alike</h2>
            </div>
            <div class="row g-4">
                <?php
                $testimonials = [
                    [
                        'text'     => 'I posted my first commission and had 5 artists apply within a day. The quality of work blew me away — I\'ve already posted three more.',
                        'name'     => 'Sofia L.',
                        'role'     => 'Commission Client',
                        'initials' => 'SL',
                    ],
                    [
                        'text'     => 'As an artist, Artovia is the best platform I\'ve used. Clients come to me with clear briefs and payment is always on time. Highly recommend.',
                        'name'     => 'Jay-R U.',
                        'role'     => 'Freelance Artist',
                        'initials' => 'JU',
                    ],
                    [
                        'text'     => 'I needed a logo for my small business and found a perfect match in under 24 hours. The whole process felt professional and stress-free.',
                        'name'     => 'Carlos R.',
                        'role'     => 'Small Business Owner',
                        'initials' => 'CR',
                    ],
                ];
                foreach ($testimonials as $t): ?>
                    <div class="col-md-4">
                        <div class="testimonial">
                            <p class="testimonial__text"><?= $t['text'] ?></p>
                            <div class="testimonial__author">
                                <div class="testimonial__avatar"><?= $t['initials'] ?></div>
                                <div>
                                    <p class="testimonial__name"><?= $t['name'] ?></p>
                                    <p class="testimonial__role"><?= $t['role'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════
         CTA
    ══════════════════════════════════════ -->
    <section class="cta-section">
        <div class="container position-relative">
            <h2 class="cta-section__title">Your next great artwork<br>starts here.</h2>
            <p class="cta-section__sub">Join thousands of artists and clients already creating on Artovia.</p>
            <div class="cta-section__actions">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="<?= BASE_URL ?>login" class="btn-artovia-light">
                        Create an Account
                    </a>
                    <a href="<?= BASE_URL ?>commissions" class="btn-artovia-ghost">
                        Browse Artists
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>commissions/create" class="btn-artovia-light">
                        Post a Commission
                    </a>
                    <a href="<?= BASE_URL ?>commissions" class="btn-artovia-ghost">
                        Browse Artists
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<script src="<?= BASE_URL ?>public/js/home.js"></script>