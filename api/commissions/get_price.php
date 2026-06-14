<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['account_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$commissionId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID.']);
    exit;
}

try {
    $db   = getDB();
    $stmt = $db->prepare('SELECT price FROM commission_tbl WHERE commission_id = ? LIMIT 1');
    $stmt->execute([$commissionId]);
    $row  = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Commission not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'price' => (float) $row['price']]);

} catch (PDOException $e) {
    error_log('get_price PDO error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}