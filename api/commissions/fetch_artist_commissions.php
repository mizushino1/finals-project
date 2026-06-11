<?php
ob_start();
require_once '../../config/session.php';
require_once '../../config/database.php';
ob_clean();
header('Content-Type: application/json');

// Only artists may call this endpoint
if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
if ($role !== 'artist') {
    echo json_encode(['success' => false, 'message' => 'Access denied. Artists only.']);
    exit;
}

try {
    $db = getDB();

    // Resolve artist_id from the session account
    $stmtArtist = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
    $stmtArtist->execute([$_SESSION['account_id']]);
    $artistRow = $stmtArtist->fetch(PDO::FETCH_ASSOC);

    if (!$artistRow) {
        echo json_encode(['success' => false, 'message' => 'Artist profile not found.']);
        exit;
    }
    $artistId = $artistRow['artist_id'];

    // ── Pending: requests submitted by this artist that are still awaiting a decision (status_id 2)
    $stmtPending = $db->prepare('
        SELECT
            r.request_id,
            r.commission_id,
            r.message          AS pitch_message,
            r.status_id        AS request_status_id,
            r.requested_at,
            c.description      AS commission_description,
            c.price,
            c.status_id        AS commission_status_id,
            c.commission_date,
            cat.category_name,
            oa.username        AS owner_username,
            oa.first_name      AS owner_first_name,
            oa.last_name       AS owner_last_name
        FROM commission_request_tbl r
        JOIN commission_tbl   c   ON r.commission_id = c.commission_id
        JOIN user_tbl         u   ON c.user_id       = u.user_id
        JOIN account_tbl      oa  ON u.account_id    = oa.account_id
        LEFT JOIN category_tbl cat ON c.category_id  = cat.category_id
        WHERE r.artist_id  = ?
          AND r.status_id  = 2
        ORDER BY r.requested_at DESC
    ');
    $stmtPending->execute([$artistId]);
    $pending = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

    // ── Accepted: commissions officially assigned to this artist (commission status 3, 5, or 6)
    //   status 3 = Accepted (not yet started)
    //   status 5 = In Progress
    //   status 6 = Completed
    $stmtAccepted = $db->prepare('
        SELECT
            c.commission_id,
            c.description      AS commission_description,
            c.price,
            c.status_id        AS commission_status_id,
            c.commission_date,
            cat.category_name,
            oa.username        AS owner_username,
            oa.first_name      AS owner_first_name,
            oa.last_name       AS owner_last_name
        FROM commission_tbl c
        JOIN user_tbl         u   ON c.user_id      = u.user_id
        JOIN account_tbl      oa  ON u.account_id   = oa.account_id
        LEFT JOIN category_tbl cat ON c.category_id = cat.category_id
        WHERE c.artist_id  = ?
          AND c.status_id  IN (3, 5, 6)
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