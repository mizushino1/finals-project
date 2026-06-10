<?php
ob_start();
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean();
header('Content-Type: application/json');

// Only users/clients (commission owners) need this endpoint
if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
if ($role !== 'user' && $role !== 'client' && $role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

try {
    $db = getDB();

    if ($role === 'admin') {
        // Admins see all pending requests across all commissions
        $stmt = $db->prepare('
            SELECT
                r.request_id,
                r.commission_id,
                r.artist_id,
                r.message          AS pitch_message,
                r.status_id        AS request_status_id,
                r.requested_at,
                c.description      AS commission_description,
                c.price,
                c.status_id        AS commission_status_id,
                c.commission_date,
                cat.category_name,
                aa.username        AS artist_username,
                aa.first_name      AS artist_first_name,
                aa.last_name       AS artist_last_name,
                oa.username        AS owner_username
            FROM commission_request_tbl r
            JOIN commission_tbl   c   ON r.commission_id = c.commission_id
            JOIN artist_tbl       art ON r.artist_id     = art.artist_id
            JOIN account_tbl      aa  ON art.account_id  = aa.account_id
            JOIN user_tbl         u   ON c.user_id       = u.user_id
            JOIN account_tbl      oa  ON u.account_id    = oa.account_id
            LEFT JOIN category_tbl cat ON c.category_id  = cat.category_id
            WHERE r.status_id = 2
            ORDER BY r.requested_at DESC
        ');
        $stmt->execute();
    } else {
        // Regular users only see pending requests on commissions they own
        $stmt = $db->prepare('
            SELECT
                r.request_id,
                r.commission_id,
                r.artist_id,
                r.message          AS pitch_message,
                r.status_id        AS request_status_id,
                r.requested_at,
                c.description      AS commission_description,
                c.price,
                c.status_id        AS commission_status_id,
                c.commission_date,
                cat.category_name,
                aa.username        AS artist_username,
                aa.first_name      AS artist_first_name,
                aa.last_name       AS artist_last_name
            FROM commission_request_tbl r
            JOIN commission_tbl   c   ON r.commission_id = c.commission_id
            JOIN artist_tbl       art ON r.artist_id     = art.artist_id
            JOIN account_tbl      aa  ON art.account_id  = aa.account_id
            JOIN user_tbl         u   ON c.user_id       = u.user_id
            LEFT JOIN category_tbl cat ON c.category_id  = cat.category_id
            WHERE r.status_id = 2
              AND u.account_id = ?
              AND c.status_id  = 1
            ORDER BY r.requested_at DESC
        ');
        $stmt->execute([$_SESSION['account_id']]);
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