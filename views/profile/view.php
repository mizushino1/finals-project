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
} elseif (isset($_SESSION['user_id'])) {
    $profile_account_id = $_SESSION['user_id'];
} else {
    die("Profile context not found. Please log in.");
}

// 2. Fetch records matching your specific artopia_db relations
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
            u.user_id,
            (SELECT img.image_url 
             FROM image_tbl img 
             WHERE (img.user_id = u.user_id OR img.artist_id = art.artist_id) 
               AND img.image_type_id = 1 
             ORDER BY img.uploaded_at DESC 
             LIMIT 1) as avatar,
            (SELECT COUNT(*) FROM favorite_tbl f WHERE f.artist_id = art.artist_id) as followers_count
        FROM account_tbl a
        LEFT JOIN artist_tbl art ON a.account_id = art.account_id
        LEFT JOIN user_tbl u ON a.account_id = u.account_id
        WHERE a.account_id = :account_id
    ";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':account_id' => $profile_account_id]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        die("User profile data could not be found inside the system tables.");
    }

    // Set variable strings immediately for the HTML block below
    $clean_username = htmlspecialchars($profile['username']);
    $display_name   = htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
    
    // Determine the avatar resource path context if it exists
    $has_custom_avatar = !empty($profile['avatar']);
    $avatar_source = $has_custom_avatar ? BASE_URL . htmlspecialchars($profile['avatar']) : '';
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<section class="profile-header">
    <div class="container">
        <div class="d-flex align-items-start gap-3 flex-wrap">
            <div class="profile-avatar-container" style="width: 130px; height: 130px; border-radius: 50%; overflow: hidden; border: 3px solid #c9873a; display: flex; align-items: center; justify-content: center; background: #e9ecef;">
                <?php if ($has_custom_avatar): ?>
                    <img
                        src="<?php echo $avatar_source; ?>"
                        alt="User avatar"
                        class="profile-avatar"
                        style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="profile-avatar" style="width: 100%; height: 100%; background: #e9ecef; padding: 1.8rem; box-sizing: border-box; color: #212529;">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                <?php endif; ?>
            </div>

            <div class="flex-grow-1 pt-1">
                <h1 class="profile-username mb-1"><?php echo $clean_username; ?></h1>
                
                <p class="profile-bio">Hello, I am <?php echo $display_name; ?></p>
                
                <?php if (!empty($profile['artist_id'])): ?>
                    <span class="badge bg-success">Artist</span>
                    <small class="text-muted ms-2">Starting Rate: ₱<?php echo number_format($profile['starting_rate'], 2); ?></small>
                <?php endif; ?>
            </div>

            <div class="profile-search-wrapper ms-auto mt-1">
                <i class="fas fa-search search-icon"></i>
                <input
                    type="search"
                    class="profile-search-input"
                    placeholder="Search profile portfolio"
                    aria-label="Search profile">
            </div>

        </div>

        <div class="d-flex align-items-center flex-wrap profile-stats-row mt-3">

            <div class="profile-stat">
                <span class="profile-stat-value"><?php echo number_format($profile['followers_count'] ?? 0); ?></span>
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

            <div class="profile-stat">
                <span class="profile-stat-value profile-reviews-badge">
                    <i class="fas fa-star"></i> 5.0/5
                </span>
                <span class="profile-stat-label">Reviews</span>
            </div>

            <div class="ms-auto d-flex align-items-center gap-2">
                <?php 
                $session_active_id = $_SESSION['account_id'] ?? $_SESSION['user_id'] ?? null;
                if ($session_active_id): 
                ?>
                    <?php if ($session_active_id != $profile['account_id']): ?>
                        <button
                            class="btn btn-follow"
                            type="button"
                            id="btn-follow-action"
                            data-following="0"
                            data-artist-id="<?php echo $profile['artist_id'] ?? 0; ?>">
                            <i class="fas fa-plus me-1"></i> Favorite Artist
                        </button>
                        <button class="btn btn-notify" type="button" aria-label="Notifications">
                            <i class="fas fa-bell"></i>
                        </button>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>views/profile/edit.php" class="btn btn-outline-secondary">Edit Account Settings</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button
                        class="btn btn-follow"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#loginPromptModal">
                        <i class="fas fa-plus me-1"></i> Favorite Artist
                    </button>
                <?php endif; ?>
            </div>

        </div>

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
                    aria-selected="true">Artworks</button>
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
                    aria-selected="false">Collaborations</button>
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
                    aria-selected="false">Showcase</button>
            </li>
        </ul>

    </div>
</section>

<main class="py-4">
    <div class="container">
        <div class="tab-content" id="profileTabContent">
            <div class="tab-pane fade show active" id="pane-artworks" role="tabpanel">
                <p class="theme-font-color">Artworks content linked to portfolio items will populate here.</p>
            </div>
            <div class="tab-pane fade" id="pane-collaborations" role="tabpanel">
                <p class="theme-font-color">Collaborations data content here.</p>
            </div>
            <div class="tab-pane fade" id="pane-showcase" role="tabpanel">
                <p class="theme-font-color">Showcase content here.</p>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo BASE_URL; ?>public/assets/js/profile.js"></script>