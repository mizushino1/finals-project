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
if ($role !== 'user' && $role !== 'client' && $role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

try {
    $db = getDB();

    if ($role === 'admin') {
        $stmt = $db->prepare('
            SELECT
                r.request_id,
                r.commission_id,
                r.artist_id,
                r.message           AS pitch_message,
                r.status_id         AS request_status_id,
                r.requested_at,
                c.description       AS commission_description,
                c.price,
                c.status_id         AS commission_status_id,
                c.commission_date,
                cat.category_name,
                aa.username         AS artist_username,
                aa.first_name       AS artist_first_name,
                aa.last_name        AS artist_last_name,
                oa.username         AS owner_username,
                img.image_url       AS artist_avatar_url
            FROM commission_request_tbl r
            JOIN commission_tbl    c   ON r.commission_id  = c.commission_id
            JOIN artist_tbl        art ON r.artist_id      = art.artist_id
            JOIN account_tbl       aa  ON art.account_id   = aa.account_id
            JOIN user_tbl          u   ON c.user_id        = u.user_id
            JOIN account_tbl       oa  ON u.account_id     = oa.account_id
            LEFT JOIN category_tbl cat ON c.category_id    = cat.category_id
            LEFT JOIN image_tbl    img ON img.artist_id    = art.artist_id AND img.image_type_id = 1
            WHERE r.status_id = 2
            ORDER BY r.requested_at DESC
        ');
        $stmt->execute();
    } else {
        // user_id stores the user_tbl PK for user/client role sessions
        $stmt = $db->prepare('
            SELECT
                r.request_id,
                r.commission_id,
                r.artist_id,
                r.message           AS pitch_message,
                r.status_id         AS request_status_id,
                r.requested_at,
                c.description       AS commission_description,
                c.price,
                c.status_id         AS commission_status_id,
                c.commission_date,
                cat.category_name,
                aa.username         AS artist_username,
                aa.first_name       AS artist_first_name,
                aa.last_name        AS artist_last_name,
                img.image_url       AS artist_avatar_url
            FROM commission_request_tbl r
            JOIN commission_tbl    c   ON r.commission_id  = c.commission_id
            JOIN artist_tbl        art ON r.artist_id      = art.artist_id
            JOIN account_tbl       aa  ON art.account_id   = aa.account_id
            JOIN user_tbl          u   ON c.user_id        = u.user_id
            LEFT JOIN category_tbl cat ON c.category_id    = cat.category_id
            LEFT JOIN image_tbl    img ON img.artist_id    = art.artist_id AND img.image_type_id = 1
            WHERE r.status_id = 2
              AND u.user_id   = ?
              AND c.status_id = 1
            ORDER BY r.requested_at DESC
        ');
        $stmt->execute([$_SESSION['user_id']]);
    }

    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data'    => $requests ?: []
    ]);

} catch (PDOException $e) {
    error_log('PDO ERROR (fetch_pending_requests): ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database operation failed.'
    ]);
}