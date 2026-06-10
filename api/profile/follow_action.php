<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to modify favorites.']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$artist_id  = !empty($data['artist_id']) ? intval($data['artist_id']) : null;
$target_user_id = !empty($data['user_id']) ? intval($data['user_id']) : null;
$action     = $data['action'] ?? '';
$account_id = $_SESSION['account_id'];

if (!$artist_id && !$target_user_id) {
    echo json_encode(['success' => false, 'message' => 'No valid target specified.']);
    exit;
}

try {
    $db = getDB();

    if ($action === 'follow') {
        $stmt = $db->prepare("
            INSERT IGNORE INTO favorite_tbl (account_id, user_id, artist_id)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$account_id, $target_user_id, $artist_id]);
    } else if ($action === 'unfollow') {
        $stmt = $db->prepare("
            DELETE FROM favorite_tbl
            WHERE account_id = ?
              AND (user_id   = ? OR (user_id IS NULL   AND ? IS NULL))
              AND (artist_id = ? OR (artist_id IS NULL AND ? IS NULL))
        ");
        $stmt->execute([$account_id, $target_user_id, $target_user_id, $artist_id, $artist_id]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>