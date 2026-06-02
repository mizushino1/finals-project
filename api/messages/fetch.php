<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db   = getDB();
$role = $_SESSION['role'];
$id   = $_SESSION['user_id'];

if ($role === 'user') {
    $artistId = intval($_GET['artist_id']);
    $stmt = $db->prepare('
        SELECT * FROM message_box
        WHERE sender_id = ? AND receiver_id = ?
        ORDER BY sent_at ASC
    ');
    $stmt->execute([$id, $artistId]);

} elseif ($role === 'artist') {
    $userId = intval($_GET['user_id']);
    $stmt = $db->prepare('
        SELECT * FROM message_box
        WHERE sender_id = ? AND receiver_id = ?
        ORDER BY sent_at ASC
    ');
    $stmt->execute([$userId, $id]);
}

// Mark messages as read
$db->prepare('UPDATE message_box SET status = ? WHERE receiver_id = ? AND status = ?')
   ->execute(['read', $id, 'unread']);

echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
?>