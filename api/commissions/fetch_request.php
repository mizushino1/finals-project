<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = intval($data['commission_id']);
$requestId    = intval($data['request_id']);
$db           = getDB();

// Verify this commission belongs to this user
$stmt = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
$stmt->execute([$commissionId, $_SESSION['user_id']]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Not your commission']);
    exit;
}

// Get the accepted artist
$stmt = $db->prepare('SELECT artist_id FROM commission_request_tbl WHERE request_id = ?');
$stmt->execute([$requestId]);
$accepted = $stmt->fetch();

if (!$accepted) {
    echo json_encode(['success' => false, 'message' => 'Request not found']);
    exit;
}

// Accept this request
$db->prepare('UPDATE commission_request_tbl SET status = ? WHERE request_id = ?')
   ->execute(['accepted', $requestId]);

// Reject all other requests for this commission
$db->prepare('
    UPDATE commission_request_tbl SET status = "rejected"
    WHERE commission_id = ? AND request_id != ?
')->execute([$commissionId, $requestId]);

// Assign artist to commission and close it
$db->prepare('
    UPDATE commission_tbl SET artist_id = ?, status = "in_progress"
    WHERE commission_id = ?
')->execute([$accepted['artist_id'], $commissionId]);

echo json_encode(['success' => true, 'message' => 'Artist accepted, commission is now in progress']);

?>