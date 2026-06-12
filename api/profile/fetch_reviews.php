<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

$account_id = intval($_GET['account_id'] ?? 0);
if (!$account_id) {
    echo json_encode(['success' => false, 'message' => 'account_id is required']);
    exit;
}

$page     = max(1, intval($_GET['page']     ?? 1));
$per_page = max(1, min(50, intval($_GET['per_page'] ?? 10)));
$offset   = ($page - 1) * $per_page;

try {
    $db = getDB();

    // Resolve artist_id from account_id
    $stmt = $db->prepare('SELECT artist_id FROM artist_tbl WHERE account_id = ?');
    $stmt->execute([$account_id]);
    $artist_id = $stmt->fetchColumn();

    if (!$artist_id) {
        // Not an artist — return empty gracefully
        echo json_encode([
            'success'    => true,
            'data'       => [],
            'total'      => 0,
            'page'       => $page,
            'per_page'   => $per_page,
            'pages'      => 0,
            'avg_rating' => null,
        ]);
        exit;
    }

    // Total count
    $countStmt = $db->prepare('SELECT COUNT(*) FROM review_tbl WHERE artist_id = ?');
    $countStmt->execute([$artist_id]);
    $total = (int) $countStmt->fetchColumn();

    // Average rating
    $avgStmt = $db->prepare('SELECT ROUND(AVG(rating), 1) FROM review_tbl WHERE artist_id = ?');
    $avgStmt->execute([$artist_id]);
    $avg_rating = $avgStmt->fetchColumn();

    // Paginated reviews joined with reviewer account info
    $reviewStmt = $db->prepare('
        SELECT
            r.review_id,
            r.rating,
            r.comment,
            r.created_at,
            a.username  AS reviewer_username,
            a.first_name,
            a.last_name,
            (SELECT img.image_url
             FROM image_tbl img
             JOIN user_tbl u ON img.user_id = u.user_id
             WHERE u.account_id = a.account_id
               AND img.image_type_id = 1
             ORDER BY img.uploaded_at DESC
             LIMIT 1) AS reviewer_avatar
        FROM review_tbl r
        JOIN account_tbl a ON r.reviewer_account_id = a.account_id
        WHERE r.artist_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?
    ');
    $reviewStmt->execute([$artist_id, $per_page, $offset]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success'    => true,
        'data'       => $reviews,
        'total'      => $total,
        'page'       => $page,
        'per_page'   => $per_page,
        'pages'      => (int) ceil($total / max(1, $per_page)),
        'avg_rating' => $avg_rating ? number_format((float) $avg_rating, 1) : null,
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}