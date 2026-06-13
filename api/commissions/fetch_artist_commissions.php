<?php
ob_start();
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean();
header('Content-Type: application/json');

// Align with app-wide session convention: user_id + role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$role = strtolower($_SESSION['role']);
if ($role !== 'artist') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Artists only.']);
    exit;
}

try {
    $db = getDB();

    // For artist sessions, user_id stores artist_id directly
    $artistId = intval($_SESSION['user_id']);

    // Verify the artist profile exists
    $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE artist_id = ?');
    $stmtArtist->execute([$artistId]);
    if (!$stmtArtist->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
        exit;
    }

    // ── Pending: requests submitted by this artist awaiting a decision (status_id 2)
    $stmtPending = $db->prepare('
        SELECT
            r.request_id,
            r.commission_id,
            r.message           AS pitch_message,
            r.status_id         AS request_status_id,
            r.requested_at,
            c.description       AS commission_description,
            c.price,
            c.status_id         AS commission_status_id,
            c.commission_date,
            cat.category_name,
            oa.username         AS owner_username,
            oa.first_name       AS owner_first_name,
            oa.last_name        AS owner_last_name,
            img.image_url       AS owner_avatar_url
        FROM commission_request_tbl r
        JOIN commission_tbl    c   ON r.commission_id = c.commission_id
        JOIN user_tbl          u   ON c.user_id       = u.user_id
        JOIN account_tbl       oa  ON u.account_id    = oa.account_id
        LEFT JOIN category_tbl cat ON c.category_id   = cat.category_id
        LEFT JOIN image_tbl    img ON img.user_id      = u.user_id AND img.image_type_id = 1
        WHERE r.artist_id = ?
          AND r.status_id = 2
        ORDER BY r.requested_at DESC
    ');
    $stmtPending->execute([$artistId]);
    $pending = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

    // ── Accepted: commissions assigned to this artist (status 3=Accepted, 5=In Progress, 6=Completed)
    $stmtAccepted = $db->prepare('
        SELECT
            c.commission_id,
            c.description       AS commission_description,
            c.price,
            c.status_id         AS commission_status_id,
            c.commission_date,
            cat.category_name,
            oa.username         AS owner_username,
            oa.first_name       AS owner_first_name,
            oa.last_name        AS owner_last_name,
            img.image_url       AS owner_avatar_url
        FROM commission_tbl c
        JOIN user_tbl          u   ON c.user_id      = u.user_id
        JOIN account_tbl       oa  ON u.account_id   = oa.account_id
        LEFT JOIN category_tbl cat ON c.category_id  = cat.category_id
        LEFT JOIN image_tbl    img ON img.user_id     = u.user_id AND img.image_type_id = 1
        WHERE c.artist_id = ?
          AND c.status_id IN (3, 5, 6)
        ORDER BY c.commission_date DESC
    ');
    $stmtAccepted->execute([$artistId]);
    $accepted = $stmtAccepted->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'  => true,
        'pending'  => $pending  ?: [],
        'accepted' => $accepted ?: [],
    ]);

} catch (PDOException $e) {
    error_log('PDO ERROR (fetch_artist_commissions): ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed.'
    ]);
}