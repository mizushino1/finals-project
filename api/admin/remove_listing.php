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

    // Step A: Delete payments tied to transactions of this commission
    $stmtDeletePayments = $db->prepare('
        DELETE p FROM payment_tbl p
        INNER JOIN transaction_tbl t ON p.transaction_id = t.transaction_id
        WHERE t.commission_id = ?
    ');
    $stmtDeletePayments->execute([$commissionId]);

    // Step B: Delete transactions tied to this commission
    $stmtDeleteTransactions = $db->prepare('DELETE FROM transaction_tbl WHERE commission_id = ?');
    $stmtDeleteTransactions->execute([$commissionId]);

    // Step C: Delete reviews tied to this commission
    $stmtDeleteReviews = $db->prepare('DELETE FROM review_tbl WHERE commission_id = ?');
    $stmtDeleteReviews->execute([$commissionId]);

    // Step D: Delete images referencing this commission (reference/commission images)
    $stmtDeleteImages = $db->prepare('DELETE FROM image_tbl WHERE commission_id = ?');
    $stmtDeleteImages->execute([$commissionId]);

    // Step E: Delete all associated artist proposals/bids for this commission
    $stmtDeleteRequests = $db->prepare('DELETE FROM commission_request_tbl WHERE commission_id = ?');
    $stmtDeleteRequests->execute([$commissionId]);

    // Step F: Delete the parent commission listing itself
    $stmtDeleteCommission = $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?');
    $stmtDeleteCommission->execute([$commissionId]);

    if ($stmtDeleteCommission->rowCount() === 0) {
        $db->rollBack();
        echo json_encode(['success' => false, 'message' => 'Commission listing not found.']);
        exit;
    }

    // All steps succeeded; permanently write changes to disk
    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Commission listing and all associated records successfully moderation-purged.'
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