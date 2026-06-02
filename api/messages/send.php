<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// Only users can send messages (per your schema: sender = user_tbl, receiver = artist_tbl)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$receiverId = intval($data['receiver_id']);
$content    = trim($data['message_content']);
$db         = getDB();

if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
    exit;
}

$stmt = $db->prepare('
    INSERT INTO message_box (sender_id, receiver_id, message_content, sent_at, status)
    VALUES (?, ?, ?, NOW(), ?)
');
$stmt->execute([$_SESSION['user_id'], $receiverId, $content, 'unread']);

echo json_encode(['success' => true, 'message' => 'Message sent']);
?>