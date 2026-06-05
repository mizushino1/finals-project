<?php
require_once '../../config/session.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$data         = json_decode(file_get_contents('php://input'), true);
$commissionId = isset($data['commission_id']) ? intval($data['commission_id']) : 0;

if ($commissionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid commission ID.']);
    exit;
}

$db   = getDB();
$role = strtolower($_SESSION['role']);
$uid  = $_SESSION['user_id'];

try {
    if ($role === 'user') {
        // Verify ownership
        $stmtCheck = $db->prepare('SELECT commission_id FROM commission_tbl WHERE commission_id = ? AND user_id = ?');
        $stmtCheck->execute([$commissionId, $uid]);
        if (!$stmtCheck->fetch(PDO::FETCH_ASSOC)) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this commission.']);
            exit;
        }
    } elseif ($role !== 'admin') {
        // Artists and any other roles are blocked
        echo json_encode(['success' => false, 'message' => 'Unauthorized operation.']);
        exit;
    }

    $db->beginTransaction();

    // Step 1: Delete images linked to this commission
    $db->prepare('DELETE FROM image_tbl WHERE commission_id = ?')->execute([$commissionId]);

    // Step 2: Delete artist proposals/bids
    $db->prepare('DELETE FROM commission_request_tbl WHERE commission_id = ?')->execute([$commissionId]);

    // Step 3: Delete payments linked to transactions of this commission
    $db->prepare('
        DELETE p FROM payment_tbl p
        JOIN transaction_tbl t ON p.transaction_id = t.transaction_id
        WHERE t.commission_id = ?
    ')->execute([$commissionId]);

    // Step 4: Delete transactions
    $db->prepare('DELETE FROM transaction_tbl WHERE commission_id = ?')->execute([$commissionId]);

    // Step 5: Delete the commission itself
    $db->prepare('DELETE FROM commission_tbl WHERE commission_id = ?')->execute([$commissionId]);

    $db->commit();

    echo json_encode(['success' => true, 'message' => 'Commission and all associated records deleted successfully.']);
} catch (PDOException $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Deletion failed: ' . $e->getMessage()
    ]);
}
