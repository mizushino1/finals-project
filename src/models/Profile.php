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

    $clean_username   = htmlspecialchars($profile['username']);
    $display_name     = htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
    $has_custom_avatar = !empty($profile['avatar']);
    $avatar_source    = $has_custom_avatar ? BASE_URL . htmlspecialchars($profile['avatar']) : '';

    // Live avg / count from DB (shown server-side; JS will refresh on Reviews tab open)
    $live_avg_rating  = $profile['avg_rating']  ? number_format((float)$profile['avg_rating'], 1) : '—';
    $live_review_count = (int)($profile['review_count'] ?? 0);

    $is_following = false;
    $viewer_following_count = 0;
    if (isset($_SESSION['account_id'])) {
        // Is the viewer following this profile?
        if ($_SESSION['account_id'] != $profile_account_id) {
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

        // How many accounts does the viewer follow?
        $fol_stmt = $db->prepare("SELECT COUNT(*) FROM favorite_tbl WHERE account_id = ?");
        $fol_stmt->execute([$_SESSION['account_id']]);
        $viewer_following_count = (int) $fol_stmt->fetchColumn();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>