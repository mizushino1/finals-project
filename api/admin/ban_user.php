<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data   = json_decode(file_get_contents('php://input'), true);
$id     = intval($data['id']);
$type   = trim($data['type']); // 'user' or 'artist'
$db     = getDB();

if ($type === 'user') {
    $stmt = $db->prepare('UPDATE user_tbl SET account_status = ? WHERE account_id = ?');
} elseif ($type === 'artist') {
    $stmt = $db->prepare('UPDATE artist_tbl SET account_status = ? WHERE artist_id = ?');
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}

$stmt->execute(['banned', $id]);
echo json_encode(['success' => true, 'message' => 'Account banned']);
?>