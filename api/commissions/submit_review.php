<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Ensure the user is authenticated
if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;
$rating       = isset($data['rating'])        ? intval($data['rating']) : 0;
$comment      = isset($data['comment'])       ? trim($data['comment']) : '';

// Validate parameters
if ($commissionId <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters. Rating must be 1 to 5.']);
    exit;
}

try {
    $db = getDB();
    $account_id = $_SESSION['account_id'];

    // 1. Verify the commission belongs to this user and its status is completed (status_id = 6)
    $stmtCheck = $db->prepare('
        SELECT c.artist_id 
        FROM commission_tbl c
        JOIN user_tbl u ON c.user_id = u.user_id
        WHERE c.commission_id = ? AND u.account_id = ? AND c.status_id = 6
    ');
    $stmtCheck->execute([$commissionId, $account_id]);
    $commission = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$commission) {
        echo json_encode(['success' => false, 'message' => 'Commission not found or not eligible for a review.']);
        exit;
    }

    $artistId = intval($commission['artist_id']);
    if ($artistId <= 0) {
        echo json_encode(['success' => false, 'message' => 'No artist assigned to this commission.']);
        exit;
    }

    // 2. Insert or update the rating into review_tbl
    // Uses ON DUPLICATE KEY UPDATE to handle your schema's unique key constraint smoothly
    $stmtInsert = $db->prepare('
        INSERT INTO review_tbl (artist_id, reviewer_account_id, commission_id, rating, comment)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            commission_id = VALUES(commission_id),
            rating        = VALUES(rating),
            comment       = VALUES(comment),
            updated_at    = CURRENT_TIMESTAMP
    ');
    $stmtInsert->execute([$artistId, $account_id, $commissionId, $rating, $comment]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your review has been submitted successfully.'
    ]);

} catch (PDOException $e) {
    error_log('PDO ERROR (submit_review): ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error encountered while processsing your review.'
    ]);
}