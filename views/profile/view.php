<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/constants.php';
?>

<section class="profile-header">
    <div class="container">

        <div class="d-flex align-items-start gap-3 flex-wrap">

            <!-- Avatar -->
            <img
                src="assets/img/default-avatar.png"
                alt="User avatar"
                class="profile-avatar"
            >

            <!-- Name / bio -->
            <div class="flex-grow-1 pt-1">
                <h1 class="profile-username mb-1">USERNAME</h1>
                <p class="profile-bio">hello I am Jay-R Umandap</p>
            </div>

            <!-- Search -->
            <div class="profile-search-wrapper ms-auto mt-1">
                <i class="fas fa-search search-icon"></i>
                <input
                    type="search"
                    class="profile-search-input"
                    placeholder="Search"
                    aria-label="Search profile"
                >
            </div>

        </div>

        <!-- Stats + action row -->
        <div class="d-flex align-items-center flex-wrap profile-stats-row mt-3">

            <!-- Followers -->
            <div class="profile-stat">
                <span class="profile-stat-value">1.14M</span>
                <span class="profile-stat-label">Followers</span>
            </div>

            <!-- Following -->
            <div class="profile-stat">
                <span class="profile-stat-value">100K</span>
                <span class="profile-stat-label">Following</span>
            </div>

            <!-- Likes -->
            <div class="profile-stat">
                <span class="profile-stat-value">5.14M</span>
                <span class="profile-stat-label">Likes</span>
            </div>

            <!-- Reviews -->
            <div class="profile-stat">
                <span class="profile-stat-value profile-reviews-badge">
                    <i class="fas fa-star"></i> 4.5/5
                </span>
                <span class="profile-stat-label">Reviews</span>
            </div>

            <!-- Actions -->
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button
                        class="btn btn-follow"
                        type="button"
                        id="btn-follow-action"
                        data-following="0"
                    >
                        <i class="fas fa-plus me-1"></i> Follow
                    </button>
                    <button class="btn btn-notify" type="button" aria-label="Notifications">
                        <i class="fas fa-bell"></i>
                    </button>
                <?php else: ?>
                    <button
                        class="btn btn-follow"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#loginPromptModal"
                    >
                        <i class="fas fa-plus me-1"></i> Follow
                    </button>
                    <button
                        class="btn btn-notify"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#loginPromptModal"
                        aria-label="Notifications"
                    >
                        <i class="fas fa-bell"></i>
                    </button>
                <?php endif; ?>
            </div>

        </div>

        <!-- Tab navigation -->
        <ul class="nav profile-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active"
                    id="tab-artworks"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-artworks"
                    type="button"
                    role="tab"
                    aria-controls="pane-artworks"
                    aria-selected="true"
                >Artworks</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="tab-collaborations"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-collaborations"
                    type="button"
                    role="tab"
                    aria-controls="pane-collaborations"
                    aria-selected="false"
                >Collaborations</button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="tab-showcase"
                    data-bs-toggle="tab"
                    data-bs-target="#pane-showcase"
                    type="button"
                    role="tab"
                    aria-controls="pane-showcase"
                    aria-selected="false"
                >Showcase</button>
            </li>
        </ul>

    </div>
</section>

<main class="py-4">
    <div class="container">
        <div class="tab-content" id="profileTabContent">

            <!-- Artworks -->
            <div
                class="tab-pane fade show active"
                id="pane-artworks"
                role="tabpanel"
                aria-labelledby="tab-artworks"
            >
                <p class="theme-font-color">Artworks content here.</p>
            </div>

            <!-- Collaborations -->
            <div
                class="tab-pane fade"
                id="pane-collaborations"
                role="tabpanel"
                aria-labelledby="tab-collaborations"
            >
                <p class="theme-font-color">Collaborations content here.</p>
            </div>

            <!-- Showcase -->
            <div
                class="tab-pane fade"
                id="pane-showcase"
                role="tabpanel"
                aria-labelledby="tab-showcase"
            >
                <p class="theme-font-color">Showcase content here.</p>
            </div>

        </div>
    </div>
</main>

<!-- Login prompt modal — guests only -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="modal fade" id="loginPromptModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Login Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You need to be logged in to follow or interact with this artist.</p>
            </div>
            <div class="modal-footer border-0">
                <a href="<?= BASE_URL ?>login" class="btn btn-primary">Log in</a>
                <a href="<?= BASE_URL ?>register" class="btn btn-outline-secondary">Register</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Follow toggle script — logged in users only -->
<?php if (isset($_SESSION['user_id'])): ?>
<script>
(function () {
    const btn = document.getElementById('btn-follow-action');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const isFollowing = this.dataset.following === '1';

        if (isFollowing) {
            this.dataset.following = '0';
            this.classList.remove('following');
            this.innerHTML = '<i class="fas fa-plus me-1"></i> Follow';
        } else {
            this.dataset.following = '1';
            this.classList.add('following');
            this.innerHTML = '<i class="fas fa-check me-1"></i> Following';
        }
    });
})();
</script>
<?php endif; ?>