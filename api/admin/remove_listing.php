<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = intval($data['commission_id']);
$db           = getDB();

$stmt = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?');
$stmt->execute([$commissionId]);

echo json_encode(['success' => true, 'message' => 'Listing removed']);

?>