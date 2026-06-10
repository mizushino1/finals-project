<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to modify favorites.']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$artist_id = intval($data['artist_id'] ?? 0);
$action    = $data['action'] ?? '';
$user_id   = $_SESSION['user_id'];

if (!$artist_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid Artist Parameter Context.']);
    exit;
}

try {
    $db = getDB();

    if ($action === 'favorite') {
        $stmt = $db->prepare("INSERT IGNORE INTO favorite_tbl (user_id, artist_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $artist_id]);
    } else {
        $stmt = $db->prepare("DELETE FROM favorite_tbl WHERE user_id = ? AND artist_id = ?");
        $stmt->execute([$user_id, $artist_id]);
    }

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>