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
$newStatus    = trim($data['status']);
$db           = getDB();
$role         = $_SESSION['role'];

// Allowed status transitions per role
$allowed = [
    'artist' => ['accepted', 'rejected', 'completed'],
    'user'   => ['cancelled'],
    'admin'  => ['pending', 'accepted', 'rejected', 'completed', 'cancelled'],
];

if (!in_array($newStatus, $allowed[$role] ?? [])) {
    echo json_encode(['success' => false, 'message' => 'Status not allowed for your role']);
    exit;
}

$stmt = $db->prepare('UPDATE commission_tbl SET status = ? WHERE commission_id = ?');
$stmt->execute([$newStatus, $commissionId]);

echo json_encode(['success' => true, 'message' => 'Commission status updated']);
?>