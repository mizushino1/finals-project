<?php
// Prevent session conflict errors if initialized globally by layout templates
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../../config/session.php';
}
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

// 1. Determine active user account context safely
if (isset($_GET['id'])) {
    $profile_account_id = intval($_GET['id']);
} elseif (isset($_SESSION['account_id'])) {
    $profile_account_id = $_SESSION['account_id'];
} else {
    die("Profile context not found. Please log in.");
}

// 2. Fetch records matching artopia_db relations
try {
    $db = getDB();

    $query = "
    SELECT
        a.account_id,
        a.username,
        a.first_name,
        a.last_name,
        art.artist_id,
        art.starting_rate,
        art.is_available,
        art.artist_description,
        u.user_id,
        (SELECT img.image_url
         FROM image_tbl img
         WHERE (img.user_id = u.user_id OR img.artist_id = art.artist_id)
           AND img.image_type_id = 1
         ORDER BY img.uploaded_at DESC
         LIMIT 1) AS avatar,
        (SELECT COUNT(*) FROM favorite_tbl f WHERE f.artist_id = art.artist_id) AS followers_count,
        (SELECT ROUND(AVG(r.rating), 1) FROM review_tbl r WHERE r.artist_id = art.artist_id) AS avg_rating,
        (SELECT COUNT(*)               FROM review_tbl r WHERE r.artist_id = art.artist_id) AS review_count
    FROM account_tbl a
    LEFT JOIN artist_tbl art ON a.account_id = art.account_id
    LEFT JOIN user_tbl   u   ON a.account_id = u.account_id
    WHERE a.account_id = :account_id
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([':account_id' => $profile_account_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        die("User profile data could not be found inside the system tables.");
    }

    $clean_username   = htmlspecialchars($profile['username']);
    $display_name     = htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
    $has_custom_avatar = !empty($profile['avatar']);
    $avatar_source    = $has_custom_avatar ? BASE_URL . htmlspecialchars($profile['avatar']) : '';

    // Live avg / count from DB (shown server-side; JS will refresh on Reviews tab open)
    $live_avg_rating  = $profile['avg_rating']  ? number_format((float)$profile['avg_rating'], 1) : '—';
    $live_review_count = (int)($profile['review_count'] ?? 0);

    $is_following = false;
    if (isset($_SESSION['account_id']) && $_SESSION['account_id'] != $profile_account_id) {
        $fav_stmt = $db->prepare("
            SELECT 1 FROM favorite_tbl
            WHERE account_id = ?
              AND (artist_id = ? OR user_id = ?)
            LIMIT 1
        ");
        $fav_stmt->execute([
            $_SESSION['account_id'],
            $profile['artist_id'] ?? null,
            $profile['user_id']   ?? null,
        ]);
        $is_following = (bool) $fav_stmt->fetchColumn();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<section class="profile-header">
    <div class="container">
        <div class="d-flex align-items-start gap-3 flex-wrap">

            <!-- Avatar -->
            <div class="profile-avatar-container"
                 style="width:130px;height:130px;border-radius:50%;overflow:hidden;
                        border:3px solid #c9873a;display:flex;align-items:center;
                        justify-content:center;background:#e9ecef;">
                <?php if ($has_custom_avatar): ?>
                    <img src="<?php echo $avatar_source; ?>"
                         alt="User avatar"
                         class="profile-avatar"
                         style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                         fill="currentColor" class="profile-avatar"
                         style="width:100%;height:100%;background:#e9ecef;
                                padding:1.8rem;box-sizing:border-box;color:#212529;">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12
                                 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0
                                 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                <?php endif; ?>
            </div>

            <!-- Meta -->
            <div class="flex-grow-1 pt-1">
                <h1 class="profile-username mb-1"><?php echo $clean_username; ?></h1>
                <p class="fs-fluid-xxs mb-0"><?php echo $display_name; ?></p>

                <?php if (!empty($profile['artist_description'])): ?>
                    <p class="profile-bio mb-2">
                        <?php echo nl2br(htmlspecialchars($profile['artist_description'])); ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($profile['artist_id'])): ?>
                    <span class="badge bg-success">Artist</span>
                    <small class="text-muted ms-2">
                        Starting Rate: ₱<?php echo number_format($profile['starting_rate'], 2); ?>
                    </small>
                <?php endif; ?>
            </div>

            <!-- Search -->
            <div class="profile-search-wrapper ms-auto mt-1">
                <i class="fas fa-search search-icon"></i>
                <input type="search"
                       class="profile-search-input"
                       id="profile-search"
                       placeholder="Search artworks"
                       aria-label="Search artworks">
            </div>

        </div><!-- /row -->

        <!-- Stats row -->
        <div class="d-flex align-items-center flex-wrap profile-stats-row mt-3">

            <div class="profile-stat">
                <span class="profile-stat-value" id="stat-followers">
                    <?php echo number_format($profile['followers_count'] ?? 0); ?>
                </span>
                <span class="profile-stat-label">Followers</span>
            </div>

            <div class="profile-stat">
                <span class="profile-stat-value">0</span>
                <span class="profile-stat-label">Following</span>
            </div>

            <div class="profile-stat">
                <span class="profile-stat-value">0</span>
                <span class="profile-stat-label">Likes</span>
            </div>

            <?php if (!empty($profile['artist_id'])): ?>
            <div class="profile-stat">
                <span class="profile-stat-value profile-reviews-badge" id="stat-avg-rating">
                    <i class="fas fa-star"></i> <?php echo $live_avg_rating; ?>/5
                </span>
                <span class="profile-stat-label">
                    Reviews
                    <span id="stat-review-count"
                          class="ms-1 text-muted">(<?php echo $live_review_count; ?>)</span>
                </span>
            </div>
            <?php endif; ?>

            <!-- Follow / Edit -->
            <div class="ms-auto d-flex align-items-center gap-2">
                <?php
                $session_active_id = $_SESSION['account_id'] ?? $_SESSION['user_id'] ?? null;
                if ($session_active_id):
                ?>
                    <?php if ($session_active_id != $profile['account_id']): ?>
                        <button class="btn <?php echo $is_following ? 'btn-success' : 'btn-follow'; ?>"
                                type="button"
                                id="btn-follow-action"
                                data-following="<?php echo $is_following ? '1' : '0'; ?>"
                                data-artist-id="<?php echo $profile['artist_id'] ?? 0; ?>"
                                data-user-id="<?php echo $profile['user_id'] ?? 0; ?>">
                            <?php if ($is_following): ?>
                                <i class="fas fa-check me-1"></i> Following
                            <?php else: ?>
                                <i class="fas fa-plus me-1"></i> Follow
                            <?php endif; ?>
                        </button>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>views/profile/edit.php"
                           class="btn btn-outline-secondary">Edit Account Settings</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-follow"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#loginPromptModal">
                        <i class="fas fa-plus me-1"></i> Favorite Artist
                    </button>
                <?php endif; ?>
            </div>

        </div><!-- /stats-row -->

        <!-- Tabs: Artworks + Reviews only -->
        <ul class="nav profile-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active"
                        id="tab-artworks"
                        data-bs-toggle="tab"
                        data-bs-target="#pane-artworks"
                        type="button" role="tab"
                        aria-controls="pane-artworks"
                        aria-selected="true">Artworks</button>
            </li>
            <?php if (!empty($profile['artist_id'])): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link"
                        id="tab-reviews"
                        data-bs-toggle="tab"
                        data-bs-target="#pane-reviews"
                        type="button" role="tab"
                        aria-controls="pane-reviews"
                        aria-selected="false">Reviews</button>
            </li>
            <?php endif; ?>
        </ul>

    </div>
</section>

<main class="py-4">
    <div class="container">
        <div class="tab-content" id="profileTabContent">

            <!-- ── Artworks pane ───────────────────────────────── -->
            <div class="tab-pane fade show active" id="pane-artworks" role="tabpanel">
                <div id="artworks-grid"
                     class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3"
                     data-account-id="<?php echo $profile_account_id; ?>">
                    <!-- Populated by profile.js -->
                    <div class="col-12 text-center py-5 text-muted" id="artworks-loading">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading artworks…
                    </div>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4" id="artworks-pagination"></div>
            </div>

            <!-- ── Reviews pane (artists only) ────────────────── -->
            <?php if (!empty($profile['artist_id'])): ?>
            <div class="tab-pane fade" id="pane-reviews" role="tabpanel"
                 data-account-id="<?php echo $profile_account_id; ?>"
                 data-loaded="false">

                <!-- Summary card -->
                <div class="d-flex align-items-center gap-3 mb-4 p-3
                            rounded" style="background:var(--clr-bg-alt);">
                    <div class="text-center">
                        <div class="display-6 fw-bold profile-reviews-badge" id="review-summary-avg">
                            <?php echo $live_avg_rating; ?>
                        </div>
                        <div id="review-summary-stars" class="mb-1">
                            <?php
                            $stars = round((float)($profile['avg_rating'] ?? 0));
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $stars
                                    ? '<i class="fas fa-star text-warning"></i>'
                                    : '<i class="far fa-star text-muted"></i>';
                            }
                            ?>
                        </div>
                        <div class="small text-muted">
                            <?php echo $live_review_count; ?> review<?php echo $live_review_count !== 1 ? 's' : ''; ?>
                        </div>
                    </div>
                </div>

                <!-- Review list -->
                <div id="reviews-list">
                    <div class="text-center py-5 text-muted" id="reviews-loading">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading reviews…
                    </div>
                </div>

                <div class="d-flex justify-content-center mt-4" id="reviews-pagination"></div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<script>
    // Pass PHP values into JS without inline PHP in profile.js
    window.PROFILE_ACCOUNT_ID = <?php echo $profile_account_id; ?>;
    window.BASE_URL            = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>public/js/profile.js"></script>