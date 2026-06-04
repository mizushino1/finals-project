<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

// 1. Strict Administrative Authentication Gate
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized administrative access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission reference ID.']);
    exit;
}

$db = getDB();

try {
    // 2. Begin Transaction to guarantee relational cleanup safety
    $db->beginTransaction();

    // Step A: Delete all associated artist proposals/bids for this commission first
    $stmtDeleteRequests = $db->prepare('DELETE FROM commission_request_tbl WHERE commission_id = ?');
    $stmtDeleteRequests->execute([$commissionId]);

    // Step B: Delete the parent commission listing itself
    $stmtDeleteCommission = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?');
    $stmtDeleteCommission->execute([$commissionId]);

    // All steps succeeded; permanently write changes to disk
    $db->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Commission listing and all associated artist proposals successfully moderation-purged.'
    ]);

} catch (PDOException $e) {
    // Rollback completely if any individual deletion fails to prevent partial data deletion
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    
    echo json_encode([
        'success' => false, 
        'message' => 'Administrative deletion failed: ' . $e->getMessage()
    ]);
}
?>