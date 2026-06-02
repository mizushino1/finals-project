<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'artist') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = intval($data['commission_id']);
$message      = trim($data['message'] ?? '');
$db           = getDB();
$artistId     = $_SESSION['user_id'];

// Check if artist already requested this commission
$stmt = $db->prepare('
    SELECT request_id FROM commission_request_tbl
    WHERE commission_id = ? AND artist_id = ?
');
$stmt->execute([$commissionId, $artistId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'You already requested this commission']);
    exit;
}

// Check commission is still open
$stmt = $db->prepare('SELECT status FROM commission_tbl WHERE commission_id = ?');
$stmt->execute([$commissionId]);
$commission = $stmt->fetch();

if (!$commission || $commission['status'] !== 'open') {
    echo json_encode(['success' => false, 'message' => 'Commission is no longer open']);
    exit;
}

$stmt = $db->prepare('
    INSERT INTO commission_request_tbl (commission_id, artist_id, message, status, requested_at)
    VALUES (?, ?, ?, "pending", ?)
');
$stmt->execute([$commissionId, $artistId, $message, date('Y-m-d')]);

echo json_encode(['success' => true, 'message' => 'Request sent, waiting for user confirmation']);

?>