<?php
// Prevent session conflict errors if initialized globally by layout templates
if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../../config/session.php';
}
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

// ── Resolve session account_id robustly ──────────────────────
// Sessions may store account_id directly (admin/artist) or only
// user_id (user/client). Normalise to $session_account_id early
// so every check below uses the same value.
$session_account_id = $_SESSION['account_id'] ?? null;

try {
    $db = getDB();

    if (!$session_account_id && isset($_SESSION['user_id'])) {
        $acc_stmt = $db->prepare('SELECT account_id FROM user_tbl WHERE user_id = ?');
        $acc_stmt->execute([$_SESSION['user_id']]);
        $session_account_id = $acc_stmt->fetchColumn() ?: null;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// ── Determine which profile to display ───────────────────────
if (isset($_GET['id'])) {
    $profile_account_id = intval($_GET['id']);
} elseif ($session_account_id) {
    $profile_account_id = $session_account_id;
} else {
    die("Profile context not found. Please log in.");
}

// ── Fetch profile data ────────────────────────────────────────
try {
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
        (SELECT COUNT(*) FROM favorite_tbl f WHERE f.account_id = a.account_id) AS following_count,
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

    $viewer_role = strtolower($_SESSION['role'] ?? '');

    $is_own_user_profile = $session_account_id !== null
        && $session_account_id == $profile['account_id']
        && in_array($viewer_role, ['user', 'client']);

    $is_artist_profile = !empty($profile['artist_id']);

    $clean_username    = htmlspecialchars($profile['username']);
    $display_name      = htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
    $has_custom_avatar = !empty($profile['avatar']);
    $avatar_source     = $has_custom_avatar ? BASE_URL . htmlspecialchars($profile['avatar']) : '';

    $live_avg_rating   = $profile['avg_rating']
        ? number_format((float) $profile['avg_rating'], 1) : '—';
    $live_review_count = (int) ($profile['review_count'] ?? 0);

    // ── Follow state (uses $session_account_id consistently) ─
    $is_following           = false;
    $viewer_following_count = 0;

    if ($session_account_id) {
        if ($session_account_id != $profile_account_id) {
            $fav_stmt = $db->prepare("
                SELECT 1 FROM favorite_tbl
                WHERE account_id = ?
                  AND (artist_id = ? OR user_id = ?)
                LIMIT 1
            ");
            $fav_stmt->execute([
                $session_account_id,
                $profile['artist_id'] ?? null,
                $profile['user_id']   ?? null,
            ]);
            $is_following = (bool) $fav_stmt->fetchColumn();
        }

        $fol_stmt = $db->prepare("SELECT COUNT(*) FROM favorite_tbl WHERE account_id = ?");
        $fol_stmt->execute([$session_account_id]);
        $viewer_following_count = (int) $fol_stmt->fetchColumn();
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
                                 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
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

                <?php if ($is_artist_profile): ?>
                    <span class="badge theme-fill text-dark">Artist</span>
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
                <span class="profile-stat-value" id="stat-following"> <?php echo number_format($profile['following_count'] ?? 0); ?></span>
                <span class="profile-stat-label">Following</span>
            </div>

            <div class="profile-stat">
                <span class="profile-stat-value">0</span>
                <span class="profile-stat-label">Likes</span>
            </div>

            <?php if ($is_artist_profile): ?>
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
                $session_active_id = $_SESSION['account_id'] ?? null;
                $is_viewing_own_profile = false;
                if ($session_active_id !== null) {
                    $is_viewing_own_profile = ($session_active_id == $profile['account_id']);
                } elseif (isset($_SESSION['user_id'])) {
                    if ($viewer_role === 'artist') {
                        $is_viewing_own_profile = ($_SESSION['user_id'] == ($profile['artist_id'] ?? null));
                    } elseif (in_array($viewer_role, ['user', 'client'])) {
                        $is_viewing_own_profile = ($_SESSION['user_id'] == ($profile['user_id'] ?? null));
                    }
                    $session_active_id = $is_viewing_own_profile ? $profile['account_id'] : $_SESSION['user_id'];
                }
                if ($session_active_id):
                ?>
                    <?php if (!$is_viewing_own_profile): ?>
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
                        <a href="<?php echo BASE_URL; ?>messages?target_id=<?php echo $profile['account_id']; ?>&name=<?php echo urlencode($clean_username); ?>"
                            class="btn-artovia-primary d-inline-flex align-items-center justify-content-center rounded-2"
                            style="width:38px;height:38px;"
                            title="Message <?php echo $clean_username; ?>">
                            <i class="bi bi-chat-dots-fill"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>settings"
                            id="btn-edit-profile"
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

        <!-- Tabs -->
        <ul class="nav profile-tabs" id="profileTabs" role="tablist">
            <?php if ($is_artist_profile): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                        id="tab-artworks"
                        data-bs-toggle="tab"
                        data-bs-target="#pane-artworks"
                        type="button" role="tab"
                        aria-controls="pane-artworks"
                        aria-selected="true">Artworks</button>
                </li>
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
            <?php if ($is_own_user_profile): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link<?php echo !$is_artist_profile ? ' active' : ''; ?>"
                        id="tab-commissions"
                        data-bs-toggle="tab"
                        data-bs-target="#pane-commissions"
                        type="button" role="tab"
                        aria-controls="pane-commissions"
                        aria-selected="<?php echo !$is_artist_profile ? 'true' : 'false'; ?>">Commissions</button>
                </li>
            <?php endif; ?>
        </ul>

    </div>
</section>

<main class="py-4">
    <div class="container">
        <div class="tab-content" id="profileTabContent">

            <!-- ── Artworks pane (artists only) ─────────────────── -->
            <?php if ($is_artist_profile): ?>
                <div class="tab-pane fade show active" id="pane-artworks" role="tabpanel">
                    <div id="artworks-grid"
                        class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 justify-content-center"
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

            <!-- ── Commissions pane (own user profile only) ─────── -->
            <?php if ($is_own_user_profile): ?>
                <div class="tab-pane fade<?php echo !$is_artist_profile ? ' show active' : ''; ?>" id="pane-commissions" role="tabpanel">

                    <div class="d-flex justify-content-end mb-3">
                        <button
                            class="btn-artovia-primary px-4 py-2 fs-fluid-xs rounded-2"
                            data-bs-toggle="modal"
                            data-bs-target="#postCommissionModal">
                            + Post a Commission
                        </button>
                    </div>

                    <div id="profileCommissionsError" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                        <p class="grid-state__title fw-bold m-0 mb-1 fs-fluid-sm">Failed to load commissions</p>
                        <p class="grid-state__sub text-muted mx-auto mb-3 fs-fluid-xs">There was an issue communicating with the server.</p>
                        <button class="btn btn-outline-secondary btn-sm fs-fluid-xs" onclick="location.reload()">Retry</button>
                    </div>

                    <div id="profileCommissionsEmpty" class="grid-state d-none text-center py-5 px-3 border rounded-3 bg-card shadow-sm">
                        <p class="grid-state__title fw-bold m-0 mb-1 fs-fluid-sm">No commissions yet</p>
                        <p class="grid-state__sub text-muted mx-auto m-0 fs-fluid-xs">Click "+ Post a Commission" to create your first one.</p>
                    </div>

                    <div class="col-12 text-center py-5 text-muted" id="profileCommissionsLoading">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Loading commissions…
                    </div>

                    <div class="row g-2 g-sm-3 row-cols-1 row-cols-md-2 row-cols-xl-3" id="profileCommissionGrid"></div>

                </div>

                <!-- ── Post Commission Modal (copied from browse.php) ── -->
                <div class="modal fade" id="postCommissionModal" tabindex="-1" aria-labelledby="postCommissionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content bg-card border rounded-3 shadow">

                            <div class="modal-header border-bottom px-4 pt-4 pb-3">
                                <h5 class="modal-title fw-bold fs-fluid-sm" id="postCommissionModalLabel">Create Commission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body px-4 py-4">
                                <div id="commissionFormAlert" class="alert d-none mb-3 fs-fluid-xs" role="alert"></div>

                                <!-- Commission Name -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-fluid-xs">Commission Name <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="commissionTitle"
                                        class="form-control theme-border fs-fluid-xs"
                                        style="border-width:1px !important;"
                                        placeholder="e.g. Character portrait for my OC">
                                </div>

                                <!-- Category (Genre) -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-fluid-xs">Genre <span class="text-danger">*</span></label>
                                    <select
                                        id="commissionCategory"
                                        class="form-select theme-border fs-fluid-xs"
                                        style="border-width:1px !important;"
                                        aria-label="Commission category">
                                        <option value="" disabled selected>Select a genre</option>
                                        <option value="1">Anime</option>
                                        <option value="2">Chibi</option>
                                        <option value="3">Pixel Art</option>
                                        <option value="4">Watercolor</option>
                                        <option value="5">Fantasy</option>
                                        <option value="6">Logo Design</option>
                                        <option value="7">Portrait</option>
                                        <option value="8">Character Design</option>
                                    </select>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-fluid-xs">Description <span class="text-danger">*</span></label>
                                    <textarea
                                        id="commissionDescription"
                                        class="form-control theme-border hide-scrollbar fs-fluid-xs"
                                        rows="5"
                                        style="resize:none; overflow-y:auto; border-width:1px !important;"
                                        placeholder="Describe your project — style, mood, references, deliverables…"></textarea>
                                </div>

                                <!-- Budget + Upload -->
                                <div class="row g-3 mb-2">
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-fluid-xs">Budget (₱) <span class="text-danger">*</span></label>
                                        <input
                                            type="number"
                                            id="commissionBudget"
                                            class="form-control theme-border fs-fluid-xs"
                                            style="border-width:1px !important;"
                                            placeholder="e.g. 2500"
                                            min="1"
                                            step="any">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold fs-fluid-xs">Reference Image <span class="text-muted fw-normal">(Optional)</span></label>
                                        <input type="file" id="commissionImageFile" accept="image/*" class="d-none">
                                        <button
                                            type="button"
                                            class="btn-artovia-outline w-100 fs-fluid-xs"
                                            onclick="document.getElementById('commissionImageFile').click()">
                                            Select Image
                                        </button>
                                        <p id="commissionImageName" class="text-muted fs-fluid-xxs mt-1 mb-0 text-truncate"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer border-top px-4 pb-4 pt-3 d-flex gap-2 flex-nowrap">
                                <button type="button" id="submitCommissionBtn" class="btn-artovia-primary w-50 fs-fluid-xs rounded-2">
                                    Post Commission
                                </button>
                                <button type="button" class="btn btn-outline w-50 fs-fluid-xs" data-bs-dismiss="modal">Cancel</button>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../commissions/partials/edit_modal.php'; ?>
<?php require_once __DIR__ . '/../commissions/partials/review_modal.php'; ?>
<?php require_once __DIR__ . '/../commissions/partials/payment_modal.php'; ?>

<script>
    window.PROFILE_ACCOUNT_ID  = <?php echo $profile_account_id; ?>;
    window.BASE_URL            = '<?php echo BASE_URL; ?>';
    window.USER_ROLE           = '<?php echo addslashes(strtolower($_SESSION['role'] ?? 'guest')); ?>';
    window.IS_OWN_USER_PROFILE = <?php echo $is_own_user_profile ? 'true' : 'false'; ?>;
    window.IS_ARTIST           = <?php echo (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'artist') ? 'true' : 'false'; ?>;
</script>

<script src="<?= BASE_URL ?>public/js/artovia.core.js"></script>
<script src="<?= BASE_URL ?>public/js/profile.js"></script>