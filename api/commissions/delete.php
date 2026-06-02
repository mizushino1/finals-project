<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = intval($data['commission_id']);
$db           = getDB();

// Only admin or the user who made it can delete
if ($_SESSION['role'] === 'admin') {
    $stmt = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?');
    $stmt->execute([$commissionId]);
} elseif ($_SESSION['role'] === 'user') {
    $stmt = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
    $stmt->execute([$commissionId, $_SESSION['user_id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Commission deleted']);
?>